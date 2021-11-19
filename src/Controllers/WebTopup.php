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
use Hanoivip\Payment\Services\WebtopupRepository;

/**
 *
 * Web topup with prepaid card
 * @author hanoivip
 *
 */
class WebTopup extends Controller
{
    private $service;
    
    private $logs;
    
    public function __construct(
        NewTopupService $service,
        WebtopupRepository $logs)
    {
        $this->service = $service;
        $this->logs = $logs;
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
            return view('hanoivip::webtopup-failure', ['message' => __('hanoivip::webtopup.no-method')]);
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
            $this->logs->saveLog(Auth::user()->getAuthIdentifier(), $result->getTransId());
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
    /**
     * Return for jhistory UI
     * @param Request $request
     */
    public function topupHistory(Request $request)
    {
        try
        {
            Log::error("Webtopup .......");
            $userId = Auth::user()->getAuthIdentifier();
            $page = 0;
            if ($request->has('page'))
                $page = $request->input('page');
            $history = $this->logs->list($userId, $page);
            if ($request->ajax())
            {
                return ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]];
            }
            else
            {
                return view('hanoivip::topup-history', ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]]);
            }
        }
        catch (Exception $ex)
        {
            Log::error("Webtopup history exception " . $ex->getMessage());
            if ($request->ajax())
            {
                return ['submits' => [], 'total_page' => 0, 'current_page' => 0];
            }
            else
            {
                return view('hanoivip::topup-history', ['submits' => [], 'total_page' => 0, 'current_page' => 0]);
            }
        }
    }
    
    public function history(Request $request)
    {
        try
        {
            $userId = Auth::user()->getAuthIdentifier();
            $page = 0;
            if ($request->has('page'))
                $page = $request->input('page');
            $logs = $this->logs->list($userId, $page);
            
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
}