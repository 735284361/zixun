<?php

namespace App\Http\Controllers\Mini;

use App\Models\UsersSub;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeChatController extends Controller
{
    //
    public function login(Request $request)
    {
        $code = $request->code;
        $miniProgram = \EasyWeChat::miniProgram();
        //获取openid session_key
//        $data = $miniProgram->auth->session($code);
//        //判断code是否过期
//        if (isset($data['errcode'])) {
//            return response()->json(['code'=>1,'msg'=>'code已过期或不正确']);
//        }
        //获取小程序信息 当前只获取了个昵称及输入的工号密码
//        $openId = $data['openid'];
//        $sessionKey = $data['session_key'];

        $openId = 'oR-sN5LZKPcvMG1_CkpoLCQTuFmo';
        $sessionKey = 'S8cTQ+DxFFB6BT/UKvV6bA==';

        // 判断子表是否存在该用户
        $userSub = UsersSub::where('open_id',$openId)->first();
        if (!$userSub) {
            // 该用户不存在
            return response()->json(['code' => '401']);
        } else {
            // 该用户存在 覆盖用户之前登录信息
            $userSub->session_key = $sessionKey;
            $userSub->save();

            // 用主表Users 创建token
            $user = User::where('uid', $userSub['uid'])->first();
            $createToken = $user->createToken($user['unionid']);
            $createToken->token->save();
            $token = $createToken->accessToken;

            return response()->json([
                'token' => $token,
                'data' => $user,
            ],200);
        }
    }

    public function register(Request $request)
    {
        $sessionKey = "oyLUVHs3xyPN8arT2JPIsg==";//$request->get('sessionKey');
        $iv = "CxAkIemE+ff5RldTfjXrTA==";//$request->get('iv');
        $encryptedData = "r8BGYpPIsBRxuE3hPG799s18C65v2DI5CiZRM6W0KvtKl9Oi3RKRn6XsatlzSeBiTScb04lbms6Tj9oApWofKj4YLTO5oe3OlCYZ2/oU6V7YF5cRHuXNpJJXrQ5f2jUC3OlCVKtcGHimK+fViaNxxWUppWVK06is6xcxgoFD0RL8AT6xDgnzY3iy2k3olqhx4PFBShl0E6mysabOuG3Huk9yxEU+HpEF5NCHt1FDvTSLzZ3Wq8uYYdeD8s+19Q4m+1VigfBMmrOorLRbp2wpwQXY4B1xWw8C88AJ+UXguM1cLSSkfxI+gy7kWwIhb+JHiRACFpn+hhFVUN3NM2IjrY3lCJh+CMPDrg0keFhHc00TaAj/Hfbznq7L7pLpTDdTeu/Nwa5O3vDEvUDe9DqkSqSFs7V6eIqENMeMzTkXi6bg+MakP37N6gQVmKKSmPmzyHNexiZiMZjLfyHyF5ZO5A==";
        // 解压数据
        $app = \EasyWeChat::miniProgram(); // 小程序
        $data = $app->encryptor->decryptData($sessionKey, $iv, $encryptedData);

        $unionId = 'oXWAQv6dq-KX5ygK8VEqFhTv6go';

        // 查看主库是否存在该用户
        $time = time();
        $user = User::firstOrCreate(['unionid'=>$unionId],[
            'username' => $data['nickName'],
            'unionid'   => $unionId,
            'regist'    => $time,
            'head_image'    => $data['avatarUrl'],
            'nickname'   => $data['nickName'],
            'wechat_name'   => $data['nickName'],
            'since_from'    => USER_SINCE_FROM_ZIXUN,
        ]);

        UsersSub::firstOrCreate(['open_id'=>$data['openId']],[
            'uid' => $user['uid'],
            'open_id'   => $data['openId'],
            'since_from'    => USER_SINCE_FROM_ZIXUN
        ]);

        return response()->json([
            'code' => 0,
            'msg' => '注册成功'
        ]);
    }

    public function profile()
    {
        return Auth::guard('api')->user();
    }

}
