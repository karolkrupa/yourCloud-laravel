<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Database\Query\Expression;

class UserController extends Controller
{
    public function findUser($name)
    {
        $users = \App\User::where('name', 'REGEXP', '^'.$name)
            ->get()
            ->toArray();

        foreach($users as &$user) {
            $user = array_intersect_key($user, ['id' => '', 'name'=> '']);
        }

        return Response()->json($users, 201);
    }
}
