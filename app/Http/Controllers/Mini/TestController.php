<?php

namespace App\Http\Controllers\Mini;

use App\Models\Order;
use App\Models\Teacher;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    //

    public function test()
    {
//        $order = Order::where('order_no','ZX201911080025')->first();
//        MessageService::paySuccessMsg($order);

//        $teacher = Teacher::where('id',1)->first();
//        $teacher = $teacher->toArray();
//        dd($teacher);

        echo time();
    }


}
