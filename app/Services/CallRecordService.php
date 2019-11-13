<?php

namespace App\Services;

use App\Models\CallBindRecord;
use App\Models\CallBindRecordLog;
use App\Models\CallEventRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CallRecordService
{

    /**
     *
     * @param $orderNo
     * @param $callData
     * @param $bindType
     */
    public function addBindRecord($orderNo, $callData, $bindType)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function saveEventRecord($data)
    {
        try {
//            $jsonArr = json_decode($data, true); //将通知消息解析为关联数组
            $jsonArr = $data;
            $eventType = $jsonArr['eventType']; //通知事件类型
            if (strcasecmp($eventType, 'fee') == 0) {
                return;
            }
            if (!array_key_exists('statusInfo', $jsonArr)) {
                return;
            }
            $statusInfo = $jsonArr['statusInfo']; //呼叫状态事件信息

            $eventRecord['event_type'] = $eventType;
            $columns = Schema::getColumnListing('zx_call_event_records');
            foreach ($statusInfo as $k => $v) {
                $k = snake_case($k);
                in_array($k,$columns) ? $eventRecord[$k] = $v : '';
            }
            $event = new CallEventRecord($eventRecord);
            $event->save();
            return;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
