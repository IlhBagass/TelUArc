<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan halaman login/register
    public function showAuthForm()
    {
        return view('auth.login-register'); // resources/views/auth.blade.php
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->has('remember-me'))) {
            $request->session()->regenerate();
            return redirect('/'); // Redirect ke landing page
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users'],
            'email' => ['required','email','unique:users'],
            'password' => ['required','confirmed','min:6'],
            'bio' => ['nullable','string'],
            'avatar' => ['nullable','image','max:2048'],
        ]);

        $avatarPath = 'default.jpg';
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bio' => $request->bio,
            'avatar' => $avatarPath,
        ]);

        Auth::login($user);

        return redirect('/'); // Redirect ke landing page
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // Redirect ke landing page
    }
}
