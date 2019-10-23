<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //

    protected $table = 'zx_orders';

    const ORDER_PENDING = 10; // 待付款
    const ORDER_PAID = 20; // 已付款
    const ORDER_COMPLETED = 30; // 已完成

    // 订单评论
    public function orderEval()
    {
        return $this->hasOne(OrderEval::class,'order_id','id');
    }

    // 获取用户信息
    public function userInfo()
    {
        return $this->hasOne(\App\User::class,'uid','user_id');
    }

    public function status($ind = null) {
        $arr = [
            self::ORDER_PENDING => '待付款',
            self::ORDER_PAID => '已付款',
            self::ORDER_COMPLETED => '已完成',
        ];

        if ($ind !== null) {
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::ORDER_PENDING];
        }

        return $arr;
    }
}
