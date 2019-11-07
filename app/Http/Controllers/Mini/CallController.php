<?php

namespace App\Http\Controllers\Mini;

use App\Services\CallService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CallController extends Controller
{
    //

    protected $callService;

    protected $origNum = '+8617600296639'; // A号码
    protected $privateNum = '+8617160095983'; // X号码(隐私号码)

    public function __construct()
    {
        $this->callService = new CallService();
    }

    public function bindAx()
    {
        return $this->callService->bindAx($this->origNum, $this->privateNum, 3);
    }

    public function cancelAxBind()
    {
        return $this->callService->cancelAxBind($this->origNum, $this->privateNum);
    }

}
