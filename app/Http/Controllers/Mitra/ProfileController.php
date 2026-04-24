<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show profile completion form for mitra without profile.
     */
    public function completeProfile(Request $request)
    {
        if ($request->user()->mitra) {
            return redirect()->route('mitra.profile.index');
        }

        return Inertia::render('Mitra/Profile/Edit', [
            'user' => [
                'email' => $request->user()->email,
            ],
            'mitra' => null,
            'mode' => 'complete',
        ]);
    }

    /**
     * Store profile for first-time mitra completion.
     */
    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'no_handphone' => 'required|string|max:50',
            'alamat' => 'required|string',
        ]);

        $request->user()->mitra()->updateOrCreate(
            ['id_user' => $request->user()->id_user],
            $validated
        );

        return redirect()->route('mitra.profile.index')
            ->with('success', 'Profil berhasil dilengkapi.');
    }

    /**
     * PENTING: Show the profile page (dashboard/portal mitra)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        return Inertia::render('Mitra/Profile', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $user->mitra,
        ]);
    }

    /**
     * Show the profile EDIT form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        return Inertia::render('Mitra/Profile/Edit', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $user->mitra,
            'mode' => 'edit',
        ]);
    }
    
    /**
     * Update the partner profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'no_handphone' => 'required|string|max:50',
            'alamat' => 'required|string',
        ]);

        $request->user()->mitra()->updateOrCreate(
            ['id_user' => $request->user()->id_user],
            $validated
        );

        return redirect()->route('mitra.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
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
}