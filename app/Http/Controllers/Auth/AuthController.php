<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $request->validate(['email' => ['required']]); // keep error shape simple
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $user = Auth::user();
        if ($user->is_suspended) {
            Auth::logout();

            return back()->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }
        $user->update(['last_login_at' => now()]);

        return redirect()->intended($user->dashboardUrl());
    }

    public function showRegister(Request $request)
    {
        return view('auth.register', ['role' => in_array($request->query('role'), ['candidate', 'employer'], true) ? $request->query('role') : 'candidate']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:candidate,employer'],
        ]);
        $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password']), 'role' => $data['role']]);
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('onboarding.show')->with('status', 'Welcome to TalentFlow! Complete your onboarding to get started.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
