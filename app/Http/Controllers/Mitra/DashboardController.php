<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
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
                'last_login' => null,
                'verification_status' => null,
            ],
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
            'pic' => 'required|string',
            'no_handphone' => 'required|string',
            'alamat' => 'required|string',
        ]);

        Mitra::create([
            'id_user' => $request->user()->id_user,
            'nama_perusahaan' => $request->nama_perusahaan,
            'no_handphone' => $request->no_handphone,
            'pic' => $request->pic,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('mitra.dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }
}
