<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\TeacherTimesRequest;
use App\Models\Order;
use App\Models\OrderEval;
use App\Models\Teacher;
use App\Services\TeachersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeachersController extends Controller
{
    //

    protected $teacherService;

    public function __construct()
    {
        $this->teacherService = new TeachersService();
    }

    // 讲师列表
    public function lists(Request $request)
    {
        $id = $request->id;
        $map = [];
        if ($id) {
            $map['id'] = $id;
        }

        $list = Teacher::with(['tags' => function($query) {
            // 老师标签
            return $query->select('tag');
        }])->where('status',Teacher::STATUS_ENABLE)->where($map)->get();
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
        }])->withCount('userLike')->first();

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

    /**
     * 讲师时间设置
     * @param TeacherTimesRequest $request
     * @return array
     */
    public function setTimes(TeacherTimesRequest $request)
    {
        $teacher = Teacher::where('user_id',auth('api')->id())->first();

        if (Auth::user()->can('create',$teacher)) {
            $res = $this->teacherService->addTimes($request->all());
            if ($res) {
                return ['code' => 0,'msg' => '修改成功'];
            } else {
                return ['code' => 1,'msg' => '修改失败'];
            }
        } else {
            return ['code' => 1,'msg' => '没有修改权限'];
        }
    }

    /**
     * 获取讲师设置过的时间
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimes(Request $request)
    {
        $this->validate($request,[
            'date_at' => 'required|date_format:Ymd'
        ]);

        $teacher = Teacher::where('user_id',auth('api')->id())->first();

        if (Auth::user()->can('view',$teacher)) {
            $data = Teacher::where('teacher_id',$teacher->id)->select();
            return response()->json([
                'code' => 0,
                'data' => $data
            ]);
        } else {
            return ['code' => 1,'msg' => '没有修改权限'];
        }
    }
}
