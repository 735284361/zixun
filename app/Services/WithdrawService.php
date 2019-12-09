<?php

namespace App\Services;

use App\Models\UsersAccount;
use App\Models\Withdraw;
use App\Models\WithdrawLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WithdrawService
{

    public $errors = ['code' => 0,'msg' => 'success'];

    protected $payService;
    protected $withdraw;

    public function __construct()
    {
        $this->payService = new PayService();
    }

    public function error()
    {
        return $this->errors;
    }

    /**
     * 申请提现
     * @param $data
     * @return bool
     * @throws \Throwable
     */
    public function apply($data)
    {
        $account = UsersAccount::where('user_id',auth('api')->id())->first();
        // 判断账户余额
        if ($account->account < env('WITHDRAW_TOTAL_MIN')) {
            $this->errors = ['code' => 1,'msg' => '账户余额不足'];
            return false;
        }
        // 判断当日已申请提现的额度
        $applyCount = Withdraw::where('user_id',auth('api')->id())->whereDate('created_at',Carbon::today())->sum('apply_total');
        if ($applyCount > env('WITHDRAW_TOTAL_MAX')) {
            $this->errors = ['code' => 1,'msg' => '超过日限额'];
            return false;
        }
        $exception = DB::transaction(function () use($data) {
            try {
                $applyTotal = $data['apply_total'];
                // 添加提现记录
                $withdraw = new Withdraw();
                $withdraw->user_id = auth('api')->id();
                $withdraw->withdraw_order = '123';
                $withdraw->apply_total = $applyTotal;
                $withdraw->save();

                // 添加提现日志
                $withdrawLog = new WithdrawLog([
                    'withdraw_id' => $withdraw->id,
                    'status' => Withdraw::STATUS_APPLY
                ]);
                $withdrawLog->save();

                // 减少用户账户余额 新增提现中的余额
                $userAccountService = new UsersAccountService($this->withdraw->user_id, $this->withdraw->apply_total);
                $userAccountService->applyWithdraw();
            }
            catch(Exception $e) {
                return $e;
            }
        });
        if (!is_null($exception)) {
            $this->errors = ['code' => 1,'msg' => '提现失败'];
        }
        return is_null($exception) ? true : false;
    }

    /**
     * 同意提现申请
     * @param $id
     * @param $remark
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function agreeWithdrawApply($id, $remark)
    {
        $this->withdraw = $withdraw = Withdraw::find($id);
        if (!$withdraw) {
            return ['code' => 1, 'msg' => '未查询到订单信息'];
        }

        if ($withdraw->status != Withdraw::STATUS_APPLY) {
            return ['code' => 1, 'msg' => '该订单不支持提现'];
        }
        // 查询提现状态
        $queryData = $this->payService->queryBalanceOrder($withdraw->withdraw_order);
        if ($queryData['return_code'] == 'SUCCESS' && $queryData['result_code'] == 'FAIL') {
            $transData =  $this->payService->transferToBalance($withdraw->withdraw_order, $withdraw->apply_total, $withdraw->user_id, remark);
            if ($transData['return_code'] == 'SUCCESS') {
                if ($transData['result_code'] == 'SUCCESS') {
                    $exception = DB::transaction(function () use ($remark) {
                        // 更新提现日志
                        $this->updateWithdrawStatus(Withdraw::STATUS_COMPLETED);
                        // 添加提现日志
                        $this->saveWithdrawLog(Withdraw::STATUS_COMPLETED, $remark);
                        // 处理提现中的余额
                        $userAccountService = new UsersAccountService($this->withdraw->user_id, $this->withdraw->apply_total);
                        $userAccountService->agreeWithdraw();
                    });
                    if (!$exception) {
                        return ['code' => 0, 'msg' => '成功'];
                    } else {
                        return ['code' => 1, 'msg' => '失败'];
                    }
                } else {
                    return ['code' => 1, 'msg' => $transData['err_code'].":".$transData['err_code_des']];
                }
            } else {
                $withdraw->status = Withdraw::STATUS_COMPLETED;
                return ['code' => 1, 'msg' => $transData['err_code'].":".$transData['err_code_des']];
            }
        } else {
            return ['code' => 1, 'msg' => '已打款'];
        }
    }

    /**
     * 拒绝提现申请
     * @param $id
     * @param $remark
     * @return array
     * @throws \Throwable
     */
    public function refuseWithdrawApply($id, $remark)
    {
        $this->withdraw = $withdraw = Withdraw::find($id);
        if (!$withdraw) {
            return ['code' => 1, 'msg' => '未查询到订单信息'];
        }

        if ($withdraw->status != Withdraw::STATUS_APPLY) {
            return ['code' => 1, 'msg' => '该订单不支持提现'];
        }
        // 处理提现中的余额
        $exception = DB::transaction(function () use ($remark) {
            // 更新提现日志
            $this->updateWithdrawStatus(Withdraw::STATUS_REFUSED);
            // 添加提现日志
            $this->saveWithdrawLog(Withdraw::STATUS_REFUSED, $remark);
            // 处理提现中的余额
            $userAccountService = new UsersAccountService($this->withdraw->user_id, $this->withdraw->apply_total);
            $userAccountService->refuseWithdraw();
        });
        if (!$exception) {
            return ['code' => 0, 'msg' => '成功'];
        } else {
            return ['code' => 1, 'msg' => '失败'];
        }
    }

    /**
     * 更新提现状态
     * @param $status
     * @return mixed
     */
    private function updateWithdrawStatus($status)
    {
        $this->withdraw->status = $status;
        return $this->withdraw->save();
    }

    /**
     * 保存提现日志
     * @param $status
     * @param $remark
     * @return mixed
     */
    private function saveWithdrawLog($status, $remark)
    {
        return $this->withdraw->withdrawLogs()->create([
            'remark' => $remark,
            'status' => $status
        ]);
    }

}
