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
use Hanoivip\Payment\Services\WebtopupRepository;
use Hanoivip\Payment\Facades\BalanceFacade;

/**
 *
 * @author hanoivip
 */
class AdminController extends Controller
{

    protected $logs;
    
    protected $stats;

    public function __construct(
        WebtopupRepository $logs,
        StatisticService $stats)
    {
        $this->logs = $logs;
        $this->stats = $stats;
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
    
    public function stats()
    {
        return view('hanoivip::admin.stat-income');
    }
}