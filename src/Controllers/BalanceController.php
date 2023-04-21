<?php

namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\Payment\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    protected $balance;
    
    public function __construct(BalanceService $balance)
    {
        $this->balance = $balance;
    }
    
    public function info(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
        $balances = $this->balance->getInfo($uid);
        if ($request->expectsJson())
        {
            $info = [];
            foreach ($balances as $bal)
            {
                $info[] = ['type' => $bal->balance_type, 'balance' => $bal->balance];
            }
            return ['error' => 0, 'message' => 'success', 'data' => ['balances' => $info]];
        }
        else
        {
            return view('hanoivip::balances-partial', ['balances' => $balances]);
        }
    }
    
    public function modHistory(Request $request)
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

}