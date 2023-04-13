<?php

namespace Hanoivip\Payment;

use Hanoivip\Payment\Policies\GiftPolicy;
use Hanoivip\Payment\Services\BalanceRequest;
use Hanoivip\Payment\Services\BalanceService;
use Hanoivip\Payment\Services\NewTopupService;
use Illuminate\Support\ServiceProvider;

class TopupServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind("BalanceService", BalanceService::class);
        $this->app->bind("BalanceRequest", BalanceRequest::class);
        $this->app->bind("GiftPolicy", function ($app, $cfg) {
            return new GiftPolicy($cfg);
        });
        $this->commands([
            \Hanoivip\Payment\Commands\BalanceAdd::class,
            \Hanoivip\Payment\Commands\BalanceRemove::class,
            \Hanoivip\Payment\Commands\BalancePendings::class,
            \Hanoivip\Payment\Commands\BalanceApprove::class,
            \Hanoivip\Payment\Commands\BalanceReject::class,
            \Hanoivip\Payment\Commands\PolicyNew::class,
            \Hanoivip\Payment\Commands\TestTopup::class,
        ]);
        $this->app->bind("LocalPaymentService", NewTopupService::class);
    }
    
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/payment.php' => config_path('payment.php'),
            __DIR__.'/../lang' => resource_path('lang/vendor/hanoivip'),
        ], 'config');
        $this->publishes([
            __DIR__.'/../resources/assets/js' => public_path('js'),
            __DIR__ . '/../resources/images' => public_path('img'),
        ], 'assets');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadTranslationsFrom( __DIR__.'/../lang/', 'hanoivip.payment');
        //$this->mergeConfigFrom( __DIR__.'/../config/payment.php', 'payment');
    }
}