<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallBindRecord extends Model
{
    //

    protected $table = 'zx_call_bind_records';

    protected $guarded = [];

    /**
     * 绑定日志
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bindLogs()
    {
        return $this->hasMany(CallBindRecordLog::class,'bind_id','id');
    }

    /**
     * 对应订单所产生的通信ID
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscription()
    {
        return $this->hasMany(CallSubscription::class,'bind_id','id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

}
