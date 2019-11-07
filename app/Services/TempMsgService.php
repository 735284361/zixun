<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PayLog;
use App\Models\UsersSub;
use App\Notifications\Test;
use Illuminate\Support\Facades\Log;

class TempMsgService
{

    const PAY_SUCCESS = 'tN10Ow_Cxu2d90Y6DOYpkYMVLlO0JZFrc2tcLYWIJgU'; // 支付成功

    public function paySuccess(Order $order)
    {
        // 转换为数组对象
        $order = json_decode($order,true);
        $user = UsersSub::where('uid',$order['user_id'])->where('since_from',USER_SINCE_FROM_ZIXUN)->first()->toArray();
        $payLog = PayLog::where('order_no',$order['order_no'])->first()->toArray();

        $app = \EasyWeChat::miniProgram();

        $startAt = date('Y-m-d H:i',$order['start_at']);

        // 通知系统
        try {
            $order->notify(new Test($order));
        } catch (\Exception $e) {
            Log::warning('通知消息发送失败：'.$e->getMessage());
        }

        try {
            $app->template_message->send([
                'touser' => $user['open_id'],
                'template_id' => self::PAY_SUCCESS,
                'page' => 'index',
                'form_id' => $payLog['prepay_id'],
                'data' => [
                    'keyword1' => $order['order_no'], // 订单编号
                    'keyword2' => '一对一咨询', // 订单内容
                    'keyword3' => $order['total_fee'], // 订单金额
                    'keyword4' => $startAt, // 开始时间
                    'keyword5' => $order['time_len'].'分钟', // 预计时长
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('模板消息发送失败：'.$e->getMessage());
        }
    }

}
