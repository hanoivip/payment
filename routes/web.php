<?php
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    'auth:web'
])->namespace('Hanoivip\Payment\Controllers')->group(function () {
    // new gate version
    Route::get('/topup/step1', 'TopupController@topupUI2')->name('topup');
    Route::get('/topup/step2', 'TopupController@selectType')->name('topup.by.type');
    Route::post('/topup/result', 'TopupController@topup2')->name('webTopup');
    Route::get('/topup/recaptcha', 'TopupController@recaptcha')->name('topup.recaptcha');
    Route::get('/topup/cancel', 'TopupController@cancel')->name('topup.cancel');
    // reactjs UI
    Route::get('/jtopup', 'TopupController@jsTopup')->name('jtopup');
    Route::get('/jhistory', 'TopupController@jsHistory')->name('jhistory');
    Route::get('/jrecharge', 'TopupController@jsRecharge')->name('jrecharge');
    // tracking topup
    Route::get('/topup/success', 'TopupController@onTopupSuccess')->name('topup.success');  
    // 20210728: new flow
    Route::get('/new/topup', 'NewTopup@listMethods')->name('newtopup');
    Route::post('/new/topup/shop', 'NewTopup@showShop')->name('newtopup.shop');
    Route::post('/new/topup', 'NewTopup@choose')->name('newtopup.choose');
    Route::any('/new/topup/do', 'NewTopup@topup')->name('newtopup.do');
    Route::any('/new/topup/query', 'NewTopup@query')->name('newtopup.query');
});

Route::middleware([
    'web',
    'admin'
])->namespace('Hanoivip\Payment\Controllers')
    ->prefix('ecmin')
    ->group(function () {
});