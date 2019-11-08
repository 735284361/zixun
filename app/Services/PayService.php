<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PayLog;
use App\Models\UsersSub;
use Illuminate\Support\Facades\Log;

class PayService
{

    /**
     * 支付统一接口
     * @param $orderNo
     * @param $totalFee
     * @param null $body
     * @return array|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPayParams($orderNo, $totalFee, $body = null)
    {
        $body == null ? $body = 'HR百科互助社' : '';

        $user = UsersSub::where('uid',auth('api')->id())->where('since_from',USER_SINCE_FROM_ZIXUN)->first()->toArray();
        $openId = $user['open_id'];

        $payment = \EasyWeChat::payment();
        $result = $payment->order->unify([
            'body' => $body,
            'out_trade_no' => $orderNo,
            'total_fee' => $totalFee,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $openId,
        ]);

        if ($result['return_code'] == 'FAIL' || $result['result_code'] == 'FAIL') {
            $data['code'] = 1;
            $data['result']  = $result;
            return $data;
        } else {
            $jssdk = $payment->jssdk;
            PayLog::updateOrCreate(
                ['order_no' => $orderNo],
                ['order_no' => $orderNo,'prepay_id' => $result['prepay_id']]
            );
            $data['code'] = 0;
            $data['result'] = $jssdk->bridgeConfig($result['prepay_id'],false);
            return $data;
        }
    }

    public function callback()
    {
        Log::warning('start');
        $payment = \EasyWeChat::payment();

        $response = $payment->handlePaidNotify(function($message, $fail){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::where('order_no',$message['out_trade_no'])->first();
            $data = json_decode($order,true);

            if (!$data || $data['status'] == Order::ORDER_PAID) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    // 用户是否支付成功
                    $order->status = Order::ORDER_PAID;
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    // 用户支付失败
                    $order->status = Order::ORDER_PAID_FAIL;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            // 进入消息发送系统
            MessageService::paySuccessMsg($order);

            return true; // 返回处理完成
        });

        return $response;
    }

}
