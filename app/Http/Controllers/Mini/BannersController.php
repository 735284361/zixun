<?php

namespace App\Http\Controllers\Mini;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function Psy\debug;

class BannersController extends Controller
{
    //

    // Banner 列表
    public function lists()
    {
        return Banner::where('status',Banner::STATUS_ONLINE)->get();
    }
}
