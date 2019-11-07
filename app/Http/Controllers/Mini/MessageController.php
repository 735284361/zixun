<?php

namespace App\Http\Controllers\Mini;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    //

    // 消息列表
    public function lists()
    {
        $list = Auth::user()->notifications()->paginate(20)->toArray();

        $data = $list['data'];
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['type'] = snake_case(class_basename($data[$i]['type']));
        }
        $list['data'] = $data;
        return response()->json($list);
    }

    // 获取未读消息数量
    public function unreadCount()
    {
        $data = Auth::user()->unreadNotifications;
        return count($data);
    }

    // 设置某条消息已读
    public function markAsRead(Request $request)
    {
        return Auth::user()->unreadNotifications->where('id', $request->get('id'))->markAsRead();
    }

    // 设置所有消息已读
    public function markAsReadForAll()
    {
        return Auth::user()->unreadNotifications->markAsRead();
    }

    // 删除某条消息
    public function deleteMsg(Request $request)
    {
        return Auth::user()->unreadNotifications->where('id', $request->get('id'))->delete();
    }

    // 删除所有消息
    public function delete()
    {
        return Auth::user()->notifications()->delete();
    }
}
