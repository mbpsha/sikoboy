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
    public function showLoginForm(Request $request)
    {
        if ($request->user()) {
            return match ($request->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'mitra' => redirect()->route('home'),
                default => redirect()->route('home'),
            };
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $normalizedEmail = mb_strtolower(trim((string) $request->email));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        if ($user->role === 'mitra' && ! $user->isMitraVerified()) {
            return back()->withErrors([
                'email' => 'Akun mitra Anda belum diverifikasi admin.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        // Regenerate session after login to prevent session fixation.
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            // Send admin users to the Dashboard route
            return redirect()->route('admin.dashboard');
        }

        // Mitra users: go to the public welcome page (Home)
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