<?php

namespace App;

use App\Models\Order;
use App\Models\Teacher;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    public $timestamps = false;

    protected $primaryKey = 'uid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [
//        'name', 'email', 'password',
//    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded=[];

    // 用户子表信息
    public function userSub()
    {
        return $this->hasOne(User::class,'uid','uid')->where('since_from',5);
    }

    // 判断用户是否是讲师
    public function teacherInfo()
    {
        return $this->hasOne(Teacher::class,'user_id','uid');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'user_id','uid');
    }

}
