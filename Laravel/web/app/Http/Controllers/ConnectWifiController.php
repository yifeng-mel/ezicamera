<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConnectWifiController extends Controller
{
    public function getConnectWifi(Request $req)
    {
        $wifi_names = ['TP-LINK_22F1_D2', 'TP-LINK_33DB', '360Wifi-DJE3', 'CCMC-AJ5N', 'TP-LINK_22F1_D2'];

        return view('wifi.connect', compact('wifi_names'));
    }

    public function postConnectWifi(Request $req)
    {
        dd(request()->all());
        return view('wifi.connect', compact('wifi_names'));
    }
}
