<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    
    /**
     * return the login html form 
     * 
     */
    public function getLogin() {
        return view('admin.Auth.login');
    }

    /**
     * receive the request from the " form method=post "
     */
    public function login(LoginRequest $request) {
        //  make validation

        // check validation
        $remember_me = $request -> has('remember_me') ? true : false;

        if(auth()->guard('admin') ->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {

            return redirect(route('admin.dashboard'));
        }
        return redirect() -> back() -> with(['error' => 'هناك خظأ بالبيانات ']) ;

    }
}
