<?php

namespace App\Http\Controllers\Mini;

use App\Models\Order;
use App\Models\Teacher;
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    //

    public function test()
    {
//        $time = strtotime(date('Y-m-d')) - 24*3600;
//
//        for ($i = 0; $i < 100; $i++) {
//            $time += 7 * 24 * 3600;
//            echo date('Y.m.d',$time)."<br>";
//        }
        echo Carbon::now();
    }


}
