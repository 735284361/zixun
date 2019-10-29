<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeachersTime;
use Illuminate\Support\Facades\DB;

class TeachersService
{

    /**
     * 讲师设置时间表
     * @param $data
     * @return mixed
     */
    public function addTimes($data)
    {
        $teacher = Teacher::where('user_id',auth('api')->id())->first();

        $date_at = strtotime($data['date_at']);

        $timeArr = $data['arr'];
        $count = count($timeArr);
        $resArr = [];
        DB::beginTransaction();
        $result1 = $result2 = true;
        for ($i = 0; $i < $count; $i++) {
            $obj['teacher_id'] = $teacher->id;
            $obj['date_at'] = $date_at;
            $obj['start_at'] = strtotime($timeArr[$i]['start_at']);
            $obj['end_at'] = strtotime($timeArr[$i]['end_at']);
            $res = $teacher->teacherTimes()->where('date_at',$date_at)->where('status','!=',TeachersTime::STATUS_TIMES_BOOKED)->updateOrCreate($obj);
            if (!$res) {
                $result1 = false;
            } else {
                $resArr[] = $res->id;
            }
        }
        $result2 = $teacher->teacherTimes()->where('date_at',$date_at)->whereNotIn('id',$resArr)->where('status','!=',TeachersTime::STATUS_TIMES_BOOKED)->delete();

        if ($result1 !== false && $result2 !== false) {
            DB::commit();
            return true;
        } else {
            DB::rollBack();
            return false;
        }
    }

}
