<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryBill extends Model
{
    //

    protected $table = 'zx_entry_bills';

    const BILL_STATUS_WAITING = 10; // 入账中
    const BILL_STATUS_COMPLETED = 20; // 已入账
    const BILL_STATUS_CANCEL = 30; // 订单取消

}
