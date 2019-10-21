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

Route::any('/wechat/decryptData', 'Mini\WeChatController@decryptData');
Route::any('/wechat/login', 'Mini\WeChatController@login');
Route::any('/wechat/register', 'Mini\WeChatController@register');

Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/user', function () {
        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料

        dd($user);
    });
});

//前端小程序拿到的地址：https://域名/api/v1/自己写的接口
Route::group(['prefix' => '/v1'], function () {
    Route::any('/user/login', 'UserController@wxLogin');
});

//Route::any('/wechat/profile', 'Mini\WeChatController@profile');

Route::group(['middleware' => ['auth:api']], function () {
    Route::any('/wechat/profile', 'Mini\WeChatController@profile');
});
