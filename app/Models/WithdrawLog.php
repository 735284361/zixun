<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawLog extends Model
{
    //

    protected $table = 'zx_withdraw_logs';

    protected $guarded = [];

    public function withdraw()
    {
        return $this->belongsTo(Withdraw::class,'withdraw_id','id');
    }

}
