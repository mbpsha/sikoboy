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

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Akun tidak aktif.']);
        }

        if ($user->role !== $request->role) {
            return back()->withErrors(['role' => 'Akses tidak sesuai.']);
        }

        if ($user->role === 'mitra' && !$user->email_verified_at) {
            return redirect()->route('verification.notice');
        }

        Auth::login($user, $request->boolean('remember'));
        
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        if (!$user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }
        
        return redirect()->route('mitra.dashboard');
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
