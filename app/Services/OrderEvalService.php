<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderEval;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class OrderEvalService
{

    /**
     * 订单评论
     * @param $data
     * @return bool
     */
    public function saveOrderEval($data)
    {
        // 判断订单 用户、状态
        $order = Order::where([
            'order_no'=>$data['order_no'],
            'user_id'=>auth('api')->id(),
            'status'=>Order::ORDER_COMPLETED
        ])->first();
        if (!$order) {
            return false;
        }

        $orderInfo = json_decode($order, true);
        // 判断是否已经评论过
        if (OrderEval::where('order_id',$orderInfo['id'])->exists()) {
            return false;
        }

        DB::transaction(function() use ($orderInfo, $data) {

            // 添加订单记录
            $orderEval = new OrderEval();
            $orderEval->order_id = $orderInfo['id'];
            $orderEval->user_id = auth('api')->id();
            $orderEval->teacher_id = $orderInfo['teacher_id'];
            $orderEval->attitude = $data['attitude'];
            $orderEval->speciality = $data['speciality'];
            $orderEval->satisfaction = $data['satisfaction'];
            $orderEval->content = $data['content'];
            $orderEval->save();

            // 更新讲师评分
            $score = $data['attitude'] + $data['speciality'] + $data['speciality'];
            Teacher::where('id',$orderInfo['teacher_id'])->increment('score',$score);
            Teacher::where('id',$orderInfo['teacher_id'])->increment('number_reputation',3);
        });
        return true;
    }

}
