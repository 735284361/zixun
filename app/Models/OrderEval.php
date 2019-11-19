<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderEval extends Model
{
    //

    protected $table = 'zx_orders_eval';

    public function user()
    {
        return $this->belongsTo(\App\User::class,'user_id','uid');
    }
}
