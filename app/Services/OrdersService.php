<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Teacher;
use App\Models\TeachersTime;

class OrdersService
{

    public function getOrderNo($pre)
    {

    }

    public function addOrder($data)
    {
        $order = new Order();

        // 获取订单编号
        $orderNo = $order->getOrderNo(Order::ORDER_PRE_ZIXUN);
        // 计算开始结束时间和时长
        $timeIdArr = $data['time_arr'];
        $times = Teacher::with(['teacherTimes'=>function($query,$data) {
            $query->where('status',TeachersTime::STATUS_TIMES_ENABLE)->where('date_at',$data['date_at'])
                ->whereIn('id',$data['time_arr'])->orderBy('start_at','asc')->select();
        }])->where('id',$data['teacher_id'])->first();
        // 判断所选时间是否有效
        $count = count($times['teacher_times']);
        // 上传的时间数量和查出来满足条件的时间数量不等
        if (count($timeIdArr) !== $count) return ['code' => 1,'msg' => '时间数据选择错误'];
        if ($count > 1) {
            for ($i = 0; $i < $count; $i++) {
                // 时间段大于 1
                // 非最后一个时间段
                // 第一个时间段的开结束间等于第二个时间段的开始时间
                if (($i != $count -1) && ($times[$i]['end_at'] != $times[$i+1]['start_at'])) {
                    return ['code' => 1,'msg' => '时间数据选择错误'];
                }
            }
        } else {

        }

        // 更新讲师时间状态
        TeachersTime::whereIn('id',$timeIdArr)->save([
            'status' => TeachersTime::STATUS_TIMES_BOOKED
        ]);

        $startAt = $times[0]['start_at'];
        $endAt = $times[$count - 1]['end_at'];
        $timeLen = ($endAt - $startAt) / 60;

        $order->user_id = auth('api')->id();
        $order->teacher_id = $data['teacher_id'];
        $order->order_no = $orderNo;
        $order->start_at = $startAt;
        $order->end_at = $endAt;
        $order->time_len = $timeLen;
        $order->subject = $data['subject'];

        $order->save();
    }
}
