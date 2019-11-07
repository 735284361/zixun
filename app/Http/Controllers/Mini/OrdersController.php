<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\Teacher;
use App\Models\TeachersTime;
use App\Services\OrdersService;
use App\Services\PayService;
use App\Services\TempMsgService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrdersController extends Controller
{
    //
    protected $orderService;

    public function __construct()
    {
        $this->orderService = new OrdersService();
    }


    public function test()
    {
        $order = Order::find(89);

        return TempMsgService::paySuccess($order);

        // 将订单的 closed 字段标记为 true，即关闭订单
//        $order->update(['status' => Order::ORDER_INVALID]);
//        // 更新讲师时间状态
//        $timeIdArr = $order->orderTimes()->get()->toArray();
//        $timeIdArr = array_column($timeIdArr,'time_id');
//        TeachersTime::whereIn('id',$timeIdArr)->update([
//            'status' => TeachersTime::STATUS_TIMES_ENABLE
//        ]);

//        CloseOrder::dispatch($order);

    }

    public function getCache()
    {
        $data = Cache::get();
        return response()->json($data);
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
}
