<?php

namespace App\Services;

use App\Models\Order;
use App\Models\UsersSub;
use Illuminate\Support\Facades\DB;

class TempMsgService
{

    const PAY_SUCCESS = 'tN10Ow_Cxu2d90Y6DOYpkYMVLlO0JZFrc2tcLYWIJgU';

    public static function paySuccess(Order $order)
    {
        $user = UsersSub::where('uid',$order->uid)->first()->toArray();
        $openId = $user['open_id'];
        $oderNo = $order->order_no;
        $payLog = DB::table('zx_pay_log')->where('order_no',$oderNo)->first()->toArray();
        $prepayId = $payLog['prepay_id'];

        $app = \EasyWeChat::miniProgram();

        $startAt = date('Y-m-d H:i',$order->start_at);
//        return $app->template_message->send([
//            'touser' => $openId,
//            'template_id' => self::PAY_SUCCESS,
//            'page' => 'index',
//            'form_id' => $prepayId,
//            'data' => [
//                'keyword1' => $order->order_no,
//                'keyword2' => 'VALUE2',
//                'keyword3' => $order->total_fee,
//                'keyword4' => $startAt,
//                'keyword5' => $order->time_len.'分钟',
//            ],
//        ]);
    }

}
