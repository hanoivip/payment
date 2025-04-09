<?php
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    // 'auth:web'
])->namespace('Hanoivip\Payment\Controllers')->group(function () {
    // Payment Gateway
    Route::get('/new/topup', 'NewTopup@start')->name('newtopup');
    Route::post('/new/topup/shop', 'NewTopup@showShop')->name('newtopup.shop');
    Route::post('/new/topup', 'NewTopup@choose')->name('newtopup.choose');
    Route::middleware('lockByUser:10,5')->any('/new/topup/do', 'NewTopup@topup')->name('newtopup.do');
    Route::any('/new/topup/query', 'NewTopup@query')->name('newtopup.query');
    // 202304: balance partial
    //Route::any('/balance/info', 'BalanceController@info')->name('balance.info');
});

Route::middleware([
    'web',
    'auth:web'
])->namespace('Hanoivip\Payment\Controllers')->group(function () {
    Route::any('/balance/info', 'BalanceController@info')->name('balance.info');
});

Route::middleware([
    'web',
    'admin'
])->namespace('Hanoivip\Payment\Controllers')
    ->prefix('ecmin')
    ->group(function () {
        Route::any('/balance/history', 'AdminController@balanceHistory')->name('ecmin.balance.history');
        // Stats
        Route::get('/stats/stat', 'AdminController@stats')->name('ecmin.stats');
        Route::get('/stats/statMonth', 'AdminController@statsMonth')->name('ecmin.stats.month');
        Route::any('/stats/today', 'AdminController@today')->name('ecmin.stats.today');
        Route::any('/stats/thisMonth', 'AdminController@thisMonth')->name('ecmin.stats.thismonth');
        Route::any('/stats/thisWeek', 'AdminController@thisWeek')->name('ecmin.stats.thisweek');
        Route::any('/stats/bymonth', 'AdminController@byMonth')->name('ecmin.stats.bymonth');
        // Ops 
        //Route::any('/pay/find-user-by-order', 'AdminController@findUserByOrder')->name('ecmin.pay.finduser');
        // Balance
        Route::any('/balance/request', 'AdminController@balanceRequest')->name('ecmin.balance.request');
        //Route::any('/balance/request-pendings', 'AdminController@balancePendings')->name('ecmin.balance.pendings');
});