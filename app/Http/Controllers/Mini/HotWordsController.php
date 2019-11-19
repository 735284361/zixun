<?php

namespace App\Http\Controllers\Mini;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wechat\Model\HotWordsModel;

class HotWordsController extends Controller
{
    //

    public function lists(Request $request)
    {
        $perPage = $request->per_page;

        $list = HotWordsModel::where()->paginate($perPage);
    }

}
