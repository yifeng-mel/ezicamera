<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/forgot-password', 'ForgotPasswordController@index');
Route::post('/forgot-password', 'ForgotPasswordController@postIndex');

Route::get('/password-reset-email-sent', function(){
    return view('forgot_password.password-reset-email-sent');
});

Route::get('/reset-password', 'ResetPasswordLinkController@getIndex');
Route::post('/reset-password', 'ResetPasswordLinkController@postIndex');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(
    ['middleware' => ['auth', 'check_initial_setup']], 
    function () {
        Route::get('/api/videos/filter', 'VideoController@filter');
        Route::post('/api/videos/token', 'VideoController@token');
        
        Route::get('/camera', 'CameraController@index');
        Route::post('/camera/token', 'CameraController@token');
        Route::get('/videos', 'VideoController@index');
        Route::get('/profile', 'ProfileController@index');
        Route::post('/profile', 'ProfileController@postIndex');
    }
);

Route::group(
    ['middleware'=>['auth']],
    function() {
        Route::get('/initial_setup', 'InitialSetupController@getIndex');
        Route::post('/initial_setup', 'InitialSetupController@postIndex');
    }
);