<?php

namespace Hanoivip\Payment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Need documents..
 * @author gameo
 *
 */
class BalanceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BalanceService';
    }
}
