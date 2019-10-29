<?php

namespace App\Http\Controllers\Mini;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

    public function postOrder()
    {

    }
}
