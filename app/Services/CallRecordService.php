<?php

namespace App\Services;

use App\Models\CallBindRecord;
use App\Models\CallBindRecordLog;
use Illuminate\Support\Facades\DB;

class CallRecordService
{

    public function addBindRecord($orderNo, $callData, $bindType)
    {
        // 新增绑定记录
        foreach ($callData as $k => $v) {
            $k = snake_case($k);
            $data[$k] = $v;
        }
        $recordData['order_no'] = $orderNo;
        array_key_exists('caller_num',$data) ? $recordData['caller_num'] = $data['caller_num'] : '';
        array_key_exists('relation_num',$data) ? $recordData['relation_num'] = $data['relation_num'] : '';
        array_key_exists('callee_num',$data) ? $recordData['callee_num'] = $data['callee_num'] : '';
        array_key_exists('duration',$data) ? $recordData['duration'] = $data['duration'] : '';
        array_key_exists('max_duration',$data) ? $recordData['max_duration'] = $data['max_duration'] : '';
        array_key_exists('resultcode',$data) ? $recordData['resultcode'] = $data['resultcode'] : '';
        array_key_exists('resultdesc',$data) ? $recordData['resultdesc'] = $data['resultdesc'] : '';
        array_key_exists('subscription_id',$data) ? $recordData['subscription_id'] = $data['subscription_id'] : '';

        $record = CallBindRecord::updateOrCreate(['order_no'=>$orderNo],$recordData);
        // 存日志
        $record->bindLogs()->create(['content' => $data, 'type' => $bindType]);
        return;
    }
}
