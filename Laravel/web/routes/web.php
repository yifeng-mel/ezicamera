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
    return view('welcome');
});

Route::get('/connect-wifi', 'ConnectWifiController@getConnectWifi');
Route::post('/connect-wifi', 'ConnectWifiController@postConnectWifi');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(
    ['middleware' => ['auth']], 
    function () {
        Route::get('/api/videos/filter', 'VideoController@filter');
        Route::get('/api/videos/token', 'VideoController@token');
        
        Route::get('/camera', 'CameraController@index');
        Route::post('/camera/token', 'CameraController@token');
        Route::get('/videos', 'VideoController@index');
        Route::get('/profile', 'ProfileController@index');
    }
);