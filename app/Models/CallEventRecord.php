<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallEventRecord extends Model
{
    //

    protected $table = 'zx_call_event_records';

    protected $guarded = [];

    const CALL_IN = 'callin';
    const CALL_OUT = 'callout';
    const ALERTING = 'alerting';
    const ANSWER = 'answer';
    const DISCONNECT = 'disconnect';

    /**
     * 获取呼叫时间类型
     * @param null $ind
     * @return array|mixed|string
     */
    public static function getEventType($ind = null)
    {
        $arr = [
            self::CALL_IN => '呼入事件',
            self::CALL_OUT => '呼出事件',
            self::ALERTING => '振铃事件',
            self::ANSWER => '应答事件',
            self::DISCONNECT => '挂机事件',
        ];

        if ($ind !== null) {
            return array_key_exists($ind,$arr) ? $arr[$ind] : '';
        }
        return $arr;
    }
}
