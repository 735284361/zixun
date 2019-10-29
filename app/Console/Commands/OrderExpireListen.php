<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrderExpireListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '监听订单创建，在1分钟后如果没付款取消订单。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
//        Log::warning('start');
//        $cachedb = config('database.redis.cache.database', 0);
//        $pattern = '__keyevent@' . $cachedb . '__:expired';
//        Redis::psubscribe($pattern, function ($channel) {     // 订阅键过期事件
//            // laravel_cache:ORDER_CONFIRM:7 这样的格式
//            Log::warning('continue1');
//            $channel = trim(strstr($channel, ':'), ':');
//            $key_type = str_before($channel, ':');
//            switch ($key_type) {
//                case 'ORDER_CONFIRM':
//                    Log::warning('continue2');
//                    $order_id = str_after($channel, ':');    // 取出订单 ID
//                    DB::enableQueryLog();
//                    $order = Order::query()->find($order_id);
//                    $sql = DB::getQueryLog();
//                    Log::warning($sql);
//                    if ($order) {
//                        // 执行取消操作
//                        $order->status = 2;
//                        $order->save();
//                    }
//                    break;
//                case 'ORDER_OTHEREVENT':
//                    break;
//                default:
//                    break;
//            }
//        });

        Log::warning('start');
        $redis=Redis::connection('publisher');//创建新的实例
        $redis->psubscribe(['__keyevent@*__:expired'], function ($message, $channel) {
            Log::warning('continue');
            echo $channel.PHP_EOL;//订阅的频道
            echo $message.PHP_EOL;//过期的key
            echo '---'.PHP_EOL;
        });
    }
}
