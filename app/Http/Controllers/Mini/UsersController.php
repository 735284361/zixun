<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\UserInfoRequest;
use App\Models\UsersAccount;
use App\Models\UsersInfo;
use App\Services\UserInfoService;
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
        $account = UsersAccount::where('user_id',auth('api')->id())->first(['account'])->toArray();
        return $account ? $account : $arr['account'] = 0;
    }

    // 添加和更新用户信息
    public function postUserInfo(UserInfoRequest $request)
    {
        $userInfoService = new UserInfoService();
        return $userInfoService->addUserInfo($request->all());
    }

    public function userInfo()
    {
        return UsersInfo::where('user_id',auth('api')->id())->first();
    }
}
