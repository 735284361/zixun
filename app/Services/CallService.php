<?php

namespace App\Services;

use App\Models\BindRecord;
use App\Models\PrivatePhone;
use Illuminate\Support\Facades\Log;

class CallService
{

    // 必填,请参考"开发准备"获取如下数据,替换为实际值
    protected $realUrl = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/provision/caas/privatenumber/v1.0'; // APP接入地址+接口访问URI
    protected $APP_KEY = 'v7k73374fefp3ZgIPS9K7s6ZJ1Rc'; // APP_Key`
    protected $APP_SECRET = 'f725AtijRS02g8rHW92X4f7G7XRG'; // APP_Secret
//    public $origNum = '+8617600296638'; // A号码
//    public $privateNum = '+8617160095983'; // X号码(隐私号码)

    /*
     * 选填,各参数要求请参考"AX模式绑定接口"
     */
    // $privateNumType = 'mobile-virtual'; //固定为mobile-virtual
    // $areaCode = '0755'; //需要绑定的X号码对应的城市码
    protected $recordFlag = 'true'; //是否需要针对该绑定关系产生的所有通话录音
    protected $recordHintTone = '001.wav'; //设置录音提示音
    protected $calleeNumDisplay = '0'; // 设置非A用户呼叫X时,A接到呼叫时的主显号码
    // $privateSms = 'true'; //设置该绑定关系是否支持短信功能
    protected $lastMinVoice = '001.wav'; //设置该绑定关系是否支持短信功能

    protected $callerHintTone = '001.wav'; //设置A拨打X号码时的通话前等待音
    protected $calleeHintTone = '001.wav'; //设置非A用户拨打X号码时的通话前等待音
//    public $preVoice = array(
//         'callerHintTone' => $this->callerHintTone,
//         'calleeHintTone' => $this->calleeHintTone
//    );

    private static function setNum($num) {
        return "+86".$num;
    }

    /**
     * @param $origNum 需要绑定的 A手机号
     * @param $maxDuration 最大通话时长
     * @return array|bool|false|mixed|string
     */
    public function bindAx($origNum, $maxDuration){
        $origNum = self::setNum($origNum);
        // 请求Body,可按需删除选填参数
        $privatePhone = PrivatePhone::where('status',PrivatePhone::STATUS_ENABLE)->first();
        if (!$privatePhone) {
            return ['resultcode' => 1,'msg' => '线路不足'];
        }
        $privatePhone = json_decode($privatePhone,true);
        $privateNum = $privatePhone['phone'];
        $preVoice = [
            'callerHintTone' => $this->callerHintTone,
            'calleeHintTone' => $this->calleeHintTone
        ];
        $data = json_encode([
            'origNum' => $origNum,
            'privateNum' => $privateNum,
            // 'privateNumType' => $privateNumType,
            // 'areaCode' => $areaCode,
            'recordFlag' => $this->recordFlag,
            'recordHintTone' => $this->recordHintTone,
            'calleeNumDisplay' => $this->calleeNumDisplay,
//            'privateSms' => $this->privateSms,
            'preVoice' => $preVoice,
            'maxDuration' => $maxDuration, // 单次通话进行的最长时间，单位为分钟
            'lastMinVoice' => $this->lastMinVoice
        ]);
        $response = $this->getResponse('POST',$data);
        // 更新小号使用状态
        if ($response['resultcode'] == 0) {
            $phone = PrivatePhone::where('phone',$privateNum)->first();
            $phone->status = PrivatePhone::STATUS_USED;
            $phone->save();
        }
        return $response;
    }

    /**
     * 临时通话接口
     * @param $subscriptionId
     * @param $calleeNum
     * @param $duration
     * @return bool|false|string
     */
    public function temporaryCall($subscriptionId, $calleeNum, $duration = 60)
    {
        $fullUrl = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/caas/privatenumber/calleenumber/v1.0';
        // 请求Body,可按需删除选填参数
        $data = json_encode([
            'subscriptionId' => $subscriptionId,
            'calleeNum' => $calleeNum,
            'duration' => $duration // 临时被叫关系保持时间，单位为秒
        ]);

        $response = $this->getResponse('PUT', $data, $fullUrl);
        return $response;
    }

    /**
     * 取消绑定
     * @param $subscriptionId
     * @return bool|false|mixed|string
     */
    public function cancelAxBind($subscriptionId)
    {
        // 请求URL参数
        $data = http_build_query([
             'subscriptionId' => $subscriptionId
        ]);
        // 完整请求地址
        $fullUrl = $this->realUrl . '?' . $data;
        $response = $this->getResponse('DELETE',null, $fullUrl);
        // 更新小号使用状态
        if ($response['resultcode'] == 0) {
            $privateNum = BindRecord::where('subscription_id',$subscriptionId)->first()->toArray();
            $phone = PrivatePhone::where('phone',$privateNum['private_num'])->first();
            $phone->status = PrivatePhone::STATUS_ENABLE;
            $phone->save();
        }
        return $response;
    }

