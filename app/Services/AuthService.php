<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function loginService(array $credentials, bool $remember = false): ?\App\Models\User
    {
        $loginField = filter_var($credentials['login_string'], FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        $attempt = [
            $loginField => $credentials['login_string'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attempt, $remember)) {
            return Auth::user();
        }

        return null;
    }

    public function logoutService(): void
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        if (Auth::user()) {
            Auth::user()->currentAccessToken()?->delete();
        }
    }
}
