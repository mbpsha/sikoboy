<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login.select');
        }

        $user = Auth::user();

        // Check if user has the required role
        if (!in_array(strtolower(trim($user->role)), array_map('strtolower', $roles))) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role === 'mitra' && ($user->status_verifikasi ?? 'disetujui') !== 'disetujui') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun mitra Anda belum diverifikasi admin.',
            ]);
        }

        // Check if mitra has complete profile
        if ($user->role === 'mitra' && !$user->mitra) {
            // Allow access to profile completion route
            if (!$request->routeIs('mitra.profile.complete') && !$request->routeIs('mitra.profile.store')) {
                return redirect()->route('mitra.profile.complete');
            }
        }

        return $next($request);
    }
}