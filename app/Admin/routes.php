<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('banners', BannerController::class);
    $router->resource('teachers', TeachersController::class);
    $router->resource('tags', TagsController::class);
    $router->resource('withdraws', WithdrawController::class);

    // 接口
    $router->any('users', 'Api\UsersController@users')->name('admin.users');
    $router->any('withdraw/refuse', 'Api\WithdrawController@refuseWithdraw')->name('admin.withdraw.refuse');
    $router->any('withdraw/agree', 'Api\WithdrawController@agreeWithdraw')->name('admin.withdraw.agree');

});
