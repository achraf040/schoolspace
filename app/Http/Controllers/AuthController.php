<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé.']);
            }
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                // Redirection vers le dashboard des travailleurs
                return redirect()->route('worker.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}