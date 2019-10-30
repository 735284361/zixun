<?php

namespace App\Http\Controllers\Mini;

use App\Jobs\CloseOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrdersController extends Controller
{
    //

    public function test()
    {
        Cache::store('redis')->put('ORDER_CONFIRM:3',3,5);
        $data = Cache::get('ORDER_CONFIRM:3');
        return response()->json($data);
    }

    public function getCache()
    {
        $data = Cache::get();
        return response()->json($data);
    }

    public function postOrder()
    {
        $order = Order::where('id',3)->first();

        $res = CloseOrder::dispatch($order)->delay(now()->addMinute(1));
        return response()->json($res);
    }
}
