<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CallService
{

    // 必填,请参考"开发准备"获取如下数据,替换为实际值
    protected $realUrl = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/caas/relationnumber/partners/v1.0'; // APP接入地址+接口访问URI
    protected $APP_KEY = 'Q54HD4UB1ZcFb83zuCesh8nPNL6j'; // APP_Key`
    protected $APP_SECRET = '7l7sSH5HJSKr1eNOxU0GO1wRp9yy'; // APP_Secret

    protected $relationNum = '+8617010000001'; // X号码(隐私号码)
    protected $callerNum = '+8618612345678'; // A号码
    protected $calleeNum = '+8618612345679'; // B号码

    /*
     * 选填,各参数要求请参考"AXB模式绑定接口"
     */
    // $areaCode = '0755'; // 需要绑定的X号码对应的城市码
    // $callDirection = 0; // 允许呼叫的方向
    // $duration = 86400; // 绑定关系保持时间,单位为秒。到期后会被系统自动解除绑定关系
    // $recordFlag = 'false'; // 是否需要针对该绑定关系产生的所有通话录音
    // $recordHintTone = 'recordHintTone.wav'; // 设置录音提示音
    // $maxDuration = 60; // 设置允许单次通话进行的最长时间,单位为分钟。通话时间从接通被叫的时刻开始计算
    protected $lastMinVoice = '001.wav'; // 设置通话剩余最后一分钟时的提示音
    // $privateSms = 'true'; // 设置该绑定关系是否支持短信功能

    protected $callerHintTone = '001.wav'; // 设置A拨打X号码时的通话前等待音
    protected $calleeHintTone = '001.wav'; // 设置B拨打X号码时的通话前等待音
    // $preVoice = [
    //     'callerHintTone' => $callerHintTone,
    //     'calleeHintTone' => $calleeHintTone
    // ];

    private static function setNum($num) {
        return "+86".$num;
    }

    protected $callData;

    public function getCallData()
    {
        return json_decode($this->callData,true);
    }

    /**
     * 绑定通话
     * @param $callerNum 需要绑定的 A手机号
     * @param $relationNum 需要绑定的 X手机号
     * @param $calleeNum 需要绑定的 B手机号
     * @param $duration 绑定关系保持时间
     * @param $maxDuration 最大通话时长
     * @return array|bool|false|mixed|string
     */
    public function bindAx($callerNum, $relationNum, $calleeNum, $duration, $maxDuration){
        $callerNum = self::setNum($callerNum);
        $calleeNum = self::setNum($calleeNum);
        // 请求Body,可按需删除选填参数
        $preVoice = [
            'callerHintTone' => $this->callerHintTone,
            'calleeHintTone' => $this->calleeHintTone
        ];
        $data = json_encode([
            'callerNum' => $callerNum, // AXB中的A号码。
            'relationNum' => $relationNum, // 指定已申请到的X号码进行绑定。
            'calleeNum' => $calleeNum, // AXB中的B号码。
            // 'areaCode' => $areaCode, // 指定城市码
            'callDirection' => 0, // 表示该绑定关系允许的呼叫方向，取值范围：0 1 2
            'duration' => $duration, // 绑定关系保持时间，单位为秒。
            'recordFlag' => true, // 是否需要针对该绑定关系产生的所有通话录音。
            // 'recordHintTone' => $recordHintTone, // 该参数用于设置录音提示音 不携带该参数表示录音前不播放提示音。
            'maxDuration' => $maxDuration, // 设置允许单次通话进行的最长时间，单位为分钟。
            'lastMinVoice' => $this->lastMinVoice, // 设置通话剩余最后一分钟时的提示音
            // 'privateSms' => $privateSms, // 设置该绑定关系是否支持短信功能
            'preVoice' => $preVoice // 设置个性化通话前等待音，即主叫听到的回铃音。
        ]);
        $response = $this->getResponse('POST',$data);
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
        return $response;
    }

    /**
     * 更新绑定
     * @param $subscriptionId
     * @param $callerNum A号码
     * @param $calleeNum B号码
     * @param $duration
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
     * @param $subscriptionId
     * @return bool|false|string
     */
    public function getAxBindInfo($subscriptionId)
    {
        // 请求URL参数
        $data = http_build_query([
            'subscriptionId' => $subscriptionId,
        ]);
        // 完整请求地址
        $fullUrl = $this->realUrl . '?' . $data;

        $response = $this->getResponse('GET', $data, $fullUrl);
        return $response;
    }

    public function getAxBindInfos($relationNum)
    {
        // 请求URL参数
        $data = http_build_query([
            'relationNum' => $relationNum,
        ]);
        // 完整请求地址
        $fullUrl = $this->realUrl . '?' . $data;

        $response = $this->getResponse('GET', $data, $fullUrl);
        return $response;
    }

    /**
     * 录音文件下载地址
     * @param $recordDomain
     * @param $fileName
     * @return bool|false|string
     */
    public function getRecordDomain($recordDomain,$fileName)
    {
        // 请求URL参数
        $data = http_build_query([
            'recordDomain' => $recordDomain,
            'fileName' => $fileName
        ]);
        // 完整请求地址
        $fullUrl = $this->realUrl . '?' . $data;

        $response = $this->getResponse('GET', $data, $fullUrl);
        if(strpos($http_response_header[0], '301') !== false){
            foreach ($http_response_header as $loop){
                if(strpos($loop, "Location") !== false){
                    $fileUrl = trim(substr($loop, 10));
                }
            }
        }
        return $response;
    }

    /**
     * 呼叫事件通知接口
     * @param $jsonBody
     */
    public function onCallEvent($jsonBody)
    {
//        $jsonArr = json_decode($jsonBody, true); //将通知消息解析为关联数组
        $jsonArr = $jsonBody;

        $eventType = $jsonArr['eventType']; //通知事件类型

        if (strcasecmp($eventType, 'fee') == 0) {
            return;
        }

        if (!array_key_exists('statusInfo', $jsonArr)) {
            return;
        }
        $statusInfo = $jsonArr['statusInfo']; //呼叫状态事件信息

        //callin：呼入事件
        if (strcasecmp($eventType, 'callin') == 0) {
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //callout：呼出事件
        if (strcasecmp($eventType, 'callout') == 0) {
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //alerting：振铃事件
        if (strcasecmp($eventType, 'alerting') == 0) {
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //answer：应答事件
        if (strcasecmp($eventType, 'answer') == 0) {
            if (array_key_exists('sessionId', $statusInfo)) {
                print_r('sessionId: ' . $statusInfo['sessionId'] . PHP_EOL);
            }
            return;
        }
        //disconnect：挂机事件
        if (strcasecmp($eventType, 'disconnect') == 0) {
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
//        $jsonArr = json_decode($jsonBody, true); //将通知消息解析为关联数组
        $jsonArr = $jsonBody;//json_decode($jsonBody, true); //将通知消息解析为关联数组
        $eventType = $jsonArr['eventType']; //通知事件类型

        if (strcasecmp($eventType, 'fee') != 0) {
            return;
        }

        if (!array_key_exists('feeLst', $jsonArr)) {
            return;
        }
        $feeLst = $jsonArr['feeLst']; //呼叫话单事件信息
        //短时间内有多个通话结束时隐私保护通话平台会将话单合并推送,每条消息最多携带50个话单
        if (sizeof($feeLst) > 1) {
            foreach ($feeLst as $loop){
                Log::info($loop);
                if (array_key_exists('sessionId', $loop)) {
                    print_r('sessionId: ' . $loop['sessionId'] . PHP_EOL);
                }
            }
        } else if(sizeof($feeLst) == 1) {
            Log::info($feeLst);
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
        $this->callData = $data;
        return $response;
    }

}
