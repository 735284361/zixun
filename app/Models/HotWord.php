<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotWord extends Model
{
    //

    protected $table = 'zx_hot_words';

    const WORDS_STATUS_ENABLE = 10; // 正常
    const WORDS_STATUS_DISABLE = 20; // 禁用

}
