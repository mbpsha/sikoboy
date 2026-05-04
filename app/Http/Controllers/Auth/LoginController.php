<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
            'login' => 'required|string',
            'password' => 'required',
            'g-recaptcha-response' => 'nullable|string',
        ]);

        $loginVal = trim((string) $request->login);
        $normalized = mb_strtolower($loginVal);

        // Try email first (if user entered an email)
        $user = null;
        if (filter_var($loginVal, FILTER_VALIDATE_EMAIL)) {
            $user = User::query()->whereRaw('LOWER(email) = ?', [$normalized])->first();
        }

        // If not found by email, try matching admin username (`admins.nama`) case-insensitively
        if (! $user) {
            $admin = Admin::query()->whereRaw('LOWER(nama) = ?', [$normalized])->first();
            if ($admin) {
                $user = $admin->user;
            }
        }

        // As a final fallback, try matching email exactly (in case input wasn't validated as email)
        if (! $user) {
            $user = User::query()->whereRaw('LOWER(email) = ?', [$normalized])->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Email/Username atau password salah.']);
        }

        if ($user->role === 'mitra') {
            $captchaToken = (string) $request->input('g-recaptcha-response', '');
            $captchaSecret = (string) config('services.recaptcha.secret');

            // Verify CAPTCHA for mitra login.
            $captchaResponse = Http::asForm()->timeout(5)->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => $captchaSecret,
                    'response' => $captchaToken,
                    'remoteip' => $request->ip(),
                ]
            )->json();

            if (empty($captchaSecret) || empty($captchaResponse['success'])) {
                return back()->withErrors(['email' => 'CAPTCHA gagal, coba lagi.']);
            }
        }

        if ($user->role === 'mitra' && ! $user->isMitraVerified()) {
            return back()->withErrors([
                'login' => 'Akun mitra Anda belum diverifikasi admin.',
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