<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Mitra/Profile/Edit', [
            'user' => [
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ],
            'mitra' => $user->mitra
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
            'npwp' => 'nullable|string|max:20|unique:mitras,npwp,' . $mitra->id_user . ',id_user',
            'pic' => 'required|string|max:100',
            'jabatan_pic' => 'nullable|string|max:100',
            'no_handphone' => 'required|string|max:15',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'required|string',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'bidang_usaha' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255'
        ]);

        $mitra->update($request->all());

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Upload company logo.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $mitra = $request->user()->mitra;

        // Delete old logo if exists
        if ($mitra->logo_perusahaan) {
            Storage::disk('public')->delete($mitra->logo_perusahaan);
        }

        // Store new logo
        $path = $request->file('logo')->store('logos', 'public');

        $mitra->update(['logo_perusahaan' => $path]);

        return back()->with('success', 'Logo berhasil diunggah.');
    }
}
