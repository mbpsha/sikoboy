<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mitra;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string|max:100',
            'no_handphone' => 'required|string|max:15',
            'alamat' => 'required|string',
            'provinsi' => 'required|string',
            'kabupaten_kota' => 'required|string',
            'terms' => 'required|accepted'
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mitra'
        ]);

        Mitra::create([
            'id_user' => $user->id_user,
            'nama_perusahaan' => $request->nama_perusahaan,
            'npwp' => $request->npwp,
            'pic' => $request->pic,
            'jabatan_pic' => $request->jabatan_pic,
            'no_handphone' => $request->no_handphone,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'kecamatan' => $request->kecamatan,
            'kode_pos' => $request->kode_pos,
            'bidang_usaha' => $request->bidang_usaha,
            'website' => $request->website
        ]);

        event(new Registered($user));

        return redirect()->route('login', ['role' => 'mitra'])
            ->with('success', 'Registrasi berhasil! Cek email untuk verifikasi.');
    }
}
