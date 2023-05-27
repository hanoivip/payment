<?php
namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hanoivip\Payment\Services\StatisticService;
use Hanoivip\User\Facades\UserFacade;

/**
 *
 * @author hanoivip
 */
class StatsController extends Controller
{
    private $statistics;
    
    public function __construct(StatisticService $service)
    {
        $this->statistics = $service;
    }
    
    public function rankByKey(Request $request, $key)
    {
        $curWeek = date('YW', time());
        $curMonth = date('Ym', time());
        $map = [
            'global' => 'income_user',
            'week' => 'income_user_week_' . $curWeek,
            'month' => 'income_user_month_' . $curMonth,
        ];
        $list = [];
        $out = [];
        if (isset($map[$key]))
        {
            $statKey = $map[$key];
            $list = $this->statistics->rankByKey($statKey);
        }
        if (!empty($list))
        {
            foreach ($list as $rank => $arr)
            {
                $userId = $arr[0];
                $value = $arr[1];
                $user = UserFacade::getUserCredentials($userId);
                $displayName =  "user " . $userId;
                if (!empty($user))
                {
                    $displayName = !empty($user->hoten) ? $user->hoten : $user->name;
                }
                $out[$rank] = [$rank, $displayName, $value];
            }
        }
        if ($request->expectsJson())
        {
            return ['error' => 0, 'message' => 'success', 'data' => $out];
        }
        else
        {
            return view('hanoivip::rank-topup-partial', ['rank' => $out]);
        }
    }
}