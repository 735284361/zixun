<?php

namespace App\Http\Controllers\Mini;

use App\Models\UsersAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    //
    public function myAccount()
    {
        $account = UsersAccount::where('user_id',auth('api')->id())->first(['account']);
        return $account ? $account : $arr['account'] = 0;
    }

}
