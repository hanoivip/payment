<?php

namespace Hanoivip\Payment;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Hanoivip\Events\Gate\UserTopup' => [
            //'Hanoivip\Payment\Services\PolicyService',
            //'Hanoivip\Payment\Activities\RankingActivity',
            'Hanoivip\Payment\Services\UserTopupHandler',
        ],
        //'Hanoivip\GateClientNew\Event\DelayCard' => [
        //    'Hanoivip\Payment\Services\TopupService',
        //],
        'Hanovip\Events\Payment\TransactionUpdated' => [
            'Hanoivip\Payment\Services\NewTopupService'
        ]
    ];
    
    public function boot()
    {
        parent::boot();
    }
}