    /**
     * 更新绑定
     * @param $subscriptionId
     * @param $maxDuration
     * @return bool|false|string
     */
    public function updateAxBind($subscriptionId, $maxDuration)
    {
        // 请求Body,可按需删除选填参数
        $data = json_encode([
            'subscriptionId' => $subscriptionId,
            'maxDuration' => $maxDuration
        ]);
        $response = $this->getResponse('PUT',$data);
        return $response;
    }

    /**
     * 获取绑定信息
     * @param $origNum
     * @return bool|false|string
     */
    public function getAxBindInfo($origNum)
    {
        $origNum = self::setNum($origNum);
        // 请求URL参数
        $data = http_build_query([
            'origNum' => $origNum,
        ]);
        // 完整请求地址
        $fullUrl = $this->realUrl . '?' . $data;

        $response = $this->getResponse('GET', $data, $fullUrl);
        return $response;
    }

    /**
     * 呼叫事件通知接口
     * @param $jsonBody
     */
    public function onCallEvent($jsonBody)
    {
        $jsonArr = json_decode($jsonBody, true); //将通知消息解析为关联数组
        $eventType = $jsonArr['eventType']; //通知事件类型

        Log::info('AX_CALL:EventType error:'.$eventType);
        if (strcasecmp($eventType, 'fee') == 0) {
            return;
        }

        if (!array_key_exists('statusInfo', $jsonArr)) {
            Log::info('AX_CALL:param error: no statusInfo.');
            return;
        }
        $statusInfo = $jsonArr['statusInfo']; //呼叫状态事件信息

        //callin：呼入事件
        if (strcasecmp($eventType, 'callin') == 0) {
            /**
             * Example: 此处以解析sessionId为例,请按需解析所需参数并自行实现相关处理
             *
             * 'timestamp': 呼叫事件发生时隐私保护通话平台的UNIX时间戳
             * 'sessionId': 通话链路的标识ID
             * 'caller': 主叫号码
             * 'called': 被叫号码
             * 'subscriptionId': 绑定关系ID
             */
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //callout：呼出事件
        if (strcasecmp($eventType, 'callout') == 0) {
            /**
             * Example: 此处以解析sessionId为例,请按需解析所需参数并自行实现相关处理
             *
             * 'timestamp': 呼叫事件发生时隐私保护通话平台的UNIX时间戳
             * 'sessionId': 通话链路的标识ID
             * 'caller': 主叫号码
             * 'called': 被叫号码
             * 'subscriptionId': 绑定关系ID
             */
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //alerting：振铃事件
        if (strcasecmp($eventType, 'alerting') == 0) {
            /**
             * Example: 此处以解析sessionId为例,请按需解析所需参数并自行实现相关处理
             *
             * 'timestamp': 呼叫事件发生时隐私保护通话平台的UNIX时间戳
             * 'sessionId': 通话链路的标识ID
             * 'caller': 主叫号码
             * 'called': 被叫号码
             * 'subscriptionId': 绑定关系ID
             */
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //answer：应答事件
        if (strcasecmp($eventType, 'answer') == 0) {
            /**
             * Example: 此处以解析sessionId为例,请按需解析所需参数并自行实现相关处理
             *
             * 'timestamp': 呼叫事件发生时隐私保护通话平台的UNIX时间戳
             * 'sessionId': 通话链路的标识ID
             * 'caller': 主叫号码
             * 'called': 被叫号码
             * 'subscriptionId': 绑定关系ID
             */
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //disconnect：挂机事件
        if (strcasecmp($eventType, 'disconnect') == 0) {
            /**
             * Example: 此处以解析sessionId为例,请按需解析所需参数并自行实现相关处理
             *
             * 'timestamp': 呼叫事件发生时隐私保护通话平台的UNIX时间戳
             * 'sessionId': 通话链路的标识ID
             * 'caller': 主叫号码
             * 'called': 被叫号码
             * 'stateCode': 通话挂机的原因值
             * 'stateDesc': 通话挂机的原因值的描述
             * 'subscriptionId': 绑定关系ID
             */
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
    }

    /**
    * 话单通知
    * @desc 详细内容以接口文档为准
    * @param jsonArr
    */
    function onFeeEvent($jsonBody) {
        $jsonArr = json_decode($jsonBody, true); //将通知消息解析为关联数组
        $eventType = $jsonArr['eventType']; //通知事件类型

        if (strcasecmp($eventType, 'fee') != 0) {
            print_r('EventType error: ' . $eventType);
            return;
        }

        if (!array_key_exists('feeLst', $jsonArr)) {
            print_r('param error: no feeLst.');
            return;
        }
        $feeLst = $jsonArr['feeLst']; //呼叫话单事件信息

        print_r('eventType: ' . $eventType . PHP_EOL); //打印通知事件类型
        /**
         * Example: 此处以解析sessionId为例,请按需解析所需参数并自行实现相关处理
         *
         * 'direction': 通话的呼叫方向
         * 'spId': 客户的云服务账号
         * 'appKey': 商户应用的AppKey
         * 'icid': 呼叫记录的唯一标识
         * 'bindNum': 隐私保护号码
         * 'sessionId': 通话链路的唯一标识
         * 'callerNum': 主叫号码
         * 'calleeNum': 被叫号码
         * 'fwdDisplayNum': 转接呼叫时的显示号码
         * 'fwdDstNum': 转接呼叫时的转接号码
         * 'callInTime': 呼入的开始时间
         * 'fwdStartTime': 转接呼叫操作的开始时间
         * 'fwdAlertingTime': 转接呼叫操作后的振铃时间
         * 'fwdAnswerTime': 转接呼叫操作后的应答时间
         * 'callEndTime': 呼叫结束时间
         * 'fwdUnaswRsn': 转接呼叫操作失败的Q850原因值
         * 'failTime': 呼入,呼出的失败时间
         * 'ulFailReason': 通话失败的拆线点
         * 'sipStatusCode': 呼入,呼出的失败SIP状态码
         * 'recordFlag': 录音标识
         * 'recordStartTime': 录音开始时间
         * 'recordObjectName': 录音文件名
         * 'recordBucketName': 录音文件所在的目录名
         * 'recordDomain': 存放录音文件的域名
         * 'serviceType': 携带呼叫的业务类型信息
         * 'hostName': 话单生成的服务器设备对应的主机名
         * 'subscriptionId': 绑定关系ID
         */
        //短时间内有多个通话结束时隐私保护通话平台会将话单合并推送,每条消息最多携带50个话单
        if (sizeof($feeLst) > 1) {
            foreach ($feeLst as $loop){
                if (array_key_exists('sessionId', $loop)) {
                    print_r('sessionId: ' . $loop['sessionId'] . PHP_EOL);
                }
            }
        } else if(sizeof($feeLst) == 1) {
            if (array_key_exists('sessionId', $feeLst[0])) {
                print_r('sessionId: ' . $feeLst[0]['sessionId'] . PHP_EOL);
            }
        } else {
            print_r('feeLst error: no element.');
        }
    }

    /**
     * 获取头部
     * @return array
     */
    private function getHeader()
    {
        return $headers = [
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8',
            'Authorization: WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
            'X-WSSE: ' . $this->buildWsseHeader($this->APP_KEY, $this->APP_SECRET)
        ];
    }

    /**
     * @param $method
     * @param $data
     * @return array
     */
    private function getContextOptions($method, $data = null)
    {
        $headers = $this->getHeader();

        $context_options = [
            'http' => [
                'method' => $method, // 请求方法为PUT
                'header' => $headers,
                'ignore_errors' => true // 获取错误码,方便调测
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ] // 为防止因HTTPS证书认证失败造成API调用失败,需要先忽略证书信任问题
        ];

        $data == null ? '' : $context_options['http']['content'] = $data;

        return $context_options;
    }

    /**
     * 构建X-WSSE值
     *
     * @param string $appKey
     * @param string $appSecret
     * @return string
     */
    private function buildWsseHeader($appKey, $appSecret) {
        date_default_timezone_set("UTC");
        $Created = date('Y-m-d\TH:i:s\Z'); //Created
        $nonce = uniqid(); //Nonce
        $base64 = base64_encode(hash('sha256', ($nonce . $Created . $appSecret), TRUE)); //PasswordDigest

        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", $appKey, $base64, $nonce, $Created);
    }

    /**
     * 获取响应结果
     * @param $method
     * @param null $data
     * @param null $realUrl
     * @return bool|false|string
     */
    private function getResponse($method, $data = null, $realUrl = null)
    {
        $realUrl == null ? $realUrl = $this->realUrl : '';

        $context_options = $this->getContextOptions($method,$data);
        try {
            $response = file_get_contents($realUrl, false, stream_context_create($context_options)); // 发送请求
            $response = json_decode($response,true);
        } catch (Exception $e) {
            $response['resultcode'] = 1;
            $response['resultdesc'] = $e->getMessage();
        }
        return $response;
    }

}
