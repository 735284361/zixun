<?php

namespace App\Http\Controllers\Mini;

use App\Models\Order;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    //

    public function test()
    {
        $order = Order::where('order_no','ZX201911080005')->first();
        MessageService::paySuccessMsg($order);
    }

}
