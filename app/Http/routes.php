<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web', 'transaction']], function () {
    Route::get('/', 'ViewController@project');
    Route::post('transaction/reserve', 'TransactionController@reserve');
    Route::get('transaction', 'ViewController@transaction');
    Route::post('transaction/process', 'TransactionController@process');
    Route::post('transaction/cancel', 'TransactionController@cancel');
});
Route::group(['prefix' => 'api', 'middleware' => ['web']], function () {
    Route::resource('project', 'ProjectController');
});