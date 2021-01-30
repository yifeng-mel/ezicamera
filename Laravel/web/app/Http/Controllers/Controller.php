<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use phpseclib\Crypt\RSA;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * $data array|string
     * @return byte array json
     */
    public function encryptData($data)
    {
        $data = json_encode($data);

        $cloud_public_key_path = '/rsa_keys/cloud_id_rsa.pub';
        $camera_private_key_path = '/rsa_keys/camera_id_rsa';
  
        $rsa = new RSA();
        // encrypt link with cloud public key
        $rsa->loadKey(file_get_contents($cloud_public_key_path));
        $cloud_public_key_encrypted_data = $rsa->encrypt($data);
        $cloud_public_key_encrypted_data_byte_array = unpack("C*",$cloud_public_key_encrypted_data);
        $cloud_public_key_encrypted_data_byte_array_json = json_encode($cloud_public_key_encrypted_data_byte_array);
  
        // encrypt data with camera private key
        $rsa->loadKey(file_get_contents($camera_private_key_path));
        $camera_private_key_encrypted_data = $rsa->encrypt($cloud_public_key_encrypted_data_byte_array_json);
        $camera_private_key_encrypted_data_byte_array = unpack("C*",$camera_private_key_encrypted_data);
        $camera_private_key_encrypted_data_byte_array_json = json_encode($camera_private_key_encrypted_data_byte_array);
                
        return $camera_private_key_encrypted_data_byte_array_json;
    }
}
