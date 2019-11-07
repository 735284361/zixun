<?php


return [


    //必填,请参考"开发准备"获取如下数据,替换为实际值
    //$realUrl = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/provision/caas/privatenumber/v1.0'; // APP接入地址+接口访问URI
    //$APP_KEY = 'a1d1f50cad21415fbdd13d8f53d36d60'; // APP_Key
    //$APP_SECRET = 'cfc881cc704c4fba8d8fef5788e03e6b'; // APP_Secret
    //$origNum = '+8618612345678'; // A号码
    //$privateNum = '+8617010000001'; // X号码(隐私号码)

    /*
     * 选填,各参数要求请参考"AX模式绑定接口"
     */
    // $privateNumType = 'mobile-virtual'; //固定为mobile-virtual
    // $areaCode = '0755'; //需要绑定的X号码对应的城市码
    // $recordFlag = 'false'; //是否需要针对该绑定关系产生的所有通话录音
    // $recordHintTone = 'recordHintTone.wav'; //设置录音提示音
    // $calleeNumDisplay = '0'; // 设置非A用户呼叫X时,A接到呼叫时的主显号码
    // $privateSms = 'true'; //设置该绑定关系是否支持短信功能

    // $callerHintTone = 'callerHintTone.wav'; //设置A拨打X号码时的通话前等待音
    // $calleeHintTone = 'calleeHintTone.wav'; //设置非A用户拨打X号码时的通话前等待音
    // $preVoice = [
    //     'callerHintTone' => $callerHintTone,
    //     'calleeHintTone' => $calleeHintTone
    // ];


    'ax_call' => [
        'real_url' => (env('CALL_AX_REAL_URL')),
        'key' => (env('CALL_AX_KEY')),
        'secret' => (env('CALL_AX_SECRET')),
        'record_flag' => (env('CALL_AX_RECORD_FLG',true)), // 是否需要针对该绑定关系产生的所有通话录音
        'record_hint_tone' => (env('CALL_AX_RECORD_HIT_TONE','callerHintTone.wav')), // 设置录音提示音
        'caller_hint_tone' => (env('CALL_AX_HINT_TONE','callerHintTone.wav')), // 设置A拨打X号码时的通话前等待音
        'pre_voice' => [
            ''
        ],

    ],

];
