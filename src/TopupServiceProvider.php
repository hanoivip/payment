<?php

namespace Hanoivip\Payment;

use Hanoivip\Payment\Policies\GiftPolicy;
use Hanoivip\Payment\Services\BalanceService;
use Hanoivip\Payment\Services\NewTopupService;
use Illuminate\Support\ServiceProvider;

class TopupServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind("BalanceService", BalanceService::class);
        $this->app->bind("GiftPolicy", function ($app, $cfg) {
            return new GiftPolicy($cfg);
        });
        $this->commands([
            \Hanoivip\Payment\Commands\PolicyNew::class,
            \Hanoivip\Payment\Commands\TestBalance::class,
            \Hanoivip\Payment\Commands\TestTopup::class,
        ]);
        $this->app->bind("LocalPaymentService", NewTopupService::class);
    }
    
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/payment.php' => config_path('payment.php'),
            __DIR__ . '/../resources/assets' => resource_path('assets/vendor/hanoivip'),
            __DIR__ . '/../resources/images' => public_path('img'),
            __DIR__.'/../lang' => resource_path('lang/vendor/hanoivip'),
        ]);
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadTranslationsFrom( __DIR__.'/../lang/', 'hanoivip');
        //$this->mergeConfigFrom( __DIR__.'/../config/payment.php', 'payment');
    }
}