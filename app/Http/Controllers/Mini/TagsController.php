<?php

namespace App\Http\Controllers\Mini;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagsController extends Controller
{
    //

    public function lists(Request $request)
    {
        $perPage = $request->input('per_page',4);

        return Tag::where('status',Tag::STATUS_ENABLE)->paginate($perPage);
    }
}
