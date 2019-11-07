<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    //

    use Notifiable;

    protected $table = 'zx_orders';

    protected $guarded = [];

    // 订单状态
    const ORDER_PENDING = 10; // 待付款
    const ORDER_PAID = 20; // 已付款
    const ORDER_COMPLETED = 30; // 已完成
    const ORDER_INVALID = 40; // 已失效
    const ORDER_PAID_FAIL = 50; // 支付失败

    // 订单前缀
    const ORDER_PRE_ZIXUN = 'ZX';

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

    // 订单和预订的时间关联
    public function orderTimes()
    {
        return $this->hasMany(OrdersTimesMap::class,'order_id','id');
    }

    // 获取订单号
    public function getOrderNo($pre)
    {
        $data = DB::select('select CreateOrderNo("'.$pre.'",8) as order_no limit 1');
        return $data[0]->order_no;
    }

    // 获取订单状态
    public function status($ind = null) {
        $arr = [
            self::ORDER_PENDING => '待付款',
            self::ORDER_PAID => '已付款',
            self::ORDER_COMPLETED => '已完成',
            self::ORDER_INVALID => '已失效',
            self::ORDER_PAID_FAIL => '支付失败',
        ];

        if ($ind !== null) {
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::ORDER_PENDING];
        }

        return $arr;
    }
}
