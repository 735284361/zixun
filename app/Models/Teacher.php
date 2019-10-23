<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //

    protected $table = 'zx_teachers';

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

    //
    public function likes()
    {
        return $this->belongsToMany(\App\User::class,'zx_users_teachers_likes','teacher_id','user_id');
    }

    public function userLike()
    {
        return $this->belongsToMany(UsersSub::class,'zx_users_teachers_likes','teacher_id','user_id',null,'uid')
            ->where('user_id',170379);
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

}
