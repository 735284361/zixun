<?php

namespace App\Http\Controllers\Mini;

use App\Models\HotWord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HotWordsController extends Controller
{
    //

    public function lists(Request $request)
    {
        $perPage = $request->input('per_page',5);
        return HotWord::where('status',HotWord::WORDS_STATUS_ENABLE)->paginate($perPage);
    }

}
