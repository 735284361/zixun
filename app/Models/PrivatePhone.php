<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivatePhone extends Model
{
    //

    protected $table = 'zx_private_phone';

    const STATUS_ENABLE = 10; // 可用
    const STATUS_USED = 20; // 使用中
    const STATUS_DISABLE = 30; // 不可用
}
