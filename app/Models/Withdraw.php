<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    //
    protected $table = 'zx_withdraw';

    const STATUS_APPLY = 10; // 10 提现申请
    const STATUS_PASSED = 20; // 20 审批通过
    const STATUS_COMPLETED = 30; // 30 提现成功
    const STATUS_REFUSED = -10; // -10 审核失败
    const STATUS_WITHDRAW_FAIL = -20; // -20 提现失败

    public function withdrawLogs()
    {
        return $this->hasMany(WithdrawLog::class,'withdraw_id','id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class,'user_id','user_id');
    }

    public static function getStatus($ids = null) {
        $arr = [
            self::STATUS_APPLY => '申请提现',
            self::STATUS_PASSED => '审批通过',
            self::STATUS_COMPLETED => '提现成功',
            self::STATUS_REFUSED => '审核失败',
            self::STATUS_WITHDRAW_FAIL => '提现失败',
        ];

        if ($ids !== null)  {
            return array_key_exists($ids, $arr) ? $arr[$ids] : $arr[self::STATUS_APPLY];
        }
        return $arr;
    }

}
