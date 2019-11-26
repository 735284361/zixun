<?php

namespace App\Services;

use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\OrderRefuse;
use App\Models\Teacher;
use App\Models\TeachersTime;
use App\Models\UsersAccount;
use EasyWeChat\Kernel\Exceptions\Exception;
use Illuminate\Support\Facades\DB;

class OrdersService
{

    protected $payService;
    protected $order;

    public function __construct()
    {
        $this->payService = new PayService();
    }

    /**
     * 添加订单
     * @param $data
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addOrder($data)
    {
        $order = new Order();

        // 获取订单编号
        $orderNo = Order::getOrderNo(Order::ORDER_PRE_ZIXUN);
        // 计算开始结束时间和时长
        $timeIdArr = $data['times'];
        $teacher = Teacher::with(['teacherTimes' => function($query) use ($data) {
            $query->where('status',TeachersTime::STATUS_TIMES_ENABLE)
                ->whereIn('id',$data['times'])
                ->orderBy('start_at','asc')
                ->select();
        }])->where('id',$data['teacher_id'])->first()->toArray();
        // 判断所选时间是否有效
        $timesData = $teacher['teacher_times'];
        $count = count($timesData);
        // 上传的时间数量和查出来满足条件的时间数量不等
        if (count($timeIdArr) !== $count) return ['code' => 1,'msg' => '时间数据选择错误'];
        if ($count > 1) {
            for ($i = 0; $i < $count; $i++) {
                if ($timesData[$i]['start_at'] <= time()) {
                    return ['code' => 1,'msg' => '选择的时间已过期'];
                }
                // 时间段大于1、非最后一个时间段、第一个时间段的开结束间等于第二个时间段的开始时间
                if (($i != $count -1) && ($timesData[$i]['end_at'] != $timesData[$i+1]['start_at'])) {
                    return ['code' => 1,'msg' => '时间数据选择错误'];
                }
            }
        } else {
            if ($timesData[0]['start_at'] <= time()) {
                return ['code' => 1,'msg' => '选择的时间已过期'];
            }
        }

        // 存储订单信息
        $startAt = $timesData[0]['start_at'];
        $endAt = $timesData[$count - 1]['end_at'];
        $timeLen = ceil(($endAt - $startAt) / 60);

        // 订单金额
        $totalFee = 1;//$count * $teacher['price'];

        $order->user_id = auth('api')->id();
        $order->teacher_id = $data['teacher_id'];
        $order->order_no = $orderNo;
        $order->total_fee = $totalFee;
        $order->start_at = $startAt;
        $order->end_at = $endAt;
        $order->time_len = $timeLen;
        $order->subject = $data['subject'];
        $order->status = Order::ORDER_PENDING;

        // 存储订单信息
        $orderRes = $order->save();

        // 更新讲师时间状态
        $timeRes = TeachersTime::whereIn('id',$timeIdArr)->update([
            'status' => TeachersTime::STATUS_TIMES_BOOKED
        ]);

        // 存储订单预订的时刻信息
        foreach ($timeIdArr as $v) {
            $obj = [];
            $obj['order_id'] = $order->id;
            $obj['time_id'] = $v;
            $timeArr[] = $obj;
        }
        $orderTimeRes = $order->orderTimes()->insert($timeArr);
        // 获取支付信息
        $payInfo = $this->payService->getPayParams($orderNo, $totalFee);

        if ($orderRes && $timeRes && $orderTimeRes && $payInfo['code'] == 0) {
            // 过期未支付，则取消订单任务
            CloseOrder::dispatch($order);
            DB::commit();
            return ['code' => 0,'msg' => '成功','data' => $payInfo['result'],'order_no' => $orderNo];
        } else {
            DB::rollBack();
            return ['code' => 1,'msg' => '失败','data' => $payInfo['result']];
        }
    }

    /**
     * 重新支付订单
     * @param $orderNo
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function repay($orderNo)
    {
        $order = Order::where('order_no',$orderNo)->first();

        if ($order) {
            $order = json_decode($order,true);
            if ($order['status'] != Order::ORDER_PENDING) {
                return ['code' => 1,'msg' => '订单失效'];
            }
            // 订单重新支付期限内 无需判断时间状态
            // 获取支付信息
            $payInfo = $this->payService->getPayParams($orderNo, $order['total_fee']);

            if ($payInfo['code'] == 0) {
                return ['code' => 0,'msg' => '成功','data' => $payInfo['result'],'order_no' => $orderNo];
            }
        }
        return ['code' => 1,'msg' => '失败'];
    }

    /**
     * 获取订单详情
     * @param $orderNo
     * @return Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function orderInfo($orderNo)
    {
        return Order::with('userInfo')->with('teacher')->with('orderEval')->with('bill')->where('order_no',$orderNo)->first();
    }

    /**
     * 订单完成
     * @param Order $order
     * @throws \Throwable
     */
    public function completeOrder(Order $order)
    {
        $this->order = $order;
        // 订单结束
        // 只有订单状态为已支付 才进行订单关闭的操作
        if ($this->order->status != Order::ORDER_PAID) {
            return;
        }

        DB::transaction(function() {
            // 更新订单状态
            $this->order->status = Order::ORDER_COMPLETED;
            $this->order->save();
            // 修改讲师咨询时长
            $this->order->teacher()->increment('duration',$this->order->time_len);
            // 修改用户咨询时长
            UsersAccount::where('user_id',$this->order->user_id)->increment('duration',$this->order->time_len);
            // 添加入账账单记录
            $billService = new BillService();
            $bill = $billService->saveEntryBill($this->order->order_no,$this->order->teacher_id,$this->order->total_fee);
            // 修改讲师入账中的余额
            $userAccount = UsersAccount::firstOrNew(['user_id' => $this->order->teacher->user_id]);
            $userAccount->account_waiting = $userAccount->account_waiting + $bill['entry_fee'];
            $userAccount->save();

            // 通知服务
            MessageService::orderCompleteMsg($this->order);

//            UsersAccount::where('user_id',$order->teacher->user_id)->increment('account_waiting',$bill['entry_fee']);
            // 讲师分成入账 计划任务 每日定时结算七日前的账单
            return;
        });
    }

