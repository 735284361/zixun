<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\OrderEvalRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Teacher;
use App\Services\OrderEvalService;
use App\Services\OrdersService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrdersController extends Controller
{
    //

    protected $orderService;

    public function __construct()
    {
        $this->orderService = new OrdersService();
    }

    /**
     * 提交订单
     * @param OrderRequest $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function postOrder(OrderRequest $request)
    {
        $teacher = Teacher::where('id',$request->teacher_id)->first();
        // 判断是否有下单权限
        $this->authorize('order',$teacher);
        return $this->orderService->addOrder($request->all());
    }

    /**
     * 重新支付订单
     * @param Request $request
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function repay(Request $request)
    {
        $this->validate($request,['order_no' => 'required']);
        return $this->orderService->repay($request->order_no);
    }

    /**
     * 获取订单信息
     * @param Request $request
     * @return \App\Models\Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Illuminate\Validation\ValidationException
     */
    public function orderInfo(Request $request)
    {
        $this->validate($request,['order_no' => 'required']);

        return $this->orderService->orderInfo($request->order_no);
    }

    /**
     * 订单列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function orderList(Request $request)
    {
        $status = $request->input('status',0);

        $status == 0 ? '' : $maps['status']  = $status; // status == 0 则查询所有状态订单，否则查询指定状态订单
        $maps['user_id'] = auth('api')->id();

        // 如果是讲师
        if (Teacher::where('user_id',auth('api')->id())->exists()) {
            // 查询所有订单 包含被咨询的订单
            $teacherId = Teacher::where('user_id',auth('api')->id())->value('id');
            // 允许展示给老师的订单状态 已支付、已完成、已取消等订单
            $statusArr = [
                Order::ORDER_PAID,
                Order::ORDER_COMPLETED
            ];
            if ($status == 0) { // 查询所有订单
                $list = Order::orderBy('id','desc')->with('userInfo')->with('teacher')->with('orderEval')->with('bill')
                    ->where($maps)->orWhere(function($query) use ($teacherId, $statusArr) { // 自己被咨询的订单 订单状态为特定状态
                        $query->where('teacher_id',$teacherId)->whereIn('status',$statusArr);
                    })->paginate(100);
                return $list;
            } else if (in_array($status,$statusArr)) { // 查询指定状态的订单 如果指定的订单状态不展示给讲师用户则跳过
                $teacherMaps['teacher_id'] = $teacherId;
                $teacherMaps['status'] = $status;
                $list = Order::orderBy('id','desc')->with('userInfo')->with('teacher')->with('orderEval')->with('bill')
                    ->where($maps)->orWhere(function($query) use ($teacherMaps) { // 自己被咨询的订单 订单状态为特定状态
                        $query->where($teacherMaps);
                    })->paginate(100);
                return $list;
            } // 其他情况只返回用户自己的订单
        }
        return Order::orderBy('id','desc')->with('userInfo')->with('teacher')->with('orderEval')->with('bill')->where($maps)->paginate(100);
    }

    /**
     * 订单结束
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function completeOrder(Request $request)
    {
        $this->validate($request,['order_no'=>'required']);

        $order = Order::where('order_no',$request->order_no)->first();
        return $this->orderService->completeOrder($order);
    }

    /**
     * 订单评论
     * @param OrderEvalRequest $request
     * @return array
     */
    public function orderEval(OrderEvalRequest $request)
    {
        $orderEvalService = new OrderEvalService();
        $res = $orderEvalService->saveOrderEval($request->all());
        if ($res) {
            $arr = ['code' => 0,'msg' => 'success'];
        } else {
            $arr = ['code' => 1,'msg' => 'fail'];
        }
        return $arr;
    }

}
