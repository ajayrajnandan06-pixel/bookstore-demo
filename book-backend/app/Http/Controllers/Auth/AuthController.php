<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     */
    // public function __construct()
    // {
    //     // Apply guest middleware only to showLoginForm method
    //     $this->middleware('guest')->except(['logout', 'dashboard']);
    // }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Redirect to intended URL or dashboard
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    /**
     * Show dashboard (after login)
     */
    public function dashboard()
    {
        if (Auth::check()) {
            return view('dashboard');
        }
        
        return redirect('/login')->withErrors([
            'email' => 'Please login to access the dashboard.',
        ]);
    }
}