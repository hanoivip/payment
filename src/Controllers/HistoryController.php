<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Payment\Services\BalanceService;
use Hanoivip\Payment\Services\StatisticService;
use Hanoivip\Payment\Services\NewTopupService;

/**
 *
 * @author hanoivip
 */
class HistoryController extends Controller
{
    protected $balance;
    
    protected $stats;
    
    protected $topup;
    
    public function __construct(
        BalanceService $balance,
        NewTopupService $topup)
    {
        $this->balance = $balance;
        $this->stats = $stats;
        $this->topup = $topup;
    }
    
    public function topupHistory(Request $request)
    {
        $page = 1;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $history = $this->topup->list($uid, $page);
        if ($request->ajax())
        {
            return ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]];
        }
        else
        {
            return view('hanoivip::topup-history', ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]]);
        }
    }
    
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
    
    public function jsTopup(Request $request)
    {
        $lang = __('hanoivip::topup');
        return view('hanoivip::jtopup', ['lang' => json_encode($lang)]);
    }
    
}