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
 *
 */
class TopupController extends Controller
{   
    protected $balance;
    
    protected $stats;
    
    public function __construct(
        BalanceService $balance, 
        StatisticService $stats)
    {
        $this->balance = $balance;
        $this->stats = $stats;
    }
    /*
    public function topupHistory(Request $request)
    {
        $page = 1;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $history = $this->topup->getHistory($uid, $page);
        if ($request->ajax())
        {
            return ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]];
        }
        else
        {
            return view('hanoivip::topup-history', ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]]);
        }
    }*/
    
    public function rechargeHistory(Request $request)
    {
        $page = 1;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $mods = $this->balance->getHistory($uid, $page);
        if ($request->ajax())
        {
            return ['mods' => $mods[0], 'total_page' => $mods[1], 'current_page' => $mods[2]];
        }
        else
        {
            return view('hanoivip::recharge-history', ['mods' => $mods]);
        }
    }
    
    public function history(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
        $submits = $this->topup->getHistory($uid);
        $mods = $this->balance->getHistory($uid);
        if ($request->ajax())
        {
            return ['submits' => $submits[0], 'mods' => $mods[0]];
        }
        else 
        {
            return view('hanoivip::history', ['submits' => $submits[0], 'mods' => $mods[0]]);
        }
    }
    
    public function globalRank()
    {
        return $this->stats->getGlobalStatistics();
    }
    
    public function rank($key)
    {
        return $this->stats->getStatistics($key, 0);
    }
    
    public function query(Request $request)
    {
        try
        {
            
        }
        catch (Exception $ex)
        {
            Log::error("Query transaction error: " . $ex->getMessage());
            abort(500);
        }
    }
    
    public function getRule(Request $request)
    {
        try 
        {
            if ($request->ajax())
                return ['html' => __('hanoivip::topup.rule')];
            else
                return view('topup-rule', ['html' =>  __('hanoivip::topup.rule')]);
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
                return ['data' => __('hanoivip::topup')];
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
        $lang = __('hanoivip::topup');
        return view('hanoivip::jtopup', ['lang' => json_encode($lang)]);
    }
    
    public function jsHistory(Request $request)
    {
        $lang = __('hanoivip::topup');
        return view('hanoivip::jhistory', ['lang' => json_encode($lang)]);
    }
    
    public function jsRecharge(Request $request)
    {
        return view('hanoivip::jrecharge');
    }
    
    public function onTopupSuccess(Request $request)
    {
        $message = 'Topup Success!';
        if ($request->has('message'))
            $message = $request->input('message');
        return view('hanoivip::topup-success', ['message' => $message]);
    }
}