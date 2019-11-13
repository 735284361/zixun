<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallBindRecord extends Model
{
    //

    protected $table = 'zx_call_bind_records';

    protected $guarded = [];

    public function bindLogs()
    {
        return $this->hasMany(CallBindRecordLog::class,'bind_id','id');
    }

}
