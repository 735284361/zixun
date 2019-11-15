<?php

namespace App\Services;

use App\Models\EntryBill;
use Carbon\Carbon;

class BillService
{

    /**
     * 添加讲师入账账单
     * @param $data
     * @return bool
     */
    public function saveEntryBill($orderNo, $teacherId, $totalFee)
    {
        $commissionRate = env('COMMISSION_RATE');
        // 佣金计算
        $commission = floor(($totalFee * $commissionRate) / 100);
        $entryFee = $totalFee - $commission;

        $entryBill = new EntryBill();
        $entryBill->order_no = $orderNo;
        $entryBill->teacher_id = $teacherId;
        $entryBill->total_fee = $totalFee;
        $entryBill->entry_fee = $entryFee;
        $entryBill->commission = $commission;
        if ($entryBill->save()) {
            return $entryBill;
        } else {
            return  false;
        }
    }

}
