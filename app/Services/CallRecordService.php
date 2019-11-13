<?php

namespace App\Services;

use App\Models\CallBindRecord;
use App\Models\CallBindRecordLog;
use App\Models\CallEventRecord;
use App\Models\CallFeeRecord;
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
            $record->bindLogs()->create(['content' => $data, 'type' => $bindType ,'subscription_id' => $data['subscription_id']]);
            $record->subscription()->updateOrCreate(['subscription_id'=>$data['subscription_id']],['subscription_id' => $data['subscription_id']]);
            return;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * 保存呼叫事件记录
     * @param $eventType
     * @param $statusInfo
     * @return bool
     */
    public function saveEventRecord($eventType, $statusInfo)
    {
        try {
            $eventRecord['event_type'] = $eventType;
            $columns = Schema::getColumnListing('zx_call_event_records');
            foreach ($statusInfo as $k => $v) {
                $k = snake_case($k);
                in_array($k,$columns) ? $eventRecord[$k] = $v : '';
            }
            $event = new CallEventRecord($eventRecord);
            $event->save();
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * 保存话单信息
     * @param $eventType
     * @param $feeLst
     * @return bool
     */
    public function saveCallFeeRecord($eventType, $feeLst)
    {
        try {
            $columns = Schema::getColumnListing('zx_call_fee_records');
            foreach ($feeLst as $data){
                $feeRecord['event_type'] = $eventType;
                $feeRecord['content'] = json_encode($data);
                foreach ($data as $k => $v) {
                    $k = snake_case($k);
                    in_array($k,$columns) ? $feeRecord[$k] = $v : '';
                }

                // 计算通话时长
                if (array_key_exists('fwd_answer_time',$feeRecord) && array_key_exists('call_end_time',$feeRecord)) {
                    $feeRecord['call_time_len'] = strtotime($feeRecord['call_end_time']) - strtotime($feeRecord['fwd_answer_time']);
                }
                $feeRecord['created_at'] = date('Y-m-d H:i:s',time());
                $feeRecord['updated_at'] = date('Y-m-d H:i:s',time());
                $list[] = $feeRecord;
            }
            $fee = new CallFeeRecord();
            $fee->insert($list);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
