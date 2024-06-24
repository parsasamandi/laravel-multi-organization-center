<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Center;
use Redirect;

class LoginController extends Controller
{
    // Show Login Form
    public function index() {
        return view('login');
    }


    // Override the username method to use 'phone_number' as the identifier
    public function username()
    {
        return 'phone_number';
    }

    // Store Login
    public function store(StoreLoginRequest $request)
    {
        $credentials = $request->only('phone_number', 'password');
        $remember_me = $request->has('remember_me');
        
        if (Auth::attempt($credentials, $remember_me, 'centers')) {
            // Authentication passed...
            return redirect()->intended('/');
        }
            
        return Redirect::back()->withErrors('رمز یا شماره تلفن شما نادرست است');
    }
    
    // Logout
    public function logout(Request $request) {
        
        Auth::logout();

        return redirect('login');
    }
}
