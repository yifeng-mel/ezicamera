<?php

namespace App\Http\Controllers;

use App\User;
use DB, Validator;
use GuzzleHttp\Client;

class ResetPasswordLinkController extends Controller
{
    public function getIndex()
    {
        $error = null;

        $token = request()->get('token');
        $time = time();
        
        $saved_token_object = DB::table('reset_password_tokens')->first();
        if (is_null($saved_token_object)) { $error = "Please check you link."; }

        $saved_token = $saved_token_object->token;
        $expired_at = $saved_token_object->expired_at;

        if ($time > $expired_at) { $error = "Your password reset link is expired, please request it again."; }
        if ($token != $saved_token) { $error = "Access denied" ; }

        return view('reset_password.index', ['error' => $error, 'token' => $token]);
    }

    public function postIndex()
    {
        $validator = Validator::make(request()->all(), [
            'password' => 'confirmed|min:8|required'
        ]);

        if ($validator->fails()) { view('reset_password.postIndex', ['error'=>'Passwords must be at least 8 characters in length']); }

        $error = null;

        $token = request()->get('token');
        $password = request()->get('password');
        $time = time();

        $saved_token_object = DB::table('reset_password_tokens')->first();
        if (is_null($saved_token_object)) { $error = "Please check you link."; }

        $saved_token = $saved_token_object->token;
        $expired_at = $saved_token_object->expired_at;

        if ($time > $expired_at) { $error = "Your password reset link is expired, please request it again."; }
        if ($token != $saved_token) { $error = "Access denied" ; }

        if ($error != null) { return view('reset_password.postIndex', ['error'=>$error]); }

        $user = User::where('id','>',0)->first();
        $user->password = bcrypt($password);
        $user->save();

        return view('reset_password.postIndex', ['error'=>$error]);
    }
}
