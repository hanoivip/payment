<?php

namespace Hanoivip\Payment\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Exception;
use Hanoivip\PaymentMethodContract\IPaymentMethod;
use Hanoivip\PaymentMethodContract\IPaymentResult;
use Hanoivip\Events\Payment\TransactionUpdated;
use Hanoivip\PaymentMethodContract\IPaymentSession;
use Illuminate\Contracts\View\View;

class NewTopupService
{   
    private $transactions;
    
    public function __construct(
        TransactionService $transactions)
    {
        $this->transactions = $transactions;
    }
    public function getConfig($id)
    {
        $all = config('payment.methods', []);
        if (isset($all[$id]) && isset($all[$id]['setting']))
        {
            return $all[$id]['setting'];
        }
    }
    /**
     * Dựa vào cấu hình, kiểm tra qua & trả về các phương pháp nạp có thể sử dụng
     * Phương pháp nào lỗi hệ thống => không hiển thị
     * Phương pháp nào đang bị khóa => hiển thị mờ
     * @throws Exception
     * @return \stdClass[]
     */
    public function getMethods($client = null)
    {
        $all = config('payment.methods', []);
        $methods = [];
        foreach ($all as $code => $cfg)
        {
            try
            {
                if (empty($cfg['enable'])) 
                    continue;
                $service = $cfg['service'];
                // check for implmenetation
                app()->make($service);
                $cfgVo = new \stdClass();
                $cfgVo->code = $code;
                $cfgVo->name = $cfg['name'];
                $cfgVo->service = $service;
                $cfgVo->guidelines = __("hanoivip.payment::payment.$service.guidelines");
                $cfgVo->url = __("hanoivip.payment::payment.$service.url");
                $methods[$code] = $cfgVo;
            }
            catch (Exception $ex)
            {
                Log::error("NewTopup method " . $cfg['name'] . " missing implementation? " . $cfg['service']);
                continue;
            }
        }
        // filter by client
        if (!empty($client))
        {
            $ret = [];
            $methodsByClient = config("payment.$client", []);
            if (empty($methodsByClient))
            {
                Log::error("Payment methods by client is empty?");
            }
            else 
            {
                foreach ($methodsByClient as $method)
                {
                    if (!isset($methods[$method]))
                    {
                        Log::error("Payment missing $method config?");
                        continue;
                    }
                    $ret[$method] = $methods[$method];
                }
            }
            return $ret;
        }
        return $methods;
    }
    /**
     * 
     * @param string $method
     * @return IPaymentMethod
     */
    protected function getMethodImplement($method)
    {
        $all = config('payment.methods', []);
        if (isset($all[$method]) && $all[$method]['enable'])
        {
            $service = $all[$method]['service'];
            $clazz = app()->make($service);
            /** @var IPaymentMethod $clazz */
            if ($all[$method]['need_config'])
            {
                $clazz->config($all[$method]['setting']);
            }
            return $clazz;
        }
    }
    /**
     * Khởi tạo phương thức
     * Khởi tạo giao dịch
     * Kích hoạt phương thức nạp
     * Khởi tạo giao dịch
     * - Lưu các tham số của trans, nếu có
     * @param string $order
     * @param string $method
     * @return IPaymentSession
     * @throws Exception
     */
    public function preparePayment($order, $method, $next = "")
    {
        $current = $this->transactions->getByOrder($order);
        if (!empty($current))
        {
            throw new Exception(__('hanoivip.payment::order-exists'));
        }
        $service = $this->getMethodImplement($method);
        /** @var IPaymentMethod $service */
        $record = $this->transactions->newTrans();
        $record->order = $order;
        $record->method = $method;
        $record->next = $next;
        $session = $service->beginTrans($record);
        /** @var IPaymentSession $session */
        $record->session = json_encode($session->getSecureData());
        $record->save();
        return $session;
    }
    /**
     * Người dùng nhập các thông tin cần thiết
     * - Kiểm tra trans có tham số không
     * Gửi tới phương thức nạp
     * Tạo hóa đơn
     * 
     * TODO: chuan hoa lai ket qua tra ve
     * 
     * @param array $params
     * @return IPaymentResult|View
     * @throws Exception
     */
    public function payment($params)
    {
        if (!isset($params['trans']))
        {
            throw new Exception('NewTopup method input must include <trans> value');
        }
        $transId = $params['trans'];
        $record = $this->transactions->get($transId);
        $secureParams = json_decode($record->session, true);
        if (!empty($secureParams))
        {
            $params = array_merge($params, $secureParams);
        }
        $service = $this->getMethodImplement($record->method);
        $validate = $service->validate($params);
        if (gettype($validate) == 'string')
        {
            Log::error("Payment validation fail:" . $validate);
            if (request()->expectsJson())
            {
                return ['error' => $validate];
            }
            else 
            {
                return back()->withInput()->withErrors(['error' => $validate]);
            }
        }
        if (gettype($validate) == 'array' && !empty($validate))
        {
            Log::error("Payment validation fails");
            if (request()->expectsJson())
            {
                return ['error' => $validate];
            }
            else 
            {
                return back()->withInput()->withErrors($validate);
            }
        }
        if ($validate === false)
        {
            Log::error("Payment validation fail in general");
            if (request()->expectsJson())
            {
                return ['error' => __('hanoivip.payment::payment.validate-errors')];
            }
            else
            {
                return back()->withInput()->withErrors(['error' => __('hanoivip.payment::payment.validate-errors')]);
            }
        }
        /** @var IPaymentMethod $service */
        $result = $service->request($record, $params);
        $service->endTrans($transId);
        $this->transactions->saveResult($record, $result);
        $next = $record->next;
        
        $userId = Auth::user()->getAuthIdentifier();
        if (!empty($next))
        {
            if (strpos($next, 'http') === false)
            {
                if (Route::has($next))
                {
                    Log::debug("NewTopup redirect to route.." . $next);
                    return response()->redirectToRoute($next, ['order' => $record->order, 'receipt' => $transId]);
                }
                else 
                {
                    try
                    {
                        $clazz = app()->make($next);
                        if (!empty($clazz))
                        {
                            Log::debug("NewTopupService payment done on $next class");
                            /** @var IPaymentDone $clazz */
                            return $clazz->onTopupDone($userId, $transId, $result);
                        }
                    }
                    catch (Exception $ex)
                    {
                        Log::error("NewTopup resolve clazz $next error " . $ex->getMessage());
                        Log::warn("NewTopup trans $transId method $record->method missing finalize process? Use default.");
                        return $this->onTopupDone($userId, $transId, $result);
                    }
                }
            }
            else
            {
                Log::debug("NewTopupService redirect to external url.." . $next);
                return response()->redirectTo($next)->withHeaders(['order' => $record->order, 'receipt' => $transId]);
            }
        }
        else 
        {
            Log::debug("NewTopup trans $transId method $record->method missing finalize process? Use default.");
            $clazz = app()->make('PaymentToCredit');
            return $clazz->onTopupDone($userId, $transId, $result);
        }
    }
    /**
     * Khởi tạo phương thức nạp
     * Gửi yêu cầu tới phương thức nạp
     * @param string $transId Transaction ID = Purchase Token
     * @param boolean $force Bắt buộc kiểm tra từ kênh nạp
     * @return IPaymentResult
     * @throws Exception
     */
    public function query($transId, $force = false)
    {
        $record = $this->transactions->get($transId);
        /** @var IPaymentResult $result */
        $result = new SavedPaymentResult($transId, $record->result);
        if ($result->isPending() || $force)
        {
            $method = $record->method;
            $service = $this->getMethodImplement($method);
            /** @var IPaymentMethod $service */
            $result = $service->query($record, $force);
            $this->transactions->saveResult($record, $result);
        }
        return $result;
    }
    public function queryByOrder($order, $force = false)
    {
        $record = $this->transactions->getByOrder($order);
        if (!empty($record))
        {
            return $this->query($record->trans_id, $force);
        }
        return false;
    }
    /**
     * 1 giao dịch có thông báo mới cần cập nhật kết quả
     * @param string $trans
     */
    public function callback($trans)
    {
        return $this->query($trans);
    }
    /**
     * 
     * @param TransactionUpdated $event
     * @return \Hanoivip\PaymentMethodContract\IPaymentResult
     */
    public function handle(TransactionUpdated $event)
    {
        return $this->query($event->transId);
    }
    /**
     * Trigger web payment flow
     * @param string $order
     * @param string $client
     */
    public function pay($order, $next = null, $client = null)
    {
        return response()->redirectToRoute('newtopup', ['order' => $order, 'next' => $next, 'client' => $client]);
    }
    /**
     * View transacions history
     * @param number $page
     * @param number $count
     * @return IPaymentResult[]
     */
    public function list($page = 0, $count = 10)
    {
        
    }
    
    public function get($transId)
    {
        return $this->transactions->get($transId);
    }
    
    public function pendingPage($transId)
    {
        $record = $this->transactions->get($transId);
        $service = $this->getMethodImplement($record->method);
        return $service->openPendingPage($record);
    }
    
    public function paymentPage($transId, $guide, $session)
    {
        $record = $this->transactions->get($transId);
        $service = $this->getMethodImplement($record->method);
        return $service->openPaymentPage($transId, $guide, $session);
    }
}