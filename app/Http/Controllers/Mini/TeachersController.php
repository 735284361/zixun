<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\TeacherTimesRequest;
use App\Models\Order;
use App\Models\OrderEval;
use App\Models\Teacher;
use App\Models\TeachersTime;
use App\Services\TeachersService;
use Carbon\Carbon;
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
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getTime(Request $request)
    {
        $this->validate($request,['id'=>'required|integer']);

        $startAt = strtotime(date('Ymd'));
        $endAt = $startAt + 30*24*3600;
        $data = TeachersTime::where('teacher_id',$request->id)->where('date_at','>=',$startAt)->where('date_at','<=',$endAt)->get();

        return response()->json(['code' => 0,'data' => $data]);
    }

    /**
     * 用户收藏
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postLikeTeacher(Request $request)
    {
        $this->validate($request,['id' => 'required|integer']);
        $id = $request->id;
        $teacher = Teacher::where('id', $id)->withCount('userLike')->first();
        if ($teacher->user_like_count == 0) {
            $teacher->likes()->attach(auth('api')->id(),[
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        return response()->json(true);
    }

    /**
     * 用户取消收藏
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteLikeTeacher(Request $request)
    {
        $this->validate($request,['id' => 'required|integer']);
        $id = $request->id;
        $teacher = Teacher::where('id', $id)->first();
        $teacher->likes()->detach(Auth::user()->id);
        return response()->json(true);
    }

    /**
     * 获取讲师认证信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function myTeacherInfo()
    {
        $teacher = Teacher::where('user_id',auth('api')->id())->first();
        return response()->json(['code' => 0,'data' => $teacher]);
    }

}
