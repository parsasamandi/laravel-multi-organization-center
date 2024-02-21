<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\StoreLoginRequest;
use Redirect;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/centerList';


    // Show Login login
    public function index() {
        return view('login');
    }

    // Store Login
    public function store(StoreLoginRequest $request) {
        // Remember Token
        $remember = $request->get('remember_me');

        $remember_me = false;
        if(isset($remember)) {
            $remember_me = true;
        }

        // Auth
        $credentials = $request->only('email', 'password');
        if (Auth::attempt(($credentials), $remember_me)) {
            // Authentication passed...
            return redirect()->intended('/adminHome');
        }
        return Redirect::back()->withErrors('رمز عبور یا ایمیل شما نادرست است');
    }
    
    //logout
    public function logout(Request $request) {
        Auth::logout();
        return redirect('login');
    }

}
