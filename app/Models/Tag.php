<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    protected $table = 'zx_tags';

    // 标签状态
    const STATUS_ENABLE = 10; // 启用
    const STATUS_DISABLE = 20; // 禁用

    public function status($ind = null)
    {
        $arr = [
            self::STATUS_ENABLE => '启用',
            self::STATUS_DISABLE => '禁用',
        ];

        if ($ind !== null) {
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::STATUS_DISABLE];
        }

        return $arr;
    }
}
