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

        // ✅ TIDAK ADA NOTIFIKASI DI SINI (profi.vue pakai dummy lokal sendiri)

        // Get latest kerjasama with periode info
        $latestKerjasama = $mitra->kerjasama()
            ->with('kategori', 'periodes')
            ->where('is_finalized', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($k) {
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
            // ✅ TIDAK ADA 'notifications' atau 'notifications_count' di sini
        ]);
    }

    /**
     * Show the notifications list page.
     */
    public function notifications(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        $mitra = $user->mitra;
        
        // 🔔 DUMMY NOTIFICATIONS UNTUK LIST NOTIFIKASI (ListNotif.vue)
        $allNotifications = [
            [
                'id' => 1,
                'type' => 'expiring_soon',
                'status_type' => 'expiring_soon',
                'title' => 'Kerjasama Anda akan berakhir dalam 90 hari',
                'message' => 'Masa kerjasama dengan SETDA Boyolali akan berakhir pada tanggal 2 Januari 2027. Harap perhatikan tanggal berakhir kerjasama Anda.',
                'days_left' => 90,
                'kerjasama_judul' => '"Perjanjian Kerja Sama antara Dinas Pemberdayaan Masyarakat dan Desa dan PT BPR Bank Boyolali (Perseroda) tentang Pengelolaan Atas Rekening Kas Desa Melalui PT BPR Bank Boyolali (Perseroda)" akan berakhir dalam 90 hari',
                'nomor_kerjasama' => '012/SP-KS/PT-ABC/V/2026',
                'tanggal_mulai' => '2026-01-02',
                'tanggal_berakhir' => '2027-01-02',
                'status' => 'Aktif',
                'created_at' => now()->subDays(5),
            ],
            [
                'id' => 2,
                'type' => 'expiring_soon',
                'status_type' => 'expiring_soon',
                'title' => 'Kerjasama Anda akan berakhir dalam 30 hari',
                'message' => 'Masa kerjasama dengan SETDA Boyolali akan berakhir pada tanggal 15 Desember 2026. Harap perhatikan tanggal berakhir kerjasama Anda.',
                'days_left' => 30,
                'kerjasama_judul' => 'Kerjasama Pengelolaan Aset Daerah antara PT Hamaz Sejahtera Group dengan Dinas Kesehatan Kabupaten Boyolali',
                'nomor_kerjasama' => '045/SP-KS/DINKES/VI/2026',
                'tanggal_mulai' => '2025-12-15',
                'tanggal_berakhir' => '2026-12-15',
                'status' => 'Aktif',
                'created_at' => now()->subDays(2),
            ],
            [
                'id' => 3,
                'type' => 'expired',
                'status_type' => 'expired',
                'title' => 'Kerjasama telah berakhir',
                'message' => 'Masa kerjasama telah berakhir pada tanggal 15 Oktober 2026. Hubungi admin untuk informasi lebih lanjut.',
                'days_left' => null,
                'kerjasama_judul' => 'Kerjasama Penyediaan Layanan Digital',
                'nomor_kerjasama' => '078/SP-KS/SETDA/III/2025',
                'tanggal_mulai' => '2024-10-15',
                'tanggal_berakhir' => '2026-10-15',
                'status' => 'Expired',
                'created_at' => now()->subDays(10),
            ],
        ];

        return Inertia::render('Mitra/Profile/ListNotif', [
            'allNotifications' => $allNotifications,
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
}