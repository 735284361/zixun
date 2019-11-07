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
//        TempMsgService::paySuccess($order);


        // 系统通知系统
        // 用户 订单支付成功
        Auth::user()->notify(new OrderPaySuccess($order));
        // 讲师 订单需要确认通知
        $teacher = $order->with('teacher')->first()->toArray();
        $user = User::where('uid',$teacher['teacher']['user_id'])->first();
        $user->notify(new OrderNeedTeacherConfirm($order));
    }

}
