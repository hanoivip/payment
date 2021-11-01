<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\Payment\Services\NewTopupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Cache;
use Hanoivip\Payment\Jobs\CheckPendingReceipt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Hanoivip\Payment\Facades\BalanceFacade;

/**
 *
 * Web topup with prepaid card
 * @author hanoivip
 *
 */
class WebTopup extends Controller
{
    private $service;
    
    public function __construct(NewTopupService $service)
    {
        $this->service = $service;
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
            return view('hanoivip::webtopup-failure');
        }
        else if (count($methods) > 1)
        {
            return view('hanoivip::webtopup-methods');
        }
        else
        {
            $order = 'WebTopup@' . Str::random(6);
            $method = $methods[0];
            $next = 'webtopup.done';
            $result = $this->service->preparePayment($order, $method, $next);
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
    }
    
    public function method(Request $request)
    {
        
    }
    
    public function topupDone(Request $request)
    {
        //$order = $request->input('order');
        $receipt = $request->input('receipt');
        try 
        {
            $result = $this->service->query($receipt);
            if (gettype($result) == 'string')
            {
                if ($request->ajax())
                {
                    return ['error' => 1, 'message' => $result, 'data' => []];
                }
                else
                {
                    return view('hanoivip::webtopup-failure', ['message' => $result]);
                }
            }
            else 
            {
                if ($result->isPending())
                {
                    dispatch(new CheckPendingReceipt(Auth::user()->getAuthIdentifier(), $receipt))->delay(60);
                    if ($request->ajax())
                    {
                        return ['error' => 0, 'message' => 'pending', 'data' => ['trans' => $receipt]];
                    }
                    else
                    {
                        return view('hanoivip::webtopup-pending', ['trans' => $receipt]);
                    }
                }
                else if ($result->isFailure()) 
                {
                    if ($request->ajax())
                    {
                        return ['error' => 2, 'message' => $result->getDetail(), 'data' => []];
                    }
                    else
                    {
                        return view('hanoivip::webtopup-failure', ['message' => $result->getDetail()]);
                    }
                }
                else 
                {
                    BalanceFacade::add(Auth::user()->getAuthIdentifier(), $result->getAmount(), "WebTopup:" . $receipt);
                    if ($request->ajax())
                    {
                        return ['error' => 0, 'message' => 'success', 'data' => []];
                    }
                    else
                    {
                        return view('hanoivip::webtopup-success');
                    }
                }
            }
        } 
        catch (Exception $ex) 
        {
            Log::error("WebTopup payment callback exception:" . $ex->getMessage());
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
    
    public function history(Request $request)
    {
        
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
                return view('hanoivip::webtopup-result', ['data' => $result]);
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