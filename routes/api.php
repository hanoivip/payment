<?php

use Illuminate\Support\Facades\Route;

// Private APIs
Route::middleware('auth:api')->prefix('api')->namespace('Hanoivip\Payment\Controllers')->group(function () {
    // Xem thông tin tài khoản
    Route::any('/topup/info', 'BalanceController@info')->name('api.balance.info');
    // Lịch sử nạp
    //Route::middleware('cacheByUser:60')->any('/topup/historyP', 'WebTopup@topupHistory');
    Route::middleware('cacheByUser:60')->any('/history/cards', 'HistoryController@topupHistory')->name('api.history.topup');
    // Lịch sử chuyển xu
    Route::any('/topup/historyR', 'BalanceController@modHistory');
    Route::any('/history/buys', 'HistoryController@rechargeHistory')->name('api.history.recharge');
});

// Payment Gateway
Route::middleware('auth:api')->prefix('api')->namespace('Hanoivip\Payment\Controllers')->group(function () {
    Route::any('/pay/methods', 'NewTopup@methods');
    Route::any('/pay/init', 'NewTopup@choose');
    Route::any('/pay/do', 'NewTopup@topup');
    Route::any('/pay/query', 'NewTopup@query');
});

// Public APIs
Route::middleware('cache:86400')->prefix('api')->namespace('Hanoivip\Payment\Controllers')
->group(function () {
    // Lấy xếp hạng tài phú: tổng thể, theo tuần, tháng... (global, week, month)
    Route::any('/topup/rank/{key}', 'StatsController@rankByKey');
});