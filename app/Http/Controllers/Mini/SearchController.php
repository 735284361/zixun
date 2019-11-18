<?php

namespace App\Http\Controllers\Mini;

use App\Search\TeacherSearch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    //
    public function search(Request $request)
    {
        return TeacherSearch::apply($request);
    }

}
