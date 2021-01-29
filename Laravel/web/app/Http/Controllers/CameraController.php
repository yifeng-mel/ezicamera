<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CameraController extends Controller
{
    public function index() {
        return view('camera.index');
    }

    public function token() {
        $token = request()->get('token');
        $command = "sudo bash /scripts/start_streaming " . $token;
        shell_exec($command);
    }
}
