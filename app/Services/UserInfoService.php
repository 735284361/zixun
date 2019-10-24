<?php

namespace App\Services;

use App\Models\UsersInfo;

class UserInfoService
{

    // 新增用户信息
    public function addUserInfo($data)
    {
        return UsersInfo::updateOrCreate(['user_id'=>auth('api')->id()],[
            'user_id' => auth('api')->id(),
            'name' => $data['name'],
            'post' => $data['post'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'company' => $data['company']
        ]);
    }

}
