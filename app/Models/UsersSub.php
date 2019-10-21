<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class UsersSub extends Authenticatable
{
    use HasApiTokens,Notifiable;
    // 表名
    protected $table = 'users_sub';
    // 主键
    protected $primaryKey = 'open_id';

    protected $guarded = [];

}
