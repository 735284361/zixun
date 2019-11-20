<?php

namespace App\Services;

use App\Models\UsersAccount;
use App\Models\Withdraw;
use App\Models\WithdrawLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WithdrawService
{

    public function apply($data)
    {
        $account = UsersAccount::where('user_id',auth('api')->id())->first();
        // 判断账户余额
        if ($account->account < env('WITHDRAW_TOTAL_MIN')) {
            return false;
        }
        // 判断当日已申请提现的额度
        $applyCount = Withdraw::where('user_id',auth('api')->id())->whereDate('created_at',Carbon::today())->sum('apply_total');
        if ($applyCount > env('WITHDRAW_TOTAL_MAX')) {
            return false;
        }
        DB::transaction(function () use($data) {
            $applyTotal = $data['apply_total'];
            // 添加提现记录
            $withdraw = new Withdraw();
            $withdraw->user_id = auth('api')->id();
            $withdraw->withdraw_order = '123';
            $withdraw->apply_total = $applyTotal;
            $withdraw->save();

            // 添加提现日志
            $withdrawLog = new WithdrawLog([
                'user_id' => auth('api')->id(),
                'withdraw_id' => $withdraw->id,
                'status' => Withdraw::STATUS_APPLY
            ]);
            $withdrawLog->save();

            // 减少用户账户余额 新增提现中的余额
            UsersAccount::where('user_id',auth('api')->id())->decrement('account',$applyTotal);
            UsersAccount::where('user_id',auth('api')->id())->increment('account_withdraw',$applyTotal);
        });
        return true;
    }

}
