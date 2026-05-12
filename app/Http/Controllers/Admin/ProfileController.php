<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Http\Requests\Admin\UpdateAdminProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Return the current admin profile data (JSON-only endpoint).
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'email' => $user->email,
            ],
            'admin' => $user->admin ? [
                'nama' => $user->admin->nama,
                'divisi' => $user->admin->divisi,
            ] : null,
        ]);
    }

    /**
     * Update admin profile data.
     */
    public function update(UpdateAdminProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->update([
            'email' => $validated['email'],
        ]);

        $user->admin()->updateOrCreate(
            ['id_user' => $user->id_user],
            [
                'nama' => $validated['nama'],
                'divisi' => $validated['divisi'] ?? '',
            ]
        );

        return back()->with('success', 'Profil admin berhasil diperbarui.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(UpdateAdminPasswordRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
