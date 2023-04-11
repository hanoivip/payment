<?php

namespace Hanoivip\Payment\Facades;

use Illuminate\Support\Facades\Facade;

class BalanceRequest extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BalanceRequest';
    }
}
