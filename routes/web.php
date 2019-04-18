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

Route::get('/redis/atoken','Weixin\WeixinController@atoken');

Route::get('Weixin/valid','Weixin\WeixinController@valid');
Route::any('Weixin/valid','Weixin\WeixinController@event');
Route::get('Weixin/token','Weixin\WeixinController@token');//获取token
Route::get('Weixin/createmenu','Weixin\WeixinController@createmenu');     //创建菜单
Route::get('Weixin/send','Weixin\WeixinController@send');//群发消息


Route::get('Weixin/text','Weixin\WxPayController@text');
Route::get('Weixin/notify','Weixin\WxPayController@notify');



