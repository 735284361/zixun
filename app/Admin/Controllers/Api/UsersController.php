<?php

namespace App\Admin\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    //

    public function users(Request $request)
    {
        $q = $request->get('q');
        return User::where('username', 'like', "%$q%")->paginate(null, ['uid as id', 'username as text']);
    }
}
