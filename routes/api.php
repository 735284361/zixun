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
    // 讲师接口
    Route::get('/teacher/list', 'Mini\TeachersController@lists'); // 列表
    Route::get('/teacher/detail', 'Mini\TeachersController@detail'); // 详情

    // Banner 列表
    Route::get('banner/list','Mini\BannersController@lists'); // 列表

    // Tag 标签
    Route::get('tag/list','Mini\TagsController@lists'); // 列表


    Route::get('test','Mini\OrdersController@test');
    Route::get('getCache','Mini\OrdersController@getCache');
    Route::get('jianting','Mini\OrdersController@jianting');
    Route::get('postOrder','Mini\OrdersController@postOrder');
    /**
     * ****************************************
     * 登录验证
     * ****************************************
     */
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('userinfo/postUser','Mini\UserInfoController@postUser');
        Route::post('profile','Mini\WeChatController@profile');

        // 用户相关接口
        Route::group(['prefix' => 'user'], function() {
            Route::get('account','Mini\UsersController@myAccount');
        });

        Route::group(['prefix' => 'teacher'], function() {
            Route::post('set_time','Mini\TeachersController@setTimes');
            Route::get('get_time','Mini\TeachersController@getTime');
            Route::get('post_like_teacher','Mini\TeachersController@postLikeTeacher');
            Route::get('delete_like_teacher','Mini\TeachersController@deleteLikeTeacher');
            Route::get('my_teacher_info','Mini\TeachersController@myTeacherInfo');
        });

        Route::group(['prefix' => 'order'], function() {
            Route::post('post_order','Mini\OrdersController@postOrder');
        });
    });
});

