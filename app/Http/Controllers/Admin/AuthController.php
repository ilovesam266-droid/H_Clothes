<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function loginShow()
    {
        return view('admin.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $attempt = $this->authService->loginService($credentials);

        //check user info for login
        if (Auth::attempt($attempt)) {
            // $request->session()->regenerate();
            $user = Auth::user();
            // return redirect()->intended(route('admin.dashboard'));
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'redirect' => route('admin.dashboard'),
                'token' => $user->createToken('api-token')->plainTextToken,
            ]);
        }
        return back()->withErrors([
            'login_string' => 'Wrong account or password',
        ])->withInput();
    }

    public function registerShow()
    {
        return view('admin.pages.auth.register');
    }

    public function forgotShow()
    {
        return view('admin.pages.auth.forgot');
    }
}
