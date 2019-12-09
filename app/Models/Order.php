<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    //

    protected $table = 'zx_orders';

    protected $guarded = [];

    // 订单状态
    const ORDER_PENDING = 10; // 待付款
    const ORDER_PAID = 20; // 已付款
    const ORDER_COMPLETED = 30; // 已完成
    const ORDER_INVALID = 40; // 过期失效
    const ORDER_PAID_FAIL = 50; // 支付失败
    const ORDER_TEACHER_CANCEL = -10; // 讲师取消订单

    // 订单前缀
    const ORDER_PRE_ZIXUN = 'ZX';
    const REFUND_PRE_ZIXUN = 'TK';
    const ORDER_PRE_WITHDRAW = 'TX';

    // 订单评论
    public function orderEval()
    {
        return $this->hasOne(OrderEval::class,'order_id','id');
    }

    // 获取用户信息
    public function user()
    {
        return $this->belongsTo(\App\User::class,'user_id','uid');
    }

    // 用户信息
    public function userInfo()
    {
        return $this->belongsTo(UsersInfo::class,'user_id','user_id');
    }

    // 订单和预订的时间关联
    public function orderTimes()
    {
        return $this->hasMany(OrdersTimesMap::class,'order_id','id');
    }

    // 关联的老师
    public function teacher()
    {
        return $this->belongsTo(Teacher::class,'teacher_id','id');
    }

    // 电话绑定信息
    public function phoneBindInfo()
    {
        return $this->hasOne(BindRecord::class,'order_no','order_no');
    }

    // 分成账单
    public function bill()
    {
        return $this->hasOne(EntryBill::class,'order_no','order_no');
    }

    // 订单被拒信息
    public function refused()
    {
        return $this->hasOne(OrderRefuse::class,'order_id','id');
    }

    // 获取订单号
    public static function getOrderNo($pre)
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
            self::ORDER_INVALID => '过期失效',
            self::ORDER_PAID_FAIL => '支付失败',
            self::ORDER_TEACHER_CANCEL => '讲师取消',
        ];

        if ($ind !== null) {
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::ORDER_PENDING];
        }

        return $arr;
    }
}
