<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Payment\Services\BalanceService;
use Hanoivip\Payment\Services\StatisticService;

/**
 * 
 * @author hanoivip
 * Old version of payment
 */
class TopupController extends Controller
{   
    protected $balance;
    
    protected $stats;
    
    protected $topup;
    
    public function __construct(
        BalanceService $balance, 
        StatisticService $stats)
    {
        $this->balance = $balance;
        $this->stats = $stats;
    }
    
    public function globalRank()
    {
        return $this->stats->getGlobalStatistics();
    }
    
    public function rank($key)
    {
        return $this->stats->getStatistics($key, 0);
    }
    
    public function getRule(Request $request)
    {
        try 
        {
            if ($request->ajax())
                return ['html' => __('hanoivip.payment::topup.rule')];
            else
                return view('topup-rule', ['html' =>  __('hanoivip.payment::topup.rule')]);
        } catch (Exception $ex) {
            Log::error("Get topup rule error: " . $ex->getMessage());
            abort(500);
        }
    }
    
    public function getLang(Request $request)
    {
        try
        {
            if ($request->ajax())
            {
                return ['data' => __('hanoivip.payment::topup')];
            }
            abort(404);
        }
        catch (Exception $ex)
        {
            Log::error("Topup get lang error: " . $ex->getMessage());
            abort(500);
        }
    }
    
    public function jsTopup(Request $request)
    {
        $lang = __('hanoivip.payment::topup');
        return view('hanoivip::jtopup', ['lang' => json_encode($lang)]);
    }
    
    public function jsHistory(Request $request)
    {
        $lang = __('hanoivip.payment::topup');
        return view('hanoivip::jhistory', ['lang' => json_encode($lang)]);
    }
    
    public function jsRecharge(Request $request)
    {
        return view('hanoivip::jrecharge');
    }
    
}