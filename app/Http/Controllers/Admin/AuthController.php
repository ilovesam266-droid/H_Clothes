<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

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
        $remember = $request->boolean('remember');

        $user = $this->authService->loginService($credentials, $remember);

        if ($user) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $user->createToken('api-token')->plainTextToken,
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong account or password',
            ], 422);
        }

        return back()->withErrors([
            'login_string' => 'Wrong account or password',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $this->authService->logoutService();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }

        return redirect(route('admin.loginShow'))->with('info', 'You are logged out');
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
