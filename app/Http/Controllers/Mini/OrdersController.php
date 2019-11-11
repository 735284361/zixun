<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Teacher;
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

        $status == 0 ? '' : $maps['status']  = $status;
        $maps['user_id'] = auth('api')->id();

        return Order::orderBy('id','desc')->with('userInfo')->with('teacher')->where($maps)->paginate(10);
    }

}
