<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CompleteOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $deley)
    {
        //
        $this->order = $order;
        $this->delay($deley);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 订单结束
        // 只有订单状态为已支付 才进行订单关闭的操作
        if ($this->order->status != Order::ORDER_PAID) {
            return;
        }

        DB::transaction(function() {
            // 更新订单状态
            $this->order->status = Order::ORDER_COMPLETED;
            $this->order->save();


        });
        // 修改讲师咨询时长
        // 修改用户咨询时长
        // 讲师分成入账


    }
}
