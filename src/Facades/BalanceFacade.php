<?php

namespace Hanoivip\Payment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static Illuminate\Database\Eloquent\Collection public getInfo($uid)
 * @method static boolean add($uid, $value, $reason, $type)
 * @method static boolean addCurrency($uid, $amount, $currency, $reason, $type)
 * @method static boolean remove($uid, $value, $reason, $type)
 * @method static boolean enough($uid, $coin, $type = 0)
 * @method static number convert($amount, $currency, $targetCurrency)
 */
class BalanceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BalanceService';
    }
}
