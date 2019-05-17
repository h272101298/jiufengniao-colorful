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
Route::group(['middleware'=>['auth']],function (){
    Route::post('picture','PictureController@addPicture');
    Route::delete('picture','PictureController@delPicture');
    Route::get('pictures','PictureController@getSystemPictures');
});