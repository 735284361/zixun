<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallEventLog extends Model
{
    //

    protected $table = 'zx_call_event_logs';

    public function getCallLogAttribute($value)
    {
        return json_decode($value,true);
    }

    public function setCallLogAttribute($value)
    {
        $value = json_encode($value);
        return $value;
    }
}
