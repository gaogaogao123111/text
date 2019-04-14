<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/info', function () {
    phpinfo();
});
Route::get('Weixin/valid','Weixin\WeixinController@valid');
Route::any('Weixin/valid','Weixin\WeixinController@event');
Route::get('/redis/token','Weixin\WeixinController@token');
