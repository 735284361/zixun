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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/wechat/login', 'Mini\WeChatController@login');
Route::get('/wechat/register', 'Mini\WeChatController@register');

//前端小程序拿到的地址：https://域名/api/v1/自己写的接口
Route::group(['prefix' => '/v1'], function () {
    Route::any('/user/login', 'UserController@wxLogin');
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::any('/wechat/profile', 'Mini\WeChatController@profile');
});
