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
        $order = Order::with('userInfo')->with('teacher')->where('order_no',$orderNo)->first();

        $orderInfo = json_decode($order,true);
        // 判断订单状态
        if ($orderInfo['status'] != Order::ORDER_PAID) {
            return ['resultcode' => 1,'resultdesc' => '订单无效'];
        }
        // 判断订单时间
        if (time() < $orderInfo['start_at']) {
            return ['resultcode' => 2,'resultdesc' => '预约未开始'];
        }
        if (time() > $orderInfo['end_at']) {
            return ['resultcode' => 3,'resultdesc' => '预约已结束'];
        }

        // 用户电话 主叫电话
        $originNum = $orderInfo['user_info']['phone'];
        // 讲师电话 被叫电话
        $teacherInfo = $order->teacher->makeVisible('phone')->toArray();
        $calleeNum = $teacherInfo['phone'];
        // 最大通话时长，订单结束时间 - 当前时间
        $maxDuration = floor(($orderInfo['end_at'] - time()) / 60);
        // 判断该号码是否已经绑定
        $bindInfo = $this->callService->getAxBindInfo($originNum);
        if ($bindInfo['resultcode'] == 0) { // 该手机号已经进行过绑定
            // 更新绑定时间
            $subscriptionId = $bindInfo['privateNumList'][0]['subscriptionId'];
            $response = $this->callService->updateAxBind($subscriptionId);
        } else { // 该手机号未绑定 新绑定
            $response =  $this->callService->bindAx($originNum,$maxDuration);
        }
        // 设置临时绑定信息
        if ($response['resultcode'] == 0) {
            $response = $this->callService->temporaryCall($response[subscriptionId],$calleeNum);
        }

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

    public function getBindInfo(Request $request)
    {
        $origNum = $request->phone;
        return $this->callService->getAxBindInfo($origNum);
    }

}
