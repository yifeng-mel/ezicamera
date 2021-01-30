<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class ProfileController extends Controller
{
    public function index() {
        return view('profile.index');
    }

    public function postIndex() {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date_format:m/d/Y',
            'password' => 'confirmed|min:8|nullable'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $user->first_name = request()->get('first_name');
        $user->last_name = request()->get('last_name');
        $user->date_of_birth = request()->get('date_of_birth');
        if (request()->has('password') && !empty(request()->get('password'))) {
            $user->password = bcrypt(request()->get('password'));
        }
        $user->save();

        session()->flash('profile_updated', 'Profile is updated successfully!');
        return redirect('/profile');
    }
}
