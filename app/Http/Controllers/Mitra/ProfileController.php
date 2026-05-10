<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        $mitra = $user->mitra;
        
        // Get kerjasama statistics
        $kerjasama = $mitra->kerjasama()->where('is_finalized', true)->get();
        $stats = [
            'total_pengajuan' => $kerjasama->count(),
            'disetujui' => $kerjasama->where('status_persetujuan', 'disetujui')->count(),
            'dalam_proses' => $kerjasama->where('status_persetujuan', 'dalam_proses')->count(),
            'pending' => $kerjasama->where('status_persetujuan', null)->count(),
        ];

        // Get latest kerjasama with periode info
        $latestKerjasama = $mitra->kerjasama()
            ->with('kategori', 'periodes')
            ->where('is_finalized', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($k) {
                // Calculate periode duration from latest periode
                $lastPeriode = collect($k->periodes)->sortByDesc('tanggal_berakhir')->first();
                $periodeText = '-';
                
                if ($lastPeriode) {
                    $start = Carbon::parse($lastPeriode->tanggal_mulai);
                    $end = Carbon::parse($lastPeriode->tanggal_berakhir);
                    $years = (int) $start->diffInYears($end);
                    $periodeText = $years > 0 ? $years . ' Tahun' : '< 1 Tahun';
                }
                
                return [
                    'id_kerjasama' => $k->id_kerjasama,
                    'judul' => $k->judul,
                    'status' => $k->status_persetujuan ?? 'pending',
                    'kategori' => $k->kategori?->nama_kategori ?? '-',
                    'urusan' => $k->urusan ?? '-',
                    'tanggal_daftar' => $k->created_at?->format('d M Y') ?? '-',
                    'periode' => $periodeText,
                ];
            });

        return Inertia::render('Mitra/Profile/Profile', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $mitra,
            'stats' => $stats,
            'kerjasama_list' => $latestKerjasama,
        ]);
    }

    /**
     * Show profile completion form for mitra without profile.
     */
    public function completeProfile(Request $request)
    {
        if ($request->user()->mitra) {
            return redirect()->route('mitra.profile.edit');
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

        return redirect()->route('mitra.profile.edit')
            ->with('success', 'Profil berhasil dilengkapi.');
    }

    /**
     * Show the profile edit form.
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