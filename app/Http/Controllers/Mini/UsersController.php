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
     * 获取用户账户余额信息
     * @return int
     */
    public function myAccount()
    {
        $account = new UsersAccount();
        if (!$account->where('user_id',auth('api')->id())->exists()) {
            $account->user_id = auth('api')->id();
            $account->save();
        }
        return $account->where('user_id',auth('api')->id())->first();
    }

    /**
     * 添加和更新用户信息
     * @param UserInfoRequest $request
     * @return mixed
     */
    public function postUserInfo(UserInfoRequest $request)
    {
        $userInfoService = new UserInfoService();
        return $userInfoService->addUserInfo($request->all());
    }

    /**
     * 获取用户订单个人信息
     * @return mixed
     */
    public function userInfo()
    {
        return UsersInfo::where('user_id',auth('api')->id())->first();
    }
}
