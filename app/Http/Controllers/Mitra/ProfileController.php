<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show the mitra profile page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $mitra = $user->mitra;

        // Get kerjasama data with related kategori
        $kerjasama = $mitra->kerjasama()
            ->with('kategori')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Mitra/Profile', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $mitra,
            'kerjasama' => $kerjasama,
            'activeTab' => 'pengajuan',
        ]);
    }

    /**
     * Show the profile edit form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Mitra/Profile/Edit', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $user->mitra,
        ]);
    }

    /**
     * Update the partner profile.
     */
    public function update(Request $request)
    {
        $mitra = $request->user()->mitra;

        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string',
            'no_handphone' => 'required|string',
            'alamat' => 'required|string',
        ]);

        $mitra->update($request->only([
            'nama_perusahaan',
            'pic',
            'no_handphone',
            'alamat',
        ]));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    // Logo upload removed: schema has no logo field.
}