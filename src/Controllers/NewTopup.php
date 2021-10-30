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
    
    public function listMethods(Request $request)
    {
        try
        {
            $methods = $this->service->getMethods();
            if ($request->ajax())
            {
                return ['error' => 0, 'message' => '', 'data' => $methods];
            }
            else
            {
                $order = $request->input('order');
                $next = $request->input('next');
                return view('hanoivip::new-topup-methods', 
                    ['methods' => $methods, 'order' => $order, 'next' => $next]);
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup list methods exception: " . $ex->getMessage());
            if ($request->ajax())
            {
                return ['error' => 99, 'message' => $ex->getMessage(), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['message' => $ex->getMessage()]);
            }
        }
    }
    
    public function choose(Request $request)
    {
        try 
        {
            $method = $request->input('method');
            $order = $request->input('order');
            $next = $request->input('next');
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
        catch (Exception $ex) 
        {
            Log::error("NewTopup start payment exception: " . $ex->getMessage());
            Log::error(">>>>>>>> " . $ex->getTraceAsString());
            if ($request->ajax())
            {
                return ['error' => 99, 'message' => $ex->getMessage(), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['message' => $ex->getMessage()]);
            }
        }
    }
    
    public function topup(Request $request)
    {
        $lock = Cache::lock('NewTopup::topup', 10);
        try
        {   
            if (!$lock->get())
            {
                if ($request->ajax())
                {
                    return ['error' => 98, 'message' => 'Do not click too fast', 'data' => []];
                }
                else
                {
                    return view('hanoivip::new-topup-failure', ['message' => 'Do not click too fast']);
                }
            }
            $params = $request->all();
            $result = $this->service->payment($params);
            $lock->release();
            if ($request->ajax())
            {
                return ['error' => 0, 'message' => '', 'data' => $result->toArray()];
            }
            else
            {
                return $result;
            }
        }
        catch (Exception $ex)
        {
            Log::error("NewTopup payment exception: " . $ex->getMessage());
            $lock->release();
            if ($request->ajax())
            {
                return ['error' => 99, 'message' => $ex->getMessage(), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['message' => $ex->getMessage()]);
            }
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
                return view('hanoivip::new-topup-result', ['data' => $result]);
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
                return view('hanoivip::new-topup-failure', ['message' => $ex->getMessage()]);
            }
        }
    }
    
    public function history(Request $request)
    {
        try
        {
            $page = $request->input('page');
            $result = $this->service->list($page);
            if ($request->ajax())
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
            if ($request->ajax())
            {
                return ['error' => 99, 'message' => $ex->getMessage(), 'data' => []];
            }
            else
            {
                return view('hanoivip::new-topup-failure', ['message' => $ex->getMessage()]);
            }
        }
    }
    
}