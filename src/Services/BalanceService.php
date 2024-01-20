<?php

namespace Hanoivip\Payment\Services;

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
    const EXTEND_BALANCE = 1;
    const AGENCY_BALANCE = 2;

    /**
     * Truy xuất tất cả các loại tài khoản của ng chơi.
     * 
     * @param number $uid
     * @return Balance[]
     */
    public function getInfo($uid)
    {
        $balances = Balance::where('user_id', $uid)
        ->orderBy('balance_type', 'asc')
        ->get();
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
        /* not support anymore
        $targetValue = Currency::convert()
            ->from($sourceCurrency)
            ->to($targetCurrency)
            ->amount($sourceValue)
            ->get();
            */
        // convert base on my table
        $key = strtoupper($sourceCurrency) . "_TO_" . strtoupper($targetCurrency);
        $rate = config("currency_rates.$key", 0);
        if (!empty($rate))
        {
            $targetValue = $sourceValue * $rate;
        }
        else 
        {
            throw new Exception("Can not convert $sourceCurrency to $targetCurrency");
        }
        if ($multiple100)
        {
            $targetValue = $targetValue * 100;
        }
        return $targetValue;
    }
    
    public function convertWebcoin($value, $currency)
    {
        $balanceCurrency = config('payment.balance_currency', 'webcoin');
        return $this->convert($value, $currency, $balanceCurrency);
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
            $coin = $this->convertWebcoin($value, $currency);
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
            Log::error("Balance value is zero or negative. skip!");
            return false;
        }
        if (!empty($currency))
        {
            $coin = $this->convertWebcoin($value, $currency);
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
    public function getHistory($uid, $page = 0, $count = 10)
    {
        $mods = BalanceMod::where('user_id', $uid)
        ->skip($page * $count)
        ->take($count)
        ->orderBy('id', 'desc')
        ->get();
        $objects = [];
        foreach ($mods as $mod)
        {
            $obj = new \stdClass();
            $obj->balance = $mod->balance;
            $obj->acc_type = $mod->balance_type == 0;
            $obj->reason = $mod->reason;
            $obj->time = $mod->created_at;
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
        if (!empty($currency))
        {
            $coin = $this->convertWebcoin($amount, $currency);
        }
        else
        {
            $coin = $amount;
        }
        if (!empty($balance))
            return $balance->balance >= $coin;
        return false;
    }

}