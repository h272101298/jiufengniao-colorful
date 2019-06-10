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

Route::options('{all}',function (){return 'ok';})->middleware('cross');
Route::get('/', function () {
    return view('welcome');
});
Route::post('login','UserController@login');
Route::post('upload','SystemController@upload');
Route::group(['middleware'=>['auth']],function (){
    Route::post('picture','PictureController@addPicture');
    Route::delete('picture','PictureController@delPicture');
    Route::get('pictures','PictureController@getSystemPictures');
    Route::get('types','GoodController@getTypes');
    Route::post('type','GoodController@addType');
    Route::delete('type','GoodController@delType');
    Route::get('goods','GoodController@getGoods');
    Route::post('good','GoodController@addGood');
    Route::get('good','GoodController@getGood');
    Route::delete('good','GoodController@delGood');
    Route::get('banners','GoodController@getBanners');
    Route::post('banner','GoodController@addBanner');
    Route::delete('banner','GoodController@delBanner');
    Route::post('score/product','ScoreController@addScoreProduct');
    Route::get('score/product','ScoreController@getScoreProduct');
    Route::get('score/products','ScoreController@getScoreProducts');
    Route::delete('score/product','ScoreController@delScoreProduct');
    Route::post('tx/config','SystemController@addTxConfig');
    Route::get('tx/config','SystemController@getTxConfig');
    Route::post('sign/config','SignController@addSignConfig');
    Route::get('sign/configs','SignController@getSignConfigs');
    Route::post('launcher','LauncherController@addLauncherImage');
    Route::get('launchers','LauncherController@getLauncherImages');
    Route::delete('launcher','LauncherController@delLauncherImage');
    Route::get('recommend','GoodController@getRecommend');
    Route::post('recommend','GoodController@setRecommend');
    Route::get('documents','DocumentController@getDocuments');
    Route::get('document','GoodController@getDocument');
    Route::post('document','DocumentController@addDocument');
    Route::delete('document','GoodController@delDocument');
});