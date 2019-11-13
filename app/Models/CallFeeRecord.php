<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallFeeRecord extends Model
{
    //

    protected $table = 'zx_call_fee_record';

    public function getDataAttribute($value)
    {
        return json_decode($value,true);
    }

    public function setDataAttribute($value)
    {
        $value = json_encode($value);
        return $value;
    }
}
