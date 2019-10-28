<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachersTime extends Model
{
    //

    protected $table = 'zx_teachers_times';

    protected $guarded = [];

    const STATUS_TIMES_ENABLE = 10; // 正常可用
    const STATUS_TIMES_BOOKED = 20; // 已被预约
}
