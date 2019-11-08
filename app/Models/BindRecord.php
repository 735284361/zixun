<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BindRecord extends Model
{
    //

    protected $table = 'zx_bind_records';

    protected $guarded = [];

    const STATUS_BINDING = 10; // 绑定中
    const STATUS_CANCEL = 20; // 绑定取消
    const STATUS_COMPLETE = 30; // 订单完成

}
