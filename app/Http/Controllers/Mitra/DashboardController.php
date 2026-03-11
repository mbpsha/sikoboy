<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the partner dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $mitra = $user->mitra;

        return Inertia::render('Mitra/Dashboard', [
            'mitra' => $mitra,
            'stats' => [
                'member_since' => $user->created_at->format('d F Y'),
                'last_login' => $user->last_login_at?->diffForHumans(),
                'verification_status' => $user->email_verified_at ? 'verified' : 'unverified'
            ]
        ]);
    }

    /**
     * Show the form to complete profile.
     */
    public function completeProfile(Request $request)
    {
        if ($request->user()->mitra) {
            return redirect()->route('mitra.dashboard');
        }

        return Inertia::render('Mitra/CompleteProfile');
    }

    /**
     * Store the completed profile.
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string|max:100',
            'no_handphone' => 'required|string|max:15',
            'alamat' => 'required|string',
            'provinsi' => 'required|string|max:100',
            'kabupaten_kota' => 'required|string|max:100'
        ]);

        Mitra::create(array_merge(
            ['id_user' => $request->user()->id],
            $request->only([
                'nama_perusahaan', 'npwp', 'pic', 'jabatan_pic',
                'no_handphone', 'no_telepon', 'alamat', 'provinsi',
                'kabupaten_kota', 'kecamatan', 'kode_pos',
                'bidang_usaha', 'website'
            ])
        ));

        return redirect()->route('mitra.dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }
}
