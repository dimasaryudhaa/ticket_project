<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login', [
            'isSecure' => true,
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('tickets.index'))
            ->with('success', 'Selamat datang kembali!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function status(Request $request): View
    {
        $loginAttempts = [];
        
        if (class_exists(\App\Models\LoginAttempt::class)) {
            $loginAttempts = \App\Models\LoginAttempt::secure()
                ->where('email', Auth::user()?->email ?? $request->input('email', ''))
                ->latest()
                ->take(10)
                ->get();
        }

        return view('auth.status', [
            'attempts' => $loginAttempts,
            'isSecure' => true,
        ]);
    }
}