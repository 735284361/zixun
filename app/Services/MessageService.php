<?php

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderNeedTeacherConfirm;
use App\Notifications\OrderPaySuccess;
use App\User;
use Illuminate\Support\Facades\Auth;

class MessageService
{

    public static function paySuccessMsg(Order $order)
    {
        // 模板消息通知
        // 发送支付成功消息
        TempMsgService::paySuccess($order);

        // 系统通知系统
        // 用户 订单支付成功
        $user = User::where('uid',$order['user_id'])->first();
        $user->notify(new OrderPaySuccess($order));
        // 讲师 订单需要确认通知
        $teacherId = $order['teacher_id'];
        $teacher = User::whereHas('teacherInfo' , function($query) use ($teacherId) {
            $query->where('id',$teacherId)->select();
        })->first();
        $teacher->notify(new OrderNeedTeacherConfirm($order));
    }

}
