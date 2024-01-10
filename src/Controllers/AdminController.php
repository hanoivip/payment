<?php
namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Hanoivip\Payment\Services\NewTopupService;
use Hanoivip\Payment\Services\StatisticService;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\Payment\Services\BalanceRequest;

/**
 *
 * @author hanoivip
 */
class AdminController extends Controller
{   
    protected $stats;
    
    protected $service;
    
    protected $request;

    public function __construct(
        StatisticService $stats,
        NewTopupService $service,
        BalanceRequest $request)
    {
        $this->stats = $stats;
        $this->service = $service;
        $this->request = $request;
    }

    public function webtopupHistory(Request $request)
    {
        $page = 0;
        if ($request->has('page'))
            $page = $request->input('page');
        $tid = $request->input('tid');
        $history = $this->logs->list($tid, $page);
        return view('hanoivip::admin.webtopup-history', [
            'submits' => $history[0],
            'total_page' => $history[1],
            'current_page' => $history[2],
            'tid' => $tid
        ]);
    }

    public function balanceHistory(Request $request)
    {
        $page = 0;
        if ($request->has('page'))
            $page = $request->input('page');
        $tid = $request->input('tid');
        $mods = BalanceFacade::getHistory($tid, $page);
        if ($request->ajax()) {
            return [
                'mods' => $mods[0],
                'total_page' => $mods[1],
                'current_page' => $mods[2]
            ];
        } else {
            return view('hanoivip::admin.balance-history', [
                'mods' => $mods[0],
                'total_page' => $mods[1],
                'current_page' => $mods[2],
                'tid' => $tid
            ]);
        }
    }
    
    public function today()
    {
        $key = "today_" . date('Ymd', time());
        $stats = $this->stats->getStatistics($key);
        $sum = 0;
        if ($stats->isNotEmpty())
            $sum = $stats->first()->total;
        return view('hanoivip::admin.income-result', ['sum' => $sum]);
    }
    
    public function thisMonth()
    {
        $curMonth = date('Ym', time());
        $key = "income_" . $curMonth;
        $stats = $this->stats->getStatistics($key);
        $sum = 0;
        if ($stats->isNotEmpty())
            $sum = $stats->first()->total;
        return view('hanoivip::admin.income-result', ['sum' => $sum]);
    }
    
    public function thisWeek()
    {
        $curWeek = date('W', time());
        $key = "income_week_" . $curWeek;
        $stats = $this->stats->getStatistics($key);
        $sum = 0;
        if ($stats->isNotEmpty())
            $sum = $stats->first()->total;
        return view('hanoivip::admin.income-result', ['sum' => $sum]);
    }
    
    public function byMonth(Request $request)
    {
        $sum = 0;
        if ($request->getMethod() == 'POST')
        {
            $month = $request->input('month');
            $key = "income_" . $month;
            $stats = $this->stats->getStatistics($key);
            if ($stats->isNotEmpty())
                $sum = $stats->first()->total;
        }
        return view('hanoivip::admin.stat-by-month', ['sum' => $sum]);
    }
    
    public function stats()
    {
        $data = $this->stats->getLastDays(7);
        return view('hanoivip::admin.stat-income', ['data' => $data]);
    }
    
    public function statsMonth()
    {
        $data = $this->stats->getLastMonths(3);
        return view('hanoivip::admin.stat-income', ['data' => $data]);
    }
    
    public function balanceRequest(Request $request)
    {
        $message = "";
        $error = "";
        $targetId = $request->input('tid');
        if ($request->getMethod() == 'POST')
        {
            try
            {
                $gmId = Auth::user()->getAuthIdentifier();
                $amount = $request->input('amount');
                $reason = $request->input('reason');
                $this->request->request($gmId, $targetId, $reason, $amount);
                $message = "get request, wait for approval";
            }
            catch (Exception $ex)
            {
                $error = $ex->getMessage();
            }
        }
        return view('hanoivip::admin.balance-request', ['message' => $message, 'error_message' => $error, 'tid' => $targetId]);
    }
}