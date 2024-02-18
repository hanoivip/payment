<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\Payment\Services\NewTopupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 *
 * @author hanoivip
 *
 */
class NewTopup extends Controller
{
    private $service;
    
    public function __construct(NewTopupService $service)
    {
        $this->service = $service;
    }
    
    public function methods(Request $request)
    {
        $client = null;
        if ($request->has('client'))
        {
            $client = $request->input('client');
        }
        $methods = $this->service->getMethods($client);
        return ['error' => 0, 'message' => '', 'data' => $methods];
    }
    
    public function start(Request $request)
    {
        $order = $request->input('order');
        $next = $request->input('next');
        $client = null;
        if ($request->has('client'))
        {
            $client = $request->input('client');
        }
        $check = $this->service->queryByOrder($order);
        if ($check !== false)
        {
            if ($check->isPending())
            {
                return $this->service->pendingPage($check->getTransId());
            }
            else if ($check->isSuccess())
            {
                return view('hanoivip::new-topup-done-success');
            }
            else
            {
                return view('hanoivip::new-topup-done-failure');
            }
        }
        try
        {
            $methods = $this->service->getMethods($client);
            if (empty($methods))
            {
                return view('hanoivip::new-topup-empty-methods');
            }
            else if (count($methods) == 1)
            {
                // just forward with default method
                $method = array_keys($methods)[0];
                $result = $this->service->preparePayment($order, $method, $next);
                if ($request->expectsJson())
                {
                    return ['error' => 0, 'message' => '',
                        'data' => ['trans' => $result->getTransId(), 'guide' => $result->getGuide(), 'data' => $result->getData()]];
                }
                else
                {
                    return $this->service->paymentPage($result->getTransId(), $result->getGuide(), $result->getData());
                }
            }
            else
            {
                if ($request->expectsJson())
                {
                    return ['error' => 0, 'message' => '', 'data' => $methods];
                }
                else
                {
                    return view('hanoivip::new-topup-methods',
                        ['methods' => $methods, 'order' => $order, 'next' => $next]);
                }
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup list methods exception: " . $ex->getMessage());
            if ($request->expectsJson())
            {
                return ['error' => 99, 'message' => __('hanoivip.payment::newtopup.methods.error'), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['error_message' => __('hanoivip.payment::newtopup.methods.error')]);
            }
        }
    }
    
    public function choose(Request $request)
    {
        try 
        {
            $method = 'credit';
            if ($request->has('method'))
                $method = $request->input('method');
            $order = $request->input('order');
            $next = $request->input('next');
            $result = $this->service->preparePayment($order, $method, $next);
            if ($request->expectsJson())
            {
                return ['error' => 0, 'message' => '', 
                    'data' => ['trans' => $result->getTransId(), 'guide' => $result->getGuide(), 'data' => $result->getData()]];
            }
            else
            {
                return $this->service->paymentPage($result->getTransId(), $result->getGuide(), $result->getData());
            }
        } 
        catch (Exception $ex) 
        {
            Log::error("NewTopup start payment exception: " . $ex->getMessage());
            report($ex);
            if ($request->expectsJson())
            {
                return ['error' => 99, 'message' => __('hanoivip.payment::newtopup.choose.error'), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['error_message' => __('hanoivip.payment::newtopup.choose.error')]);
            }
        }
    }
    /**
     * TODO: thiet ke lai payment, chuyen cac kieu result ra ngoai controller
     */
    public function topup(Request $request)
    {
        try
        {
            $params = $request->all();
            $result = $this->service->payment($params);
            if ($request->expectsJson())
            {
                //bad
                return $result;
            }
            else
            {
                //bad
                return $result;
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup payment exception: " . $ex->getMessage());
            report($ex);
            if ($request->expectsJson())
            {
                return ['error' => 99, 'message' => __('hanoivip.payment::newtopup.topup.error')];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['error_message' => __('hanoivip.payment::newtopup.topup.error')]);
            }
        }
    }
    
    public function query(Request $request)
    {
        try
        {
            $trans = $request->input('trans');
            $result = $this->service->query($trans);
            if ($request->expectsJson())
            {
                return ['error' => 0, 'message' => '', 'data' => $result->toArray()];
            }
            else
            {
                return view('hanoivip::new-topup-result', ['data' => $result]);
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup query exception: " . $ex->getMessage());
            if ($request->expectsJson())
            {
                return ['error' => 99, 'message' => __('hanoivip.payment::newtopup.query.error'), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['error_message' => __('hanoivip.payment::newtopup.query.error')]);
            }
        }
    }
    
    public function history(Request $request)
    {
        try
        {
            $page = $request->input('page');
            $result = $this->service->list($page);
            if ($request->expectsJson())
            {
                return ['error' => 0, 'message' => '', 'data' => $result->toArray()];
            }
            else
            {
                return view('hanoivip::new-topup-history', ['data' => $result]);
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup history exception: " . $ex->getMessage());
            if ($request->expectsJson())
            {
                return ['error' => 99, 'message' => __('hanoivip.payment::newtopup.history.error'), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['error_message' => __('hanoivip.payment::newtopup.history.error')]);
            }
        }
    }
    
}