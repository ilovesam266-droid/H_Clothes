<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function loginService($credentials){

        $loginField = filter_var($credentials['login_string'], FILTER_VALIDATE_EMAIL) ? 'email':'user_name';
        return [
            $loginField => $credentials['login_string'],
            'password' => $credentials['password'],
        ];
    }

    public function logoutService(){
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/admin/login')->with('info', 'You are logout');
    }
}
