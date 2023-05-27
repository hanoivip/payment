<?php
namespace Hanoivip\Payment\Services;

use Hanoivip\Events\Gate\UserTopup;
use Hanoivip\Payment\Models\StatisticType;
use Illuminate\Support\Facades\Log;

class UserTopupHandler
{       
    private $statistics;
    
    public function __construct(StatisticService $service)
    {
        $this->statistics = $service;
    }
    
    public function handle(UserTopup $event)
    {
        //Log::debug("Statistics topup..");
        $curMonth = date('Ym', time());
        //all
        $this->statistics->stat("income", 0, $event->coin);
        //this month
        $this->statistics->stat("income_$curMonth", 0, $event->coin);
        //this week
        $curWeek = date('W', time());
        $this->statistics->stat("income_week_$curWeek", 0, $event->coin);
        //this user
        $week = date('YW', time());
        $this->statistics->stat("income_user", $event->uid, $event->coin);
        $this->statistics->stat("income_user_week_" . $week, $event->uid, $event->coin);
        $this->statistics->stat("income_user_month_" . $curMonth, $event->uid, $event->coin);
        $this->statistics->stat("income_user", $event->uid, $event->coin);
        //today
        $today = date('Ymd', time());
        $this->statistics->stat("today_$today", 0, $event->coin);
        
        $now = time();
        $types = StatisticType::where('start_time', '<=', $now)
        ->where('end_time', '>', $now)
        ->where('disable', false)
        ->get();
        foreach ($types->all() as $type)
        {
            $this->statistics->stat($type->key, $event->uid, $event->coin);
        }
    }
}