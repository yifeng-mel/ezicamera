<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CameraController extends Controller
{
    public function index() {
        if (is_null(auth()->user()->date_of_birth)) {
            session()->flash('please_complete_profile', 'Please complete your profile:');
            return redirect('/profile');
        }
        return view('camera.index');
    }

    public function token() {
        $host = request()->getHost();
        $ws_server = "wss://" . $host . "/wss/";
        $token = request()->get('token');
        $command = "sudo /bin/bash /scripts/start_streaming.bash " . $token . " " . $ws_server . " &>> /log/webrtc-sendrecv.txt";
        shell_exec($command);
    }
}
