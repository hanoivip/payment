<?php

use Illuminate\Support\Facades\Route;

// Private APIs
Route::middleware('auth:api')->prefix('api')->namespace('Hanoivip\Payment\Controllers')->group(function () {
    // Xem thông tin tài khoản
    Route::any('/topup/info', 'BalanceController@info')->name('api.balance.info');
    // Lịch sử nạp
    Route::any('/topup/historyP', 'WebTopup@topupHistory');
    Route::any('/history/cards', 'HistoryController@topupHistory')->name('api.history.topup');
    // Lịch sử chuyển xu
    Route::any('/topup/historyR', 'BalanceController@modHistory');
    Route::any('/history/buys', 'HistoryController@rechargeHistory')->name('api.history.recharge');
});

// New flow
Route::middleware('auth:api')->prefix('api')->namespace('Hanoivip\Payment\Controllers')->group(function () {
    Route::any('/pay/methods', 'NewTopup@listMethods');
    Route::any('/pay/init', 'NewTopup@choose');
    Route::any('/pay/do', 'NewTopup@topup');
    Route::any('/pay/query', 'NewTopup@query');
	// Quick topup
    Route::any('/webtopup', 'WebTopup@quickTopup');
	Route::any('/webtopup/done', 'WebTopup@topupDone');
	Route::any('/pay/quick/topup', 'WebTopup@quickTopup');
	//Route::any('/pay/quick/recharge', 'WebTopup@quickRecharge');
});

// Public APIs
// TODO: cache middleware
Route::prefix('api')->namespace('Hanoivip\Payment\Controllers')->group(function () {
    // Lấy xếp hạng tài phú: tổng thể, theo tuần, tháng... (global, week, month)
    Route::any('/topup/rank/{key}', 'StatsController@rankByKey');
});

