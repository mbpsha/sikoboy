<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mitra;
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
            'npwp' => 'nullable|string',
            'no_handphone' => 'required|string',
            'pic' => 'required|string',
            'alamat' => 'required|string'
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
            'no_handphone' => $request->no_handphone,
            'pic' => $request->pic,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('login', ['role' => 'mitra'])
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
