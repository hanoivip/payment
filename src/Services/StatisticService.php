<?php
namespace Hanoivip\Payment\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Payment\Models\StatisticType;
use Hanoivip\Payment\Models\Statistic;

class StatisticService
{   
    const STAT_CACHE_KEY = "cache_";
    
    const CACHE_INTERVAL = 300;
    
    /**
     * Add or create new record
     * 
     * @param string $key
     * @param integer $uid
     * @param integer $num
     */
    public function stat($key, $uid, $num)
    {
        $stat = Statistic::where('key', $key)
                        ->where('user_id', $uid)
                        ->get();
        if ($stat->count() > 1)
        {
            Log::error('Statistic more than 2 record with single key (per user)');
        }
        else if ($stat->count() <= 0)
        {
            $newStat = new Statistic();
            $newStat->key = $key;
            $newStat->user_id = $uid;
            $newStat->total = $num;
            $newStat->save();
        }
        else
        {
            // add
            $curStat = $stat->first();
            $curStat->total += $num;
            $curStat->save();
        }
            
    }
    
    public function getStatistics($key, $page = 0, $count = 10)
    {
        $cacheKey = self::STAT_CACHE_KEY . $key;
        if (Cache::has($cacheKey))
            return Cache::get($cacheKey);
        if ($count > 0)
            $stats = Statistic::where('key', $key)
            ->orderBy('total', 'desc')
            ->limit($count)
            ->skip($page * $count)
            ->get();
        else
            $stats = Statistic::where('key', $key)
            ->orderBy('total', 'desc')
            ->skip($page * $count)
            ->get();
        
        $expires = now()->addSeconds(self::CACHE_INTERVAL);
        Cache::put($cacheKey, $stats, $expires);
        return $stats;
    }
    
    public function addKey($key, $starttime = 0, $endtime = 0)
    {
        $stat = StatisticType::where('key', $key)
            ->get();
        if ($stat->isNotEmpty())
        {
            Log::error("Statistic key is duplicated.");
            return false;
        }
        if (empty($starttime))
            $starttime = time();
        if (empty($endtime))
            $endtime = time() + 10 * 365 * 86400;
        
        $stat = new StatisticType();
        $stat->key = $key;
        $stat->start_time = $starttime;
        $stat->end_time = $endtime;
        $stat->save();
        return true;    
    }
    
    public function removeKey($key)
    {
        $stat = StatisticType::where('key', $key)
                        ->get();
        if ($stat->isNotEmpty())
        {
            foreach ($stat->all() as $type)
            {
                $type->disable = true;
                $type->save();
            }
        }
    }
    /**
     * Get last <number> of days: 7 14 30
     * @param number $num
     */
    public function getLastDays($num = 7)
    {
        $keys = [];
        $idx = [];
        for ($i = $num - 1; $i >=0; --$i)
        {
            $today = date('Ymd', now()->subDays($i)->timestamp);
            $keys[] = "today_$today";
            $idx["today_$today"] = $i;
        }
        $stats = Statistic::whereIn('key', $keys)->get();
        $vals = [];
        foreach ($stats as $stat)
        {
            $vals[$idx[$stat->key]] = $stat->total;
        }
        return [$keys, $vals];
    }
    /**
     * Get last <number> of months: 3 6 9 12
     * @param number $num
     */
    public function getLastMonths($num = 3)
    {
        $keys = [];
        $idx = [];
        for ($i = $num - 1; $i >=0; --$i)
        {
            $today = date('Ymd', now()->subDays($i)->timestamp);
            $keys[] = "income_$today";
            $idx["income_$today"] = $i;
        }
        $stats = Statistic::whereIn('key', $keys)->get();
        $vals = [];
        foreach ($stats as $stat)
        {
            $vals[$idx[$stat->key]] = $stat->total;
        }
        return [$keys, $vals];
    }
    
}