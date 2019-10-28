<?php

namespace App\Http\Controllers\Mini;

use App\Models\Teacher;
use App\Models\UsersAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    //

    /**
     * 获取用户账户余额
     * @return int
     */
    public function myAccount()
    {
        $account = UsersAccount::where('user_id',auth('api')->id())->first(['account']);
        return $account ? $account : $arr['account'] = 0;
    }
}
