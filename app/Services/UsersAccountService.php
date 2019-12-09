<?php

namespace App\Services;

use App\Models\UsersAccount;

class UsersAccountService
{

    protected $userAccount;
    protected $account;

    public function __construct($userId, $account)
    {
        $this->userAccount = UsersAccount::firstOrCreate(['user_id'=>$userId]);
        $this->account = $account;
    }

    /**
     * 申请提现
     */
    public function applyWithdraw()
    {
        // 减少 账户余额
        $this->decAccount();
        // 增加 提现中 余额
        $this->incAccountWithdraw();
        return;
    }

    /**
     * 同意提现
     */
    public function agreeWithdraw()
    {
        // 减少 提现中 的余额
        $this->decAccountWithdraw();
        // 增加 已提现 的余额
        $this->incAccountSettled();
        return;
    }

    /**
     * 拒绝提现
     */
    public function refuseWithdraw()
    {
        // 减少 提现中 的余额
        $this->decAccountWithdraw();
        // 增加 余额
        $this->incAccount();
        return;
    }

    /**
     * 增加账户余额
     * @return mixed
     */
    public function incAccount()
    {
        return $this->userAccount->increment('account',$this->account);
    }

    /**
     * 减少账户余额
     * @return mixed
     */
    public function decAccount()
    {
        return $this->userAccount->decrement('account',$this->account);
    }

    /**
     * 增加 提现中 余额
     * @return mixed
     */
    public function incAccountWithdraw()
    {
        return $this->userAccount->increment('account_withdraw',$this->account);
    }

    /**
     * 减少 提现中 余额
     * @return mixed
     */
    public function decAccountWithdraw()
    {
        return $this->userAccount->decrement('account_withdraw',$this->account);
    }

    /**
     * 增加 已提现 余额
     * @return mixed
     */
    public function incAccountSettled()
    {
        return $this->userAccount->increment('account_settled',$this->account);
    }

}
