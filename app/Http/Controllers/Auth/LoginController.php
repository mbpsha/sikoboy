<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request, $role = null)
    {
        if ($role && in_array($role, ['admin', 'mitra'])) {
            return Inertia::render('Auth/Login', ['role' => $role]);
        }
        return Inertia::render('Auth/RoleSelection');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,mitra'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        if ($user->role !== $request->role) {
            return back()->withErrors(['role' => 'Akses tidak sesuai.']);
        }

        Auth::login($user, $request->boolean('remember'));

        // Regenerate session to prevent session fixation and ensure auth persists
        $request->session()->regenerate();

        // Redirect to the public welcome page as requested
        return redirect()->route('home');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
