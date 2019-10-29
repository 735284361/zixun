<?php

namespace App\Http\Controllers\Mini;

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
        $data = Cache::get('ORDER_CONFIRM:3');
        return response()->json($data);
    }

    public function jianting()
    {
        Log::warning('start');
        $redis=Redis::connection('publisher');//创建新的实例
        $redis->subscribe(['__keyevent@*__:expired'], function ($message, $channel) {
            Log::warning($message.PHP_EOL);
            echo $channel.PHP_EOL;//订阅的频道
            echo $message.PHP_EOL;//过期的key
            echo '---'.PHP_EOL;
        });
    }

    public function postOrder()
    {

    }
}
