<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the mitra dashboard dengan perhitungan masa berlaku.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $mitra = $user->mitra;

        // Jika mitra belum lengkap profil
        if (!$mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        // =====================================================
        // HITUNG STATISTIK SEDERHANA & AKURAT
        // =====================================================
        $today = Carbon::today();
        $today6bulan = $today->clone()->addMonths(6);
        $today3bulan = $today->clone()->addMonths(3);

        // ✅ Total Kerjasama
        $totalKerjasama = DB::table('kerjasama')
            ->where('id_mitra', $mitra->id_mitra)
            ->where('is_finalized', 1)
            ->count();

        // ✅ Masa Berlaku < 6 Bulan
        // Cari periode terakhir untuk setiap kerjasama, lalu filter
        $masaBerlakuKurang6bulan = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->where('k.is_finalized', 1)
            // Hanya ambil periode dengan tanggal_berakhir terbesar per kerjasama
            ->whereRaw('p.id_periode_kerjasama = (
                SELECT id_periode_kerjasama FROM periode_kerjasama p2
                WHERE p2.id_kerjasama = k.id_kerjasama
                ORDER BY p2.tanggal_berakhir DESC
                LIMIT 1
            )')
            // Tanggal berakhir: lebih dari hari ini tapi kurang dari 6 bulan
            ->whereBetween('p.tanggal_berakhir', [$today->toDateString(), $today6bulan->toDateString()])
            ->distinct('k.id_kerjasama')
            ->count();

        // ✅ Masa Berlaku < 3 Bulan
        $masaBerlakuKurang3bulan = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->where('k.is_finalized', 1)
            ->whereRaw('p.id_periode_kerjasama = (
                SELECT id_periode_kerjasama FROM periode_kerjasama p2
                WHERE p2.id_kerjasama = k.id_kerjasama
                ORDER BY p2.tanggal_berakhir DESC
                LIMIT 1
            )')
            // Tanggal berakhir: lebih dari hari ini tapi kurang dari 3 bulan
            ->whereBetween('p.tanggal_berakhir', [$today->toDateString(), $today3bulan->toDateString()])
            ->distinct('k.id_kerjasama')
            ->count();

        // ✅ Masa Berlaku Habis (sudah expired)
        $masaBerlakuHabis = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->where('k.is_finalized', 1)
            ->whereRaw('p.id_periode_kerjasama = (
                SELECT id_periode_kerjasama FROM periode_kerjasama p2
                WHERE p2.id_kerjasama = k.id_kerjasama
                ORDER BY p2.tanggal_berakhir DESC
                LIMIT 1
            )')
            // Tanggal berakhir sudah lebih kecil dari hari ini
            ->where('p.tanggal_berakhir', '<', $today->toDateString())
            ->distinct('k.id_kerjasama')
            ->count();

        // ✅ Kerjasama Aktif (sedang berjalan)
        $kerjasamaAktif = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->where('k.is_finalized', 1)
            ->whereRaw('p.id_periode_kerjasama = (
                SELECT id_periode_kerjasama FROM periode_kerjasama p2
                WHERE p2.id_kerjasama = k.id_kerjasama
                ORDER BY p2.tanggal_berakhir DESC
                LIMIT 1
            )')
            // Tanggal mulai <= hari ini dan tanggal berakhir >= hari ini
            ->where('p.tanggal_mulai', '<=', $today->toDateString())
            ->where('p.tanggal_berakhir', '>=', $today->toDateString())
            ->distinct('k.id_kerjasama')
            ->count();

        $metrics = [
            'total_kerjasama' => $totalKerjasama,
            'masa_berlaku_kurang_6bulan' => $masaBerlakuKurang6bulan,
            'masa_berlaku_kurang_3bulan' => $masaBerlakuKurang3bulan,
            'masa_berlaku_habis' => $masaBerlakuHabis,
            'aktif' => $kerjasamaAktif,
        ];

        // =====================================================
        // CHART: Kerjasama per Tahun (hanya milik mitra)
        // =====================================================
        $kerjasamaPerTahun = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->where('k.is_finalized', 1)
            ->selectRaw('YEAR(p.tanggal_mulai) AS tahun, COUNT(DISTINCT k.id_kerjasama) AS total')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get()
            ->map(fn($r) => ['tahun' => (int) $r->tahun, 'total' => (int) $r->total])
            ->values();

        // =====================================================
        // CHART: Kategori Kerjasama (hanya milik mitra)
        // =====================================================
        $totalFinalized = $metrics['total_kerjasama'] ?: 1;

        $kategoriKerjasama = DB::table('kerjasama as k')
            ->leftJoin('kategori_kerjasama as kk', 'kk.id_kategori', '=', 'k.id_kategori')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->where('k.is_finalized', 1)
            ->whereNotNull('kk.nama_kategori')
            ->selectRaw('kk.nama_kategori as kategori, COUNT(*) AS total')
            ->groupBy('kk.nama_kategori')
            ->get()
            ->map(fn($r) => [
                'kategori' => $r->kategori,
                'total' => (int) $r->total,
                'persentase' => round($r->total / $totalFinalized * 100, 1),
            ])
            ->values();

        return Inertia::render('Mitra/Dashboard', [
            'mitra' => $mitra,
            'metrics' => $metrics,
            'kerjasama_per_tahun' => $kerjasamaPerTahun,
            'kategori_kerjasama' => $kategoriKerjasama,
            'stats' => [
                'member_since' => $user->created_at->format('d F Y'),
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