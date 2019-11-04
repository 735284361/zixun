<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\Teacher;
use App\Services\OrdersService;
use App\Services\PayService;
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
//        Cache::store('redis')->put('ORDER_CONFIRM:3',3,5);
//        $data = Cache::get('ORDER_CONFIRM:3');
//        return response()->json($data);

//        $key = 'time_id';
//        $value = [1,2,3,4];
//        $data = array_fill_keys($value,$key);
//        return array_flip($data);

        $orderNo = date('YmdHis').rand(100000,999999);
        $payService = new PayService();

        return $payService->getPayParams($orderNo,1);

    }

    public function getCache()
    {
        $data = Cache::get();
        return response()->json($data);
    }

    public function postOrder(OrderRequest $request)
    {
//        $order = Order::where('id',3)->first();
//
//        $res = CloseOrder::dispatch($order)->delay(now()->addMinute(1));
//        return response()->json($res);

        $teacher = Teacher::where('id',$request->teacher_id)->first();
        // 判断是否有下单权限
        $this->authorize('order',$teacher);
        return $this->orderService->addOrder($request->all());
    }
}
