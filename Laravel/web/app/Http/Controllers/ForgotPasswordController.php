<?php
 
namespace App\Http\Controllers;
 
use App\User;
use DB;
use GuzzleHttp\Client;
use phpseclib\Crypt\RSA;
 
class ForgotPasswordController extends Controller
{
   public function index()
   {
       $user = User::where('id', '>', 0)->first();
       $show_date_of_birth = $user->date_of_birth ? true : false;
       return view('forgot_password.index', ['show_date_of_birth'=>$show_date_of_birth]);
   }
 
   public function postIndex()
   {
       $email = request()->get('email', null);
       $date_of_birth = request()->get('date_of_birth', null);
       $user = User::where(['email'=>$email, 'date_of_birth'=>$date_of_birth])->first();
       if (is_null($user)) {
           session()->flash('identification_failed', 'Information provided is not correct, please try it again.');
           return redirect('/forgot-password');
       }
 
       $reset_token = $this->random_str();
       DB::table('reset_password_tokens')->truncate();
       DB::table('reset_password_tokens')->insert(['token'=> $reset_token, 'expired_at'=>strtotime("+15 minutes")]);
       $host = request()->getHost();
 
       $link = 'https://' . $host . '/reset-password?token=' . urlencode($reset_token);
       $user_name = $user->first_name ?? "";
 
       $camera_uid = DB::table('configurations')->where('key', 'camera_uid')->first()->value;
       $this->sendEmail($camera_uid, $user_name, $email, $link);
 
       return redirect('/password-reset-email-sent');
   }
 
   public function sendEmail($camera_uid, $user_name, $email_address, $link)
   {
       $camera_uid = \DB::table('configurations')->where(['key'=>'camera_uid'])->first()->value;
       $cloud_public_key_path = '/rsa_keys/cloud_id_rsa.pub';
       $camera_private_key_path = '/rsa_keys/camera_id_rsa';
 
       $rsa = new RSA();
       // encrypt link with cloud public key
       $rsa->loadKey(file_get_contents($cloud_public_key_path));
       $encrypted_link = $rsa->encrypt($link);
       $encrypted_link_byte_array = unpack("C*",$encrypted_link);
       $encrypted_link_byte_array_json = json_encode($encrypted_link_byte_array);
 
       // encrypt data with camera private key
       $rsa->loadKey(file_get_contents($camera_private_key_path));
       $data = [
           'encrypted_link' => $encrypted_link_byte_array_json,
           'user_name' => $user_name,
           'email_address' => $email_address
       ];
       $data_str = json_encode($data);
       $encrypted_data_str = $rsa->encrypt($data_str);
       $encrypted_data_str_byte_array = unpack("C*",$encrypted_data_str);
       $encrypted_data_str_byte_array_json = json_encode($encrypted_data_str_byte_array);
 
       $url = "https://ezicamera.com/api/v1/send-email/password-reset-link";
       $guzzle_client = new Client();
       $response = $guzzle_client->post($url, ['form_params'=>['camera_uid'=>$camera_uid, 'data'=>$encrypted_data_str_byte_array_json]]);
   }
 
   /**
    * Generate a random string, using a cryptographically secure
    * pseudorandom number generator (random_int)
    *
    * This function uses type hints now (PHP 7+ only), but it was originally
    * written for PHP 5 as well.
    *
    * For PHP 7, random_int is a PHP core function
    * For PHP 5.x, depends on https://github.com/paragonie/random_compat
    *
    * @param int $length      How many characters do we want?
    * @param string $keyspace A string of all possible characters
    *                         to select from
    * @return string
    */
   function random_str(
       int $length = 64,
       string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
   ): string {
       $pieces = [];
       $max = mb_strlen($keyspace, '8bit') - 1;
       for ($i = 0; $i < $length; ++$i) {
           $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
 }
 