<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, DB;
use App\User;
use App\Configuration;

class InitialSetupController extends Controller
{
    public function getIndex()
    {
        return view('initial_setup.index');
    }

    public function postIndex()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'date_of_birth' => 'required|date_format:m/d/Y',
            'password' => 'confirmed|min:8|nullable'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::first();
        $camera_uid = DB::table('configurations')->where('key', 'camera_uid')->first()->value;

        $user->email = request()->get('email');
        $user->date_of_birth = request()->get('date_of_birth');
        $user->password = bcrypt(request()->get('password'));
        $user->save();

        $user_data = ['email'=>$user->email, 'date_of_birth'=>$user->date_of_birth];

        $url = "https://ezicamera.com/api/v1/set-user-info";
        $guzzle_client = new \GuzzleHttp\Client();
        $request = $guzzle_client->post($url, ['form_params'=>['camera_uid'=>$camera_uid, 'data'=>$this->encryptData($user_data)]]);
        
        $configuration = new Configuration();
        $configuration->key = 'initial_setup_done';
        $configuration->value = 'true';
        $configuration->save();

        return redirect('/camera');
    }
}
