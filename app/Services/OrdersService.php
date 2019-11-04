<?php

namespace App\Services;

use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\Teacher;
use App\Models\TeachersTime;
use Illuminate\Support\Facades\DB;

class OrdersService
{

    protected $payService;

    public function __construct()
    {
        $this->payService = new PayService();
    }

    public function addOrder($data)
    {
        $order = new Order();

        // 获取订单编号
        $orderNo = $order->getOrderNo(Order::ORDER_PRE_ZIXUN);
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

        DB::beginTransaction();

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

        if ($orderRes && $timeRes && $orderTimeRes) {
            // 过期未支付，则取消订单任务
            CloseOrder::dispatch($order);
            DB::commit();
            return ['code' => 0,'msg' => '订单成功','data' => $payInfo];
        } else {
            DB::rollBack();
            return ['code' => 1,'msg' => '订单失败'];
        }
    }
}
