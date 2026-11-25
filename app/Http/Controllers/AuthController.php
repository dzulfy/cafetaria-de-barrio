<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status_aktif' => true,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah, atau akun Anda nonaktif.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route(Auth::user()->homeRoute());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Registrasi hanya untuk peran Pelanggan.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'peran' => 'pelanggan',
            'status_aktif' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRoute());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar.');
    }
}
