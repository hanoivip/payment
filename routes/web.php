<?php
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    'auth:web'
])->namespace('Hanoivip\Payment\Controllers')->group(function () {
    // new gate version: deprecated
    Route::get('/topup/step1', function (){
        return response()->redirectToRoute('webtopup');
    })->name('topup');
    // reactjs UI
    //Route::get('/jtopup', 'TopupController@jsTopup')->name('jtopup');
    // Route::get('/jhistory', 'TopupController@jsHistory')->name('jhistory');
    //Route::get('/jrecharge', 'TopupController@jsRecharge')->name('jrecharge');
    // tracking topup
    //Route::get('/topup/success', 'TopupController@onTopupSuccess')->name('topup.success');  
    // 20210728: new flow
    Route::get('/new/topup', 'NewTopup@listMethods')->name('newtopup');
    Route::post('/new/topup/shop', 'NewTopup@showShop')->name('newtopup.shop');
    Route::post('/new/topup', 'NewTopup@choose')->name('newtopup.choose');
    Route::any('/new/topup/do', 'NewTopup@topup')->name('newtopup.do');
    Route::any('/new/topup/query', 'NewTopup@query')->name('newtopup.query');
    // 20211101: web credit
    Route::get('/webtopup', 'WebTopup@index')->name('webtopup');
    Route::any('/webtopup/method', 'WebTopup@choose')->name('webtopup.method');
    Route::any('/webtopup/done', 'WebTopup@topupDone')->name('webtopup.done');
    Route::any('/webtopup/query', 'WebTopup@query')->name('webtopup.query');
    Route::any('/webtopup/history', 'WebTopup@history')->name('webtopup.history');
    // 202304: balance partial
    Route::any('/balance/info', 'BalanceController@info')->name('balance.info');
});

Route::middleware([
    'web',
    'admin'
])->namespace('Hanoivip\Payment\Controllers')
    ->prefix('ecmin')
    ->group(function () {
        Route::any('/balance/history', 'AdminController@balanceHistory')->name('ecmin.balance.history');
        Route::any('/webtopup/history', 'AdminController@webtopupHistory')->name('ecmin.webtopup.history');
        // Stats
        Route::get('/stats/stat', 'AdminController@stats')->name('ecmin.stats');
        Route::any('/stats/today', 'AdminController@today')->name('ecmin.stats.today');
        Route::any('/stats/month', 'AdminController@thisMonth')->name('ecmin.stats.month');
        Route::any('/stats/week', 'AdminController@thisWeek')->name('ecmin.stats.week');
        Route::any('/stats/bymonth', 'AdminController@byMonth')->name('ecmin.stats.bymonth');
        // Ops 
        //Route::any('/webtopup', 'AdminController@ops')->name('ecmin.webtopup');
        //Route::any('/webtopup/retry', 'AdminController@retry')->name('ecmin.webtopup.retry');
        Route::any('/webtopup/check', 'AdminController@check')->name('ecmin.webtopup.check');
        // Balance
        Route::any('/balance/request', 'AdminController@balanceRequest')->name('ecmin.balance.request');
        //Route::any('/balance/request-pendings', 'AdminController@balancePendings')->name('ecmin.balance.pendings');
});