<?php

namespace App\Http\Middleware;

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
        return [
            ...parent::share($request),
            'auth' => [
                // Only share minimal identity info: username and role (plus id/email)
                'user' => $request->user() ? [
                    'id' => $request->user()->id_user,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    // username: prefer admin.nama, then mitra.pic, then email local part
                    'username' => $request->user()->admin?->nama
                        ?? $request->user()->mitra?->pic
                        ?? preg_replace('/@.*$/', '', $request->user()->email),
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
                'generated_password' => $request->session()->get('generated_password'),
            ],
        ];
    }
}
