<?php

namespace App\Http\Controllers\Mini;

use App\Models\CallBindRecord;
use App\Models\Order;
use App\Services\CallRecordService;
use App\Services\CallService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CallController extends Controller
{
    //
    protected $callService;
    protected $callRecordService;
    protected $relationNum = '+8617090000944';

    public function __construct()
    {
        $this->callService = new CallService();
        $this->callRecordService = new CallRecordService();
    }

    /**
     * 电话绑定
     * @param Request $request
     * @return array|bool|false|mixed|string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function bindAx(Request $request)
    {
        $this->validate($request,['order_no' => 'required']);

        $orderNo = $request->order_no;
        // 获取订单信息
        $order = Order::with('userInfo')->with('teacher')->where('order_no',$orderNo)->first();

        $orderInfo = json_decode($order,true);
        // 判断订单状态
//        if ($orderInfo['status'] != Order::ORDER_PAID) {
//            return ['resultcode' => 1,'resultdesc' => '订单无效'];
//        }
//        // 判断订单时间
//        if (time() < $orderInfo['start_at']) {
//            return ['resultcode' => 2,'resultdesc' => '预约未开始'];
//        }
//        if (time() > $orderInfo['end_at'] - 60) { // 提前一分钟绑定
//            return ['resultcode' => 3,'resultdesc' => '预约已结束'];
//        }

        // 用户电话 主叫电话
        $callerNum = $orderInfo['user_info']['phone'];
        // 讲师电话 被叫电话
        $teacherInfo = $order->teacher->makeVisible('phone')->toArray();
        $calleeNum = $teacherInfo['phone'];
        $relationNum = $this->relationNum;
        // 最大通话时长，订单结束时间 - 当前时间
        $maxDuration = floor(($orderInfo['end_at'] - time()) / 60);
        $duration = 60; // 每分钟失效 失效后重新绑定进行通话
        // 查询绑定信息
        // 一个订单只能对应一条绑定记录 如果存在则更新绑定
        $bindRecord = CallBindRecord::where('order_no',$orderNo)->first();
        if ($bindRecord !== null) { // 判断是否有绑定记录
            $bindRecord = json_decode($bindRecord, true);
            $subscriptionId = $bindRecord['subscription_id'];
            // 如果有绑定记录 判断绑定状态 避免自动被解绑的情况
            $bindInfo = $this->callService->getAxBindInfo($subscriptionId);
            if ($bindInfo['resultcode'] == 0) {
                // 该手机号已经进行过绑定 则更新绑定
                $bindType = 'updateBind';
                $response = $this->callService->updateAxBind($subscriptionId, $maxDuration);
                $response['resultcode'] == 0 ? $response['relationNum'] = $relationNum : '';
            } else {
                // 该绑定已失效 重新进行绑定
                $bindType = 'reBind';
                $response =  $this->callService->bindAx($callerNum, $relationNum, $calleeNum, $duration, $maxDuration);
            }
        } else {
            // 不存在绑定关系 则进行新增绑定
            $bindType = 'newBind';
            $response =  $this->callService->bindAx($callerNum, $relationNum, $calleeNum, $duration, $maxDuration);
        }
        // 更新绑定记录
        $callData = $this->callService->getCallData();
        $data = array_merge($callData, $response);
        $this->callRecordService->addBindRecord($orderNo, $data, $bindType);
        return $response;
    }

    public function cancelAxBind(Request $request)
    {
        $subscriptionId = $request->id;
        return $this->callService->cancelAxBind($subscriptionId);
    }

    public function getBindInfo(Request $request)
    {
        $id = $request->id;
        return $this->callService->getAxBindInfo($id);
    }

    public function getBindInfos()
    {
        return $this->callService->getAxBindInfos($this->relationNum);
    }

    public function onCallEvent(Request $request)
    {
        $jsonBody = $request->all();
        $this->callService->onCallEvent($jsonBody);
    }

    public function onFeeEvent(Request $request)
    {
//        $jsonBody = json_encode([
//            'eventType' => 'fee',
//            'feeLst' => [
//                [
//                    'direction' => 1,
//                    'spId' => 'linlingoo_omp',
//                    'appKey' => 'V1Z96521zFr3vxe5N2A1UJ1sQ1WP',
//                    'icid' => 'ba171f34e6953fcd751edc77127748f4.3757223714.337238282.9',
//                    'bindNum' => '+8613800000022',
//                    'sessionId' => '1200_1029_4294967295_20190123091514@callenabler246.huaweicaas.com',
//                    'subscriptionId' => 'e97b2863-e7ad-4b4c-87c0-91b0171fe803',
//                    'callerNum' => '+8613800000021',
//                    'calleeNum' => '+8613800000022',
//                    'fwdDisplayNum' => '+8613800000022',
//                    'fwdDstNum' => '+8613866887021',
//                    'callInTime' => '2019-01-23 09:15:14',
//                    'fwdStartTime' => '2019-01-23 09:15:15',
//                    'fwdAlertingTime' => '2019-01-23 09:15:21',
//                    'fwdAnswerTime' => '2019-01-23 09:15:36',
//                    'callEndTime' => '2019-01-23 09:16:41',
//                    'fwdUnaswRsn' => 0,
//                    'ulFailReason' => 0,
//                    'sipStatusCode' => 0,
//                    'callOutUnaswRsn' => 0,
//                    'recordFlag' => 1,
//                    'recordStartTime' => '2019-01-23 09:15:37',
//                    'recordDomain' => 'ostor.huawei.com',
//                    'recordBucketName' => 'sp-v1z96521zfr3vxe5n2a1uj1sq1wp',
//                    'recordObjectName' => '19012309153712050118304.wav',
//                    'ttsPlayTimes' => 0,
//                    'ttsTransDuration' => 0,
//                    'mptyId' => 'e97b2863-e7ad-4b4c-87c0-91b0171fe803',
//                    'serviceType' => '004',
//                    'hostName' => 'callenabler246.huaweicaas.com'
//                ],
//                [
//                    'direction' => 1,
//                    'spId' => 'linlingoo_omp',
//                    'appKey' => 'V1Z96521zFr3vxe5N2A1UJ1sQ1WP',
//                    'icid' => 'ba171f34e6953fcd751edc77127748f4.3757223714.337238282.9',
//                    'bindNum' => '+8613800000022',
//                    'sessionId' => '1200_1029_4294967295_20190123091514@callenabler246.huaweicaas.com',
//                    'subscriptionId' => 'e97b2863-e7ad-4b4c-87c0-91b0171fe803',
//                    'callerNum' => '+8613800000021',
//                    'calleeNum' => '+8613800000022',
//                    'fwdDisplayNum' => '+8613800000022',
//                    'fwdDstNum' => '+8613866887021',
//                    'callInTime' => '2019-01-23 09:15:14',
//                    'fwdStartTime' => '2019-01-23 09:15:15',
//                    'fwdAlertingTime' => '2019-01-23 09:15:21',
//                    'fwdAnswerTime' => '2019-01-23 09:15:36',
//                    'callEndTime' => '2019-01-23 09:16:41',
//                    'fwdUnaswRsn' => 0,
//                    'ulFailReason' => 0,
//                    'sipStatusCode' => 0,
//                    'callOutUnaswRsn' => 0,
//                    'recordFlag' => 1,
//                    'recordStartTime' => '2019-01-23 09:15:37',
//                    'recordDomain' => 'ostor.huawei.com',
//                    'recordBucketName' => 'sp-v1z96521zfr3vxe5n2a1uj1sq1wp',
//                    'recordObjectName' => '19012309153712050118304.wav',
//                    'ttsPlayTimes' => 0,
//                    'ttsTransDuration' => 0,
//                    'mptyId' => 'e97b2863-e7ad-4b4c-87c0-91b0171fe803',
//                    'serviceType' => '004',
//                    'hostName' => 'callenabler246.huaweicaas.com'
//                ]
//            ]
//        ]);

        $jsonBody = $request->all();
        $this->callService->onFeeEvent($jsonBody);
    }

}
