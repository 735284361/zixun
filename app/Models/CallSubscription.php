<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallSubscription extends Model
{
    //

    protected $table = 'zx_call_subscriptions';

    protected $guarded = [];

    public function bindInfo()
    {
        return $this->belongsTo(CallBindRecord::class,'bind_id','id');
    }

}
