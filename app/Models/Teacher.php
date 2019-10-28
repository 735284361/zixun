<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //

    protected $table = 'zx_teachers';

    protected $guarded = [];

    // 老师状态
    const STATUS_ENABLE = 10; // 启用
    const STATUS_DISABLE = 20; // 禁用

    // 讲师时刻 课表 一对多
    public function teacherTimes()
    {
        return $this->hasMany(TeachersTime::class,'teacher_id','id');
    }

    // 讲师标签 多对多
    public function tags()
    {
        return $this->belongsToMany(Tag::class,'zx_teachers_tags','teacher_id','tag_id');
    }

    // 所有收藏用户
    public function likes()
    {
        return $this->belongsToMany(\App\User::class,'zx_users_teachers_likes','teacher_id','user_id');
    }

    // 用户收藏
    public function userLike()
    {
        return $this->belongsToMany(\App\User::class,'zx_users_teachers_likes',
            'teacher_id','user_id',null,'uid')
            ->where('user_id',auth('api')->id());
    }

    // 订单
    public function orders()
    {
        return $this->hasMany(Order::class,'teacher_id','id');
    }

    // 订单评论
    public function orderEval()
    {
        return $this->hasMany(OrderEval::class,'teacher_id','id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class,'uid','user_id');
    }

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
