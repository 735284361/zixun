<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\UsersAccount;
use App\Services\OrdersService;
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
        // TODO 测试自动完成订单
        $this->order = $order;
        $this->delay($deley);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // 订单结束
        // 只有订单状态为已支付 才进行订单关闭的操作
        if ($this->order->status != Order::ORDER_PAID) {
            return;
        }
        // 进入订单完成流程
        $orderService = new OrdersService();
        $orderService->completeOrder($this->order);
        return;
    }
}
