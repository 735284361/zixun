<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallFeeRecord extends Model
{
    //

    protected $table = 'zx_call_fee_records';

    protected $guarded = [];

    public function getContentAttribute($value)
    {
        return json_decode($value,true);
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = json_encode($value);
    }
}
