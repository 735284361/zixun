<?php

namespace App\Http\Controllers\Mini;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeachersController extends Controller
{
    //

    public function lists()
    {
        $list = Teacher::with(['tags' => function($query) {
            // 老师标签
            $query->select('tag');
        }])->get();
        return $list;
    }
}
