<?php

namespace App\Http\Controllers\Mini;

use App\Models\Order;
use App\Services\PayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    //

    public $payService;

    public function __construct()
    {
        $this->payService = new PayService();
    }

    public function callback(Request $request)
    {
        $this->payService->callback();
    }

    // 退款
    public function refund(Request $request)
    {
        $orderNo = $request->order_no;
        $refundNo = 'TK'.date('YmdHis').rand(10000,99999);;

        $order = Order::where('order_no',$orderNo)->first()->toArray();

        $payment = \EasyWeChat::payment();
        $result = $payment->refund->byOutTradeNumber($orderNo, $refundNo, 1, 1, [
            // 可在此处传入其他参数，详细参数见微信支付文档
            'refund_desc' => '退运费',
        ]);
        return $result;
    }
}
