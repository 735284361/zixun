<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderEval extends Model
{
    //

    protected $table = 'zx_orders_eval';

    public function user()
    {
        return $this->hasOne(\App\User::class,'uid','user_id');
    }
}
