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
    protected $privateNum = '17160095983'; // X号码(隐私号码)

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
//        $originNum = $orderInfo['user_info']['phone'];
        // 讲师电话 被叫电话
        $teacherInfo = $order->teacher->makeVisible('phone')->toArray();
        $originNum = $teacherInfo['phone'];
        // 最大通话时长，订单结束时间 - 当前时间
        $maxDuration = floor(($orderInfo['end_at'] - time()) / 60);
        // 判断该号码是否已经绑定
        $bindInfo = $this->callService->getAxBindInfo($originNum);
        if ($bindInfo['resultcode'] == 0) { // 该手机号已经进行过绑定
            // 更新绑定时间
            $subscriptionId = $bindInfo['privateNumList'][0]['subscriptionId'];
            $response = $this->callService->updateAxBind($subscriptionId,$maxDuration);
            if ($response['resultcode'] == 0) {
                $response['origNum'] = $bindInfo['privateNumList'][0]['origNum'];
                $response['privateNum'] = $bindInfo['privateNumList'][0]['privateNum'];
                $response['subscriptionId'] = $bindInfo['privateNumList'][0]['subscriptionId'];
            }
        } else { // 该手机号未绑定 新绑定
            $response =  $this->callService->bindAx($originNum,$maxDuration);
        }
        // 设置临时绑定信息
        if ($response['resultcode'] == 0) {
//            $tempBind = $this->callService->temporaryCall($response['subscriptionId'],$calleeNum);
            // 更新隐私小号使用状态
//            if ($tempBind['resultcode'] == 0) {
                BindRecord::updateOrCreate(['order_no' => $orderNo],[
                    'order_no' => $orderNo,
                    'origin_num' => $response['origNum'],
                    'private_num' => $response['privateNum'],
                    'subscription_id' => $response['subscriptionId']
                ]);
//            }
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

    public function temporaryCall()
    {
        return $this->callService->temporaryCall('17600296638', '+8617160095983','17713267173');
    }

    public function onCallEvent()
    {
        $jsonBody = json_encode([
            'eventType' => 'disconnect',
            'statusInfo' => [
                'sessionId' => '1200_1827_4294967295_20190124023003@callenabler246.huaweicaas.com',
                'timestamp' => '2019-01-24 02:30:22',
                'caller' => '+8613800000022',
                'called' => '+8613866887021',
                'stateCode' => 0,
                'stateDesc' => 'The user releases the call.',
                'subscriptionId' => '1d39eaef-9279-4d18-b806-72e43ab3f85c'
            ]
        ]);

        $this->callService->onCallEvent($jsonBody);
    }

}
