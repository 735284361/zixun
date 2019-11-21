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

        $refundNumber = Order::getOrderNo(Order::REFUND_PRE_ZIXUN);

        $this->payService->refund($orderNo, $refundNumber, 1, 1);
    }
}