    /**
     * 订单到期未支付取消订单
     * @param Order $order
     * @throws \Throwable
     */
    public function orderOutTime(Order $order)
    {
        $this->order = $order;
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->status != Order::ORDER_PENDING) {
            return;
        }
        // 通过事务执行 sql
        DB::transaction(function() {
            // 将订单的 closed 字段标记为 true，即关闭订单
            $this->order->update(['status' => Order::ORDER_INVALID]);
            // 更新讲师时间状态
            $this->restoreTeacherTime();
        });
    }

    public function teacherCancelOrder($orderNo,$remark)
    {
        DB::enableQueryLog();
        $this->order = Order::where('order_no',$orderNo)->whereHas('teacher',function($query) {
            $query->where('user_id',auth('api')->id());
        })->first();
        $sql = DB::getQueryLog();
//        dd($sql);
//        dd($this->order);
        // 判断该讲师是否能操作该订单
        if (!$this->order) {
            return ['code' => 1,'msg' => '未获取到对应订单信息'];
        }

        // 判断订单状态 满足订单状态为已支付状态才能进行取消
        if ($this->order->status != Order::ORDER_PAID) {
            return ['code' => 1,'msg' => '订单状态错误'];
        }
        // TODO 时间判断
        // 通过事务执行 sql
        $exception = DB::transaction(function() use ($orderNo, $remark) {
                // 将订单的更新订单状态
                $this->order->update(['status' => Order::ORDER_TEACHER_CANCEL]);
                // 更新讲师时间状态
                $this->restoreTeacherTime();
                // 讲师信誉分处理
                // 讲师退单次数判断
                $refusedCount = OrderRefuse::where('teacher_id',$this->order->teacher_id)->count();
                if ($refusedCount <= 3) {
                    // 扣除信誉分
                    $this->order->teacher()->decrement('reputation');
                } else {
                    // 扣款处理
                    UsersAccount::where('user_id',$this->order->teacher->user_id)->decrement('account',50);
                }
                // 添加取消记录
                $orderRefuse = new OrderRefuse();
                $orderRefuse->order_id = $this->order->id;
                $orderRefuse->teacher_id = $this->order->teacher_id;
                $orderRefuse->remark = $remark;
                $orderRefuse->save();
                // 发起退款
                $refundNo = Order::getOrderNo(Order::REFUND_PRE_ZIXUN);
                $refundRes = $this->payService->refund($orderNo,$refundNo,$this->order['total_fee'],$this->order['total_fee']);
                if ($refundRes['code'] != 0) {
                    return false;
                }
                // 通知处理
        });

        if (!is_null($exception)) {
            return ['code' => 1,'msg' => '取消失败'];
        } else {
            return ['code' => 0,'msg' => '取消成功'];
        }
    }

    /**
     * 重置讲师时间状态
     * @return mixed
     */
    private function restoreTeacherTime()
    {
        // 更新讲师时间状态
        $timeIdArr = $this->order->orderTimes()->get('time_id');
        if ($timeIdArr) {
            $timeIdArr = json_decode($timeIdArr,true);
            $timeIdArr = array_column($timeIdArr,'time_id');
            return TeachersTime::whereIn('id',$timeIdArr)->update([
                'status' => TeachersTime::STATUS_TIMES_ENABLE
            ]);
        }
    }

}
