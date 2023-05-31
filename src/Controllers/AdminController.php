<?php
namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Payment\Services\NewTopupService;
use Hanoivip\Payment\Services\StatisticService;
use Hanoivip\Payment\Jobs\CheckPendingReceipt;
use Hanoivip\Payment\Services\WebtopupRepository;
use Hanoivip\Payment\Facades\BalanceFacade;
use Hanoivip\Events\Gate\UserTopup;
use Hanoivip\Payment\Models\WebtopupLogs;
use Hanoivip\Payment\Services\BalanceRequest;

/**
 *
 * @author hanoivip
 */
class AdminController extends Controller
{

    protected $logs;
    
    protected $stats;
    
    protected $service;
    
    protected $request;

    public function __construct(
        WebtopupRepository $logs,
        StatisticService $stats,
        NewTopupService $service,
        BalanceRequest $request)
    {
        $this->logs = $logs;
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
    
    public function retry(Request $request)
    {
        $receipt = $request->input('receipt');
        $log = WebtopupLogs::where('trans_id', $receipt)->first();
        if (empty($log))
        {
            return view('hanoivip::admin.webtopup-retry-result', ['message' => 'Receipt not found']);
        }
        //if (!empty($log->callback))
        //{
        //    return view('hanoivip::admin.webtopup-retry-result', ['message' => 'Receipt was done']);
        //}
        $tid = $log->user_id;
        $log->callback = true;
        $log->by_admin = true;
        $log->save();
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
                    return view('hanoivip::admin.webtopup-retry-result', ['message' => $result]);
                }
            }
            else
            {
                if ($result->isPending())
                {
                    dispatch(new CheckPendingReceipt($tid, $receipt))->delay(60);
                    if ($request->ajax())
                    {
                        return ['error' => 0, 'message' => 'pending', 'data' => ['trans' => $receipt]];
                    }
                    else
                    {
                        return view('hanoivip::admin.webtopup-retry-result', ['message' => "OK. Thẻ trễ, đợi.."]);
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
                        return view('hanoivip::admin.webtopup-retry-result', ['message' => 'Err:' . $result->getDetail()]);
                    }
                }
                else
                {
                    event(new UserTopup($tid, 0, $result->getAmount(), $receipt));
                    BalanceFacade::add($tid, $result->getAmount(), "WebTopup:" . $receipt);
                    if ($request->ajax())
                    {
                        return ['error' => 0, 'message' => 'success', 'data' => []];
                    }
                    else
                    {
                        return view('hanoivip::admin.webtopup-retry-result', ['message' => "Thành công."]);
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            Log::error("WebTopup  callback exception:" . $ex->getMessage());
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
    
    public function check(Request $request)
    {
        $receipt = $request->input('receipt');
        $log = WebtopupLogs::where('trans_id', $receipt)->first();
        if (empty($log))
        {
            return view('hanoivip::admin.webtopup-retry-result', ['message' => 'Receipt not found']);
        }
        $tid = $log->user_id;
        $log->callback = true;
        $log->by_admin = true;
        $log->save();
        try
        {
            $resultCache = $this->service->query($receipt);
            if (gettype($resultCache) == 'string')
            {
                return view('hanoivip::admin.webtopup-retry-result', ['message' => $resultCache]);
            }
            else
            {
                if ($resultCache->isPending() || $resultCache->isSuccess())
                {
                    return view('hanoivip::admin.webtopup-retry-result', ['message' => 'No thing to do']);
                }
                else 
                {
                    $resultForce = $this->service->query($receipt, true);
                    if (gettype($resultForce) == 'string')
                    {
                        return view('hanoivip::admin.webtopup-retry-result', ['message' => $resultForce]);
                    }
                    else 
                    {
                        if ($resultForce->isFailure())
                        {
                            return view('hanoivip::admin.webtopup-retry-result', ['message' => $resultForce->getDetail()]);
                        }
                        else if ($resultForce->isPending())
                        {
                            dispatch(new CheckPendingReceipt($tid, $receipt))->delay(60);
                            return view('hanoivip::admin.webtopup-retry-result', ['message' => "OK. Still pending. Wait more."]);
                        }
                        else
                        {
                            event(new UserTopup($tid, 0, $resultForce->getAmount(), $receipt));
                            BalanceFacade::add($tid, $resultForce->getAmount(), "WebTopup:" . $receipt);
                            return view('hanoivip::admin.webtopup-retry-result', ['message' => "OK. Credit added."]);
                        }
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            Log::error("WebTopup admin check exception:" . $ex->getMessage());
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
                $log = $this->request->request($gmId, $targetId, $reason, $amount);
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