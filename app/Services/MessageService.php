<?php

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderCompleteToTeacher;
use App\Notifications\OrderCompleteToUser;
use App\Notifications\OrderNeedTeacherConfirm;
use App\Notifications\OrderPaySuccess;
use App\Notifications\TeacherCancelOrderToTacher;
use App\Notifications\TeacherCancelOrderToUser;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageService
{

    /**
     * 支付成功消息通知
     * @param Order $order
     */
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
        return;
    }

    /**
     * 订单完成通知
     * @param Order $order
     */
    public static function orderCompleteMsg(Order $order)
    {
        // 系统通知系统
        // 用户 订单支付成功
        $user = User::where('uid',$order['user_id'])->first();
        $user->notify(new OrderCompleteToUser($order));
        // 讲师 订单需要确认通知
        $teacherId = $order['teacher_id'];
        $teacher = User::whereHas('teacherInfo' , function($query) use ($teacherId) {
            $query->where('id',$teacherId)->select();
        })->first();
        $teacher->notify(new OrderCompleteToTeacher($order));
        return;
    }

    public static function teacherCancelOrderMsg(Order $order)
    {
        // 系统通知系统
        // 用户 订单支付成功
        $user = User::where('uid',$order['user_id'])->first();
        $user->notify(new TeacherCancelOrderToUser($order));
        // 讲师 订单需要确认通知
        $teacherId = $order['teacher_id'];
        $teacher = User::whereHas('teacherInfo' , function($query) use ($teacherId) {
            $query->where('id',$teacherId)->select();
        })->first();
        $teacher->notify(new TeacherCancelOrderToTacher($order));
        return;
    }

}
