<?php

namespace App\Http\Controllers\Mini;

use App\Models\Order;
use App\Models\OrderEval;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TeachersController extends Controller
{
    //

    public function lists()
    {
        $list = Teacher::with(['tags' => function($query) {
            // 老师标签
            return $query->select('tag');
        }])->get();
        return $list;
    }

    // 讲师详情
    public function detail(Request $request)
    {
        $id = $request->id;

        $data = Teacher::where('id',$id)->with(['tags' => function($query) {
            // 讲师标签
            return $query->select('tag');
        }])->with(['teacherTimes' => function($query) {
            // 讲师时间
            $startAt = strtotime(date('Ymd'));
            $endAt = $startAt + 30*24*3600;
            return $query->where('date_at','>=',$startAt)->where('date_at','<=',$endAt);
        }])->with(['userLike' => function($query) {
            return $query->select('user_id');
        }])->first();

        // 咨询历史
        $orders = Order::where('teacher_id',$id)->where('subject','!=',null)
            ->where('status',Order::ORDER_COMPLETED)
            ->with(['userInfo' => function($query) {
                $query->select(['uid','username','head_image']);
            }])->get();
        $data->orders = $orders;

        // 评论
        $eval = OrderEval::where('teacher_id',$id)->with(['user' => function($query) {
            return $query->select(['uid','username','head_image']);
        }])->get();
        $data->eval = $eval;

        return response()->json($data);
    }
}
