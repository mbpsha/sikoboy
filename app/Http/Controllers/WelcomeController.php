<?php

namespace App\Http\Controllers;

use App\Models\Potensi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Display the landing/welcome page dengan statistik publik.
     */
    public function __invoke()
    {
        // =====================================================
        // AMBIL DATA POTENSI
        // =====================================================
        $potensi = Potensi::query()
            ->with('poin')
            ->where('status_tampil', true)
            ->orderBy('kategori')
            ->orderBy('id_potensi')
            ->get()
            ->groupBy('kategori')
            ->map(function ($items) {
                return $items->map(function (Potensi $p) {
                    return [
                        'id_potensi' => $p->id_potensi,
                        'kategori' => $p->kategori,
                        'judul' => $p->judul,
                        'deskripsi' => $p->deskripsi,
                        'gambar_url' => $p->gambar_path ? asset('storage/' . $p->gambar_path) : null,
                        'poin' => $p->poin->map(fn($pt) => [
                            'id' => $pt->id_potensi_poin,
                            'isi' => $pt->isi,
                        ])->values(),
                    ];
                })->values();
            });

        // =====================================================
        // HITUNG STATISTIK PUBLIK
        // =====================================================
        $today = Carbon::today();
        $today6bulan = $today->clone()->addMonths(6);
        $today3bulan = $today->clone()->addMonths(3);

        // ✅ Total Kerjasama (semua yang finalized)
        $totalKerjasama = DB::table('kerjasama')
            ->where('is_finalized', 1)
            ->count();

        // ✅ Masa Berlaku < 6 Bulan
        $masaBerlakuKurang6bulan = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.is_finalized', 1)
            // Hanya ambil periode dengan tanggal_berakhir terbesar per kerjasama
            ->whereRaw('p.id_periode = (
                SELECT id_periode FROM periode_kerjasama p2
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
            ->where('k.is_finalized', 1)
            ->whereRaw('p.id_periode = (
                SELECT id_periode FROM periode_kerjasama p2
                WHERE p2.id_kerjasama = k.id_kerjasama
                ORDER BY p2.tanggal_berakhir DESC
                LIMIT 1
            )')
            // Tanggal berakhir: lebih dari hari ini tapi kurang dari 3 bulan
            ->whereBetween('p.tanggal_berakhir', [$today->toDateString(), $today3bulan->toDateString()])
            ->distinct('k.id_kerjasama')
            ->count();

        // ✅ Masa Berlaku Habis
        $masaBerlakuHabis = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.is_finalized', 1)
            ->whereRaw('p.id_periode = (
                SELECT id_periode FROM periode_kerjasama p2
                WHERE p2.id_kerjasama = k.id_kerjasama
                ORDER BY p2.tanggal_berakhir DESC
                LIMIT 1
            )')
            // Tanggal berakhir sudah lebih kecil dari hari ini
            ->where('p.tanggal_berakhir', '<', $today->toDateString())
            ->distinct('k.id_kerjasama')
            ->count();

        // ✅ Build stats array
        $stats = [
            ['label' => 'Jumlah Kerja Sama', 'value' => (string) $totalKerjasama],
            ['label' => 'Masa Berlaku <6 Bulan', 'value' => (string) $masaBerlakuKurang6bulan],
            ['label' => 'Masa Berlaku <3 Bulan', 'value' => (string) $masaBerlakuKurang3bulan],
            ['label' => 'Masa Berlaku Habis', 'value' => (string) $masaBerlakuHabis],
        ];

        return Inertia::render('Welcome', [
            'stats' => $stats,
            'potensiData' => $potensi,
        ]);
    }
}