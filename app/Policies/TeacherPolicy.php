<?php

namespace App\Policies;

use App\Models\Teacher;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $user, Teacher $teacher)
    {
        return $user->uid == $teacher->user_id;
    }

    public function view(User $user, Teacher $teacher)
    {
        return $user->uid == $teacher->user_id;
    }

    // 订单预约权限
    public function order(User $user, Teacher $teacher)
    {
        if ($user->uid == $teacher->user_id) {
            // 不能预约自己
            return false;
        } else if ($teacher->status != Teacher::STATUS_ENABLE) {
            // 讲师预约状态
            return false;
        }
        return true;
    }
}
