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
}
