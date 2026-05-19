<?php

namespace App\Http\Middleware;

use App\Support\NotificationFeed;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $mitraNotifications = collect();
        $adminNotifications = collect();

        if ($user?->role === 'mitra') {
            $mitraNotifications = NotificationFeed::forMitra($user, 50);
        }

        if ($user?->role === 'admin') {
            $adminNotifications = NotificationFeed::forAdmin(50);
        }

        return [
            ...parent::share($request),
            'auth' => [
                // Only share minimal identity info: username and role (plus id/email)
                'user' => $user ? [
                    'id' => $user->id_user,
                    'email' => $user->email,
                    'role' => $user->role,
                    // username: prefer admin.nama, then mitra.pic, then email local part
                        'username' => $user->admin?->nama
                            ?? $user->mitra?->pic
                            ?? preg_replace('/@.*$/', '', $user->email),
                        // include division for admins so frontend can show it
                        'divisi' => $user->admin?->divisi ?? null,
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
                'generated_password' => $request->session()->get('generated_password'),
            ],
            'recaptcha_site_key' => ($request->getHost() === 'localhost' || $request->getHost() === '127.0.0.1') ? null : config('services.recaptcha.key'),
            'notifications' => $mitraNotifications->take(5)->values()->all(),
            'notifications_count' => $mitraNotifications->count(),
            'admin_notifications' => $adminNotifications->take(5)->values()->all(),
            'admin_notifications_count' => $adminNotifications->count(),
        ];
    }
}
