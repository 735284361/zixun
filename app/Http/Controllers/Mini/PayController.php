<?php

namespace App\Http\Controllers\Mini;

use App\Models\Order;
use App\Services\PayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{

    protected $payService;

    public function __construct()
    {
        $this->payService = new PayService();
    }

    public function test()
    {
        $orderNo = date('YmdHis').rand(100000,999999);

        return $this->payService->getPayParams($orderNo,1);
    }

}
