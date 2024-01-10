<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\Payment\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * History for webclient
 * @author hanoivip
 */
class HistoryController extends Controller
{
    private $service;
    
    private $logs;
    
    private $balances;
    
    public function __construct(
        BalanceService $balances)
    {
        $this->balances = $balances;
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
    
    public function topupHistory(Request $request)
    {
        $page = 0;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $submits = $this->logs->list($uid, $page);
        return view('hanoivip::webtopup-history-submits', ['submits' => $submits[0], 'total_submits' => $submits[1], 'current_page' => $submits[2]]);
    }
    
    public function rechargeHistory(Request $request)
    {
        $page = 0;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $submits = $this->balances->getHistory($uid, $page);
        return view('hanoivip::webtopup-history-mods', ['mods' => $submits[0], 'total_mods' => $submits[1], 'current_page' => $submits[2]]);
    }
    
}