<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //

    protected $table = 'zx_orders';

    // 订单评论
    public function orderEval()
    {
        return $this->hasOne(OrderEval::class,'order_id','id');
    }
}
