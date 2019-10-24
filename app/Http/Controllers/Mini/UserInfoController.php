<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\UserInfoRequest;
use App\Services\UserInfoService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserInfoController extends Controller
{
    //

    protected $userInfoService;

    public function __construct()
    {
        $this->userInfoService = new UserInfoService();
    }

    // 添加和更新用户信息
    public function postUser(UserInfoRequest $request)
    {
        return $this->userInfoService->addUserInfo($request->all());
    }
}
