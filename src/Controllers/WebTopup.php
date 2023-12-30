<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\Payment\Services\NewTopupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Hanoivip\Payment\Services\WebtopupRepository;
use Hanoivip\Payment\Services\BalanceService;

/**
 *
 * Web topup with prepaid card
 * - Target to web balance
 * - Quick flow
 * @author hanoivip
 * 
 * TODO: move to another package
 *
 */
class WebTopup extends Controller
{   
    private $service;
    
    private $logs;
    
    private $balances;
    
    public function __construct(
        NewTopupService $service,
        WebtopupRepository $logs,
        BalanceService $balances)
    {
        $this->service = $service;
        $this->logs = $logs;
        $this->balances = $balances;
    }
    /**
     * Nếu có nhiều hơn 1 phương pháp nạp thì cho chọn
     * Nếu không, chuyển thẳng phương pháp nạp 
     */
    public function index(Request $request)
    {
        $methods = config('payment.webtopup.methods', []);
        if (empty($methods))
        {
            return view('hanoivip::webtopup-failure', ['message' => __('hanoivip.payment::webtopup.no-method')]);
        }
        else if (count($methods) > 1)
        {
            return view('hanoivip::webtopup-methods');
        }
        else
        {
            $userId = Auth::user()->getAuthIdentifier();
            $order = "WebTopup@" . Str::random(6);
            $method = $methods[0];
            try
            {
                $result = $this->service->preparePayment($order, $method);
                if ($this->logs->saveLog($userId, $result->getTransId()))
                {
                    if ($request->ajax())
                    {
                        return ['error' => 0, 'message' => '',
                            'data' => ['trans' => $result->getTransId(), 'guide' => $result->getGuide(), 'data' => $result->getData()]];
                    }
                    else
                    {
                        return view('hanoivip::new-topup-method-' . $method,
                            ['trans' => $result->getTransId(), 'guide' => $result->getGuide(), 'data' => $result->getData()]);
                    }
                }
                else
                {
                    return view('hanoivip::webtopup-failure', ['message' => __('hanoivip.payment::webtopup.log-fail')]);
                }
            }
            catch (Exception $ex)
            {
                Log::error("Webtopup index exception:" + $ex->getMessage());
                //report($ex);
                return view('hanoivip::webtopup-failure', ['message' => __('hanoivip.payment::webtopup.exception')]);
            }
        }
    }
    
    public function method(Request $request)
    {
        throw new Exception("Webtopup implement method method...");
    }
    
    public function history(Request $request)
    {
        try
        {
            $userId = Auth::user()->getAuthIdentifier();
            $submits = $this->logs->list($userId);
            $mods = $this->balances->getHistory($userId);
            return view('hanoivip::webtopup-history', 
                ['submits' => $submits[0], 'total_submits' => $submits[1],
                 'mods' => $mods[0], 'total_mods' => $mods[1]]);
        }
        catch (Exception $ex)
        {
            Log::error("Webtopup history exception " . $ex->getMessage());
            
        }
    }
    
    public function query(Request $request)
    {
        try
        {
            $trans = $request->input('trans');
            $result = $this->service->query($trans);
            if ($request->ajax())
            {
                return ['error' => 0, 'message' => '', 'data' => $result->toArray()];
            }
            else
            {
                return view('hanoivip::webtopup-result', ['data' => $result, 'trans' => $trans]);
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup query exception: " . $ex->getMessage());
            if ($request->ajax())
            {
                return ['error' => 99, 'message' => $ex->getMessage(), 'data' => []];
            }
            else
            {
                return view('hanoivip::webtopup-failure', ['message' => $ex->getMessage()]);
            }
        }
    }
    /**
     * App quick topup
     * - No next route
     */ 
    public function quickTopup(Request $request)
    {
        $methods = config('payment.webtopup.methods', []);
        if (empty($methods))
        {
            return ['error' => 1, 'message' => '', 'data' => []];
        }
        else
        {
            $userId = Auth::user()->getAuthIdentifier();
            $order = "AppTopup@" . Str::random(6);
            $method = $methods[0];
            try
            {
                $result = $this->service->preparePayment($order, $method, '');
                if ($this->logs->saveLog($userId, $result->getTransId()))
                {
                    return ['error' => 0, 'message' => '',
                        'data' => ['trans' => $result->getTransId(), 'guide' => $result->getGuide(), 'data' => $result->getData()]];
                }
                else
                {
                    return ['error' => 2, 'message' => '', 'data' => []];
                }
            }
            catch (Exception $ex)
            {
                Log::error("Webtopup index exception:" + $ex->getMessage());
                return ['error' => 3, 'message' => '', 'data' => []];
            }
        }
    }
	/**
	 * pay with credit, at once
	 * QuickPaymentScreen: make order & request quick payemnt
	 * @deprecated
	 * @param Request $request
	 * @return number[]|string[]|NULL[][]|number[]|string[]|array[]
	 */
    public function quickRecharge(Request $request)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $order = $request->input('order');
        //?fuck
        $order = str_replace("\"", "", $order);
        //$item = $request->input('item');
        $method = 'credit';
        try
        {
            $result = $this->service->preparePayment($order, $method, '');
            if ($this->logs->saveLog($userId, $result->getTransId()))
            {
                $payResult = $this->service->payment(['trans' => $result->getTransId()]);
                return ['error' => 0, 'message' => '', 'data' => [ 
                    'trans' => $payResult->getTransId(),
                    'detail' => $payResult->getDetail(),
                    'isFailure' => $payResult->isFailure(),
                    'isPending' => $payResult->isPending(),
                    'isSuccess' => $payResult->isSuccess()
                ]];
            }
            else
            {
                return ['error' => 2, 'message' => '', 'data' => []];
            }
        }
        catch (Exception $ex)
        {
            Log::error("Webtopup index exception:" + $ex->getMessage());
            return ['error' => 3, 'message' => '', 'data' => []];
        }
    }
}