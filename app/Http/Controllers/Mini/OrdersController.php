<?php

namespace App\Http\Controllers\Mini;

use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Services\OrdersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        $key = 'time_id';
        $value = [1,2,3,4];
        $data = array_fill_keys($value,$key);
        return array_flip($data);
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

        $this->orderService->addOrder($request->all());
    }
}
