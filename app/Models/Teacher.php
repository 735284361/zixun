<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //

    protected $table = 'zx_teachers';

    // 讲师时刻 课表 一对多
    public function times()
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
        return $this->belongsToMany(User::class,'zx_users_teachers_likes','teacher_id','user_id');
    }

    // 订单
    public function orders()
    {
        return $this->hasMany(Order::class,'teacher_id','id');
    }

    public function orderEval()
    {

    }

}
