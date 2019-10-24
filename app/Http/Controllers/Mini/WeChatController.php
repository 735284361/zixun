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
        $data = $miniProgram->auth->session($code);
        //判断code是否过期
        if (isset($data['errcode'])) {
            return response()->json(['code'=>1,'msg'=>'code已过期或不正确']);
        }
//        获取小程序信息 当前只获取了个昵称及输入的工号密码
        $openId = $data['openid'];
        $sessionKey = $data['session_key'];

        // 判断子表是否存在该用户
        $userSub = UsersSub::where('open_id',$openId)->first();
        if (!$userSub) {
            // 该用户不存在
            return response()->json(['code' => '10000']);
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
                'code' => 0,
                'token' => $token,
                'data' => $user,
            ],200);
        }
    }

    public function register(Request $request)
    {
        $code = $request->get('code');
        $iv = $request->get('iv');
        $encryptedData = $request->get('encryptedData');
        // 解压数据
        $app = \EasyWeChat::miniProgram(); // 小程序
        $sessionData = $app->auth->session($code);
        $sessionKey = $sessionData['session_key'];
        $unionId = $sessionData['unionid'];
        $data = $app->encryptor->decryptData($sessionKey, $iv, $encryptedData);
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
        return Auth::guard('api')->id();
    }

}
