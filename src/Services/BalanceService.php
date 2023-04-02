<?php

namespace Hanoivip\Payment\Services;

use AmrShawky\LaravelCurrency\Facade\Currency;
use Hanoivip\Payment\Models\BalanceMod;
use Hanoivip\Payment\Models\Balance;
use Illuminate\Support\Facades\Log;
use Hanoivip\Payment\Contracts\IBalance;
use Exception;
/**
 * 
 * @author GameOH
 * 22/02/2023: Add currency supports
 * 
 */
class BalanceService implements IBalance
{
    const MAIN_BALANCE = 0;

    /**
     * Truy xuất tất cả các loại tài khoản của ng chơi.
     * 
     * @param number $uid
     * @return Balance[]
     */
    public function getInfo($uid)
    {
        $balances = Balance::where('user_id', $uid)->get();
        return $balances;
    }
    /**
     * convert between currency
     * beware: webcoin = USD * 100
     * 
     * @param number $value
     * @param string $currency
     * @param string $targetCurrency
     * @throws Exception
     * @return number
     */
    public function convert($value, $currency, $targetCurrency)
    {
        if ($value < 0)
        {
            throw new Exception("Convert value must be equal greater than 0");
        }
        if (strtoupper($currency) == strtoupper($targetCurrency))
        {
            return $value;
        }
        $sourceCurrency = $currency;
        $sourceValue = $value;
        if (strtoupper($currency) == 'WEBCOIN')
        {
            $sourceCurrency = 'USD';
            $sourceValue = $value / 100;
        }
        $multiple100 = false;
        if (strtoupper($targetCurrency) == 'WEBCOIN')
        {
            $targetCurrency = 'USD';
            $multiple100 = true;
        }
        // convert
        $targetValue = Currency::convert()
            ->from($sourceCurrency)
            ->to($targetCurrency)
            ->amount($sourceValue)
            ->get();
        if ($multiple100)
        {
            $targetValue = $targetValue * 100;
        }
        return $targetValue;
    }
    
    public function convertWebcoin($value, $currency)
    {
        return $this->convert($value, $currency, 'webcoin');
    }
    
    /**
     * Convert amount of money (w/wo currency) into webcoin
     * - no currency => convert 1-1
     * - currency => convert into USD * 100
     * @param number $uid
     * @param string $type
     * @param number $value
     * @param string $reason
     * @param string $currency Source currency
     * @return boolean
     */
    public function add($uid, $value, $reason, $type = 0, $currency = null)
    {
        if ($value <= 0)
        {
            Log::warn("Balance value is zero or negative. skip!");
            return;
        }
        if (!empty($currency))
        {
            // exchange: $amount => USD * 100
            $coin = intval(Currency::convert()
                ->from($currency)
                ->to('USD')
                ->amount($value)
                ->get() * 100);
            $reason = $reason . ":$currency@$value";
        }
        else
        {
            $coin = intval($value);
        }
        $balance = Balance::where('user_id', $uid)
                        ->where('balance_type', $type)
                        ->first();
        if (empty($balance))
        {
            $balance = new Balance();
            $balance->user_id = $uid;
            $balance->balance_type = $type;
            $balance->balance = $coin;
            $balance->save();
        }
        else
        {
            $balance->balance += $coin;
            $balance->save();
        }
        // save log
        $log = new BalanceMod();
        $log->user_id = $uid;
        $log->balance_type = $type;
        $log->balance = $coin;
        $log->reason = $reason;
        $log->save();
        return true;
    }
    
    /**
     * 
     * @param number $uid
     * @param string $type Value to substract. Positive value.
     * @param number $value
     * @param string $reason
     * @param string $currency
     * @return boolean
     */
    public function remove($uid, $value, $reason, $type = 0, $currency = null)
    {
        if ($value <= 0)
        {
            Log::warn("Balance value is zero or negative. skip!");
            return false;
        }
        if (!empty($currency))
        {
            // exchange: $amount => USD * 100
            $coin = intval(Currency::convert()
                ->from($currency)
                ->to('USD')
                ->amount($value)
                ->get() * 100);
            $reason = $reason . ":$currency@$value";
        }
        else 
        {
            $coin = intval($value);
        }
        $balance = Balance::where('user_id', $uid)
            ->where('balance_type', $type)
            ->first();
        if (empty($balance))
        {
            Log::debug("Balance user {$uid} has not balance type {$type} yet.");
            return false;
        }
        if ($balance->balance < $coin)
        {
            Log::debug("Balance user {$uid} has not enough balance");
            return false;
        }
        $balance->balance -= $coin;
        $balance->save();
        // save log
        $log = new BalanceMod();
        $log->user_id = $uid;
        $log->balance_type = $type;
        $log->balance = -1 * $coin;
        $log->reason = $reason;
        $log->save();
        return true;
    }
    
    /**
     * 
     * @param number $uid User ID
     * @param number $page Requested Page
     * @param number $count Number of rows to fetch
     * @return \stdClass[]
     */
    public function getHistory($uid, $page = 1, $count = 10)
    {
        $mods = BalanceMod::where('user_id', $uid)
        ->skip(($page - 1) * $count)
        ->take($count)
        ->orderBy('id', 'desc')
        ->get();
        $objects = [];
        foreach ($mods as $mod)
        {
            $obj = new \stdClass();
            $obj->balance = $mod->balance;
            $obj->acc_type = $mod->balance_type == 0 ;//? 'TK chính' : 'TK phụ';
            $list = explode(':', $mod->reason);
            if($list[0]=='Recharge')
            {
                $reason = __('hanoivip.payment::balance.' . $list[0]) . $list[3];
            }
            else
            {
                $reason = __('hanoivip.payment::balance.' . $list[0]);
                if (isset($list[1]))
                    $reason = $reason . ' ' . $list[1];
            }
            $obj->reason = $reason;
            $obj->time = $mod->created_at;//Carbon::parse($mod->created_at)->format('d/M/Y m:H');
            $objects[] = $obj;
        }
        $total = BalanceMod::where('user_id', $uid)->count();
        return [$objects, ceil($total / 10), $page];
    }
    
    public function enough($uid, $amount, $type = 0, $currency = null)
    {
        $balance = Balance::where('user_id', $uid)
                        ->where('balance_type', $type)
                        ->first();
        $coin = $amount;
        if (!empty($currency))
        {
            $coin = intval(Currency::convert()
                ->from($currency)
                ->to('USD')
                ->amount($amount)
                ->get()) * 100;
        }
        if (!empty($balance))
            return $balance->balance >= $coin;
        return false;
    }

}