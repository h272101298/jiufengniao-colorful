<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login','WeChatController@createUser');//Login
Route::post('proxy/apply','ProxyController@addProxyApply');//proxyApply
Route::post('address','WeChatController@addUserAddress');//proxyApply
Route::delete('address','WeChatController@delUserAddress');//proxyApply
Route::post('default/address','WeChatController@setDefaultAddress');//proxyApply
Route::get('addresses','WeChatController@getUserAddresses');//proxyApply
Route::post('user/info','WeChatController@addUserInfo');//proxyApply
Route::get('user/info','WeChatController@getUserInfo');//proxyApply
