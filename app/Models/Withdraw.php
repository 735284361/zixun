<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    //
    protected $table = 'zx_withdraw';

    const STATUS_APPLY = 10; // 10 申请提现
    const STATUS_PASSED = 20; // 20 审批通过
    const STATUS_COMPLETED = 30; // 30 交易完成
    const STATUS_REFUSED = -10; // -10 审批不通过

    public function withdrawLogs()
    {
        $this->hasMany(WithdrawLog::class,'withdraw_id','id');
    }

}
