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
Route::post('pay/notify','OrderController@notifyOrder');
Route::post('proxy/apply','ProxyController@addProxyApply');//proxyApply
Route::post('address','WeChatController@addUserAddress');//proxyApply
Route::delete('address','WeChatController@delUserAddress');//proxyApply
Route::post('default/address','WeChatController@setDefaultAddress');//proxyApply
Route::get('default/address','WeChatController@getDefaultAddress');//proxyApply
Route::get('addresses','WeChatController@getUserAddresses');//proxyApply
Route::get('address','WeChatController@getUserAddress');//proxyApply
Route::post('user/info','WeChatController@addUserInfo');//proxyApply
Route::get('user/info','WeChatController@getUserInfo');//proxyApply
Route::get('user/amount','ProxyController@getUserProxyAmount');//proxyApply
Route::get('proxy/users','ProxyController@getProxyUsers');//proxyApply
Route::get('user/pictures','PictureController@getUserPictures');//proxyApply
Route::get('pictures','PictureController@getPictures');//proxyApply
Route::post('picture','PictureController@addPicture');//
Route::get('types','GoodController@getTypes');
Route::get('goods','GoodController@getGoods');
Route::post('good/detail','GoodController@addGoodDetail');
Route::get('banners','GoodController@getBanners');
Route::get('good/details','GoodController@getGoodDetails');
Route::post('order','OrderController@makeOrder');
Route::post('pay/order','OrderController@payOrder');
Route::get('score/product','ScoreController@getScoreProduct');
Route::get('score/products','ScoreController@getScoreProducts');
Route::post('sign','SignController@sign');
Route::get('sign','SignController@getSignRecords');
Route::get('sign/configs','SignController@getSignConfigs');
Route::get('launchers','LauncherController@getLauncherImages');
Route::get('attention/count','WeChatController@getAttentionCount');
Route::get('order/count','OrderController@getUserOrderCount');
Route::get('recommend','GoodController@getRecommend');
Route::get('documents','DocumentController@getDocuments');
Route::get('document','GoodController@getDocument');