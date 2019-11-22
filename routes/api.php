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
    Route::post('/teacher/list', 'Mini\TeachersController@lists'); // 列表
    Route::get('/teacher/detail', 'Mini\TeachersController@detail'); // 详情

    // Banner 列表
    Route::get('banner/list','Mini\BannersController@lists'); // 列表

    // Tag 标签
    Route::get('tag/list','Mini\TagsController@lists'); // 列表

    // 搜索
    Route::get('search/search','Mini\SearchController@search');

    // 热词
    Route::get('words/list','Mini\HotWordsController@lists');
    /**
     * ****************************************
     * 登录验证
     * ****************************************
     */
    Route::group(['middleware' => ['auth:api']], function () {

        // 用户相关接口
        Route::group(['prefix' => 'user'], function() {
            Route::get('account','Mini\UsersController@myAccount');
            Route::get('user_info','Mini\UsersController@userInfo');
            Route::post('post_user_info','Mini\UsersController@postUserInfo');
            Route::post('withdraw/apply','Mini\WithdrawController@apply');
        });

        // 讲师
        Route::group(['prefix' => 'teacher'], function() {
            Route::post('set_time','Mini\TeachersController@setTimes');
            Route::get('get_time','Mini\TeachersController@getTime');
            Route::get('post_like_teacher','Mini\TeachersController@postLikeTeacher');
            Route::get('delete_like_teacher','Mini\TeachersController@deleteLikeTeacher');
            Route::get('my_teacher_info','Mini\TeachersController@myTeacherInfo');
        });

        // 订单
        Route::group(['prefix' => 'order'], function() {
            Route::post('post_order','Mini\OrdersController@postOrder');
            Route::get('order_info','Mini\OrdersController@orderInfo');
            Route::get('repay','Mini\OrdersController@repay');
            Route::get('order_list','Mini\OrdersController@orderList');
            Route::post('complete','Mini\OrdersController@completeOrder');
            Route::post('order_eval','Mini\OrdersController@orderEval');
            Route::post('teacherCancelOrder/{orderNo}','Mini\OrdersController@teacherCancelOrder');
            Route::get('test','Mini\OrdersController@test');
        });

        // 消息通知
        Route::group(['prefix' => 'message'], function() {
            Route::get('list','Mini\MessageController@lists');
            Route::get('markAsRead','Mini\MessageController@markAsRead');
            Route::get('markAsReadForAll','Mini\MessageController@markAsReadForAll');
            Route::get('unreadCount','Mini\MessageController@unreadCount');
            Route::get('markAsRead','Mini\MessageController@markAsRead');
            Route::get('markAsReadForAll','Mini\MessageController@markAsReadForAll');
            Route::get('deleteMsg','Mini\MessageController@deleteMsg');
            Route::get('delete','Mini\MessageController@delete');
        });

        // 通话
        Route::group(['prefix' => 'call'], function() {
            Route::get('bindAx','Mini\CallController@bindAx');
            Route::get('cancelAxBind','Mini\CallController@cancelAxBind');
            Route::get('getBindInfo','Mini\CallController@getBindInfo');
            Route::get('getBindInfos','Mini\CallController@getBindInfos');
        });
    });

    // 测试接口
    Route::group(['prefix' => '/test'], function () {
        Route::group(['middleware' => ['auth:api']], function() {
//            Route::any('/test', 'Mini\TestController@test');
        });
        Route::any('/test', 'Mini\TestController@test');
    });

    Route::group(['prefix' => '/pay'], function () {
        Route::group(['middleware' => ['auth:api']], function() {
            Route::any('/pay', 'Mini\PayController@pay');
        });
        Route::any('/callback', 'Mini\PayController@callback');
        Route::any('/refund', 'Mini\PayController@refund');
        Route::any('/refundCallback', 'Mini\PayController@refundCallback');
    });
    //呼叫回调
    Route::any('call/onCallEvent','Mini\CallController@onCallEvent');
    Route::any('call/onFeeEvent','Mini\CallController@onFeeEvent');
});

