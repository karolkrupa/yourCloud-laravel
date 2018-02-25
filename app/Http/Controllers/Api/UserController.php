<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function findUser($name)
    {
        $users = User::where('name', 'REGEXP', '^'.$name)
            ->get()
            ->toArray();

        foreach($users as &$user) {
            $user = array_intersect_key($user, ['id' => '', 'name'=> '']);
        }

        return Response()->json($users, 201);
    }

    public function updateFullName(Request $request, $userId = null) {
        if($userId == null) {
            $user = Auth::user();
        }else {
            $user = User::find($userId);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|max:40'
        ]);

        if($validator->fails()) {
            return Response()->json([
                'message' => $validator->errors()->first('full_name'),
                'code' => 404
            ], 404);
        }

        $user->full_name = $request->get('full_name');

        if($user->save()) {
            return Response()->json([
                'message' => 'Successfully updated',
                'new_full_name' => $user->full_name,
                'code' => 201
            ], 201);
        }else {
            return Response()->json([
                'message' => 'Unexpected error',
                'code' => 404
            ], 404);
        }
    }

    public function updateLanguage(Request $request) {
        $validator = Validator::make($request->all(), [
            'lang' => [
                'required',
                Rule::in(config('app.languages'))
            ]
        ]);

        if($validator->fails()) {
            return Response()->json([
                'message' => $validator->errors()->first('lang'),
                'code' => 404
            ], 404);
        }

        $user = Auth::user();
        $user->lang = $request->get('lang');

        if($user->save()) {
            App::setlocale($user->lang);
            return Response()->json([
                'message' => 'Successfully updated. The page will be refreshed in a moment.',
                'new_lang' => $user->lang,
                'code' => 201
            ], 201);
        }else {
            return Response()->json([
                'message' => 'Unexpected error',
                'code' => 404
            ], 404);
        }
    }

    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed'
        ]);

        if($validator->fails()) {
            return Response()->json([
                'message' => $validator->errors()->first('password'),
                'code' => 404
            ], 404);
        }

        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));

        if($user->save()) {
            return Response()->json([
                'message' => 'Successfully updated',
                'new_lang' => $user->lang,
                'code' => 201
            ], 201);
        }else {
            return Response()->json([
                'message' => 'Unexpected error',
                'code' => 404
            ], 404);
        }
    }
}
