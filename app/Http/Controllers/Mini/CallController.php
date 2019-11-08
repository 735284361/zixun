<?php

namespace App\Http\Controllers\Mini;

use App\Models\BindRecord;
use App\Models\Order;
use App\Services\CallService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CallController extends Controller
{
    //

    protected $callService;

    protected $origNum = '17600296638'; // A号码
    protected $privateNum = '+8617160095983'; // X号码(隐私号码)

    public function __construct()
    {
        $this->callService = new CallService();
    }

    public function bindAx(Request $request)
    {
        $this->validate($request,['order_no' => 'required']);

        $orderNo = $request->order_no;
        // 获取订单信息
        $order = Order::with('userInfo')
            ->with('phoneBindInfo')->where('order_no',$orderNo)->first()->toArray();

        $originNum = $order['user_info']['phone'];
        // 判断该号码是否已经绑定
        $bindInfo = $this->callService->getAxBindInfo($originNum);
        if ($bindInfo['resultcode'] == 0) {
            return $bindInfo;
        }

//        // 判断订单状态
//        if ($order['status'] != Order::ORDER_PAID) {
//            return ['resultcode' => 1,'resultdesc' => '订单无效'];
//        }
//
//        // 判断订单时间
//        if (time() < $order['start_at']) {
//            return ['resultcode' => 2,'resultdesc' => '预约未开始'];
//        }
//        if (time() > $order['end_at']) {
//            return ['resultcode' => 3,'resultdesc' => '预约已结束'];
//        }

        $response =  $this->callService->bindAx($originNum, $order['time_len']);

        if ($response['resultcode'] == 0) {
            BindRecord::updateOrCreate(['order_no' => $orderNo],[
                'order_no' => $orderNo,
                'origin_num' => $response['origNum'],
                'private_num' => $response['privateNum'],
                'subscription_id' => $response['subscriptionId']
            ]);
        }

        return $response;
    }

    public function cancelAxBind(Request $request)
    {
        $subscriptionId = $request->id;
        return $this->callService->cancelAxBind($subscriptionId);
    }

    public function getBindInfo()
    {
//        $origNum = "+8617600296638";
        $origNum = '+8618210889173';

        $response = $this->callService->getAxBindInfo($origNum);
        dd($response);
    }

}
