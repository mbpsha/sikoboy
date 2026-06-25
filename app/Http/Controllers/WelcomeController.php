<?php

namespace App\Http\Controllers;

use App\Models\Potensi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WelcomeController extends Controller
{
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
        // HITUNG STATISTIK PUBLIK (tanpa whereRaw subquery)
        // =====================================================
        $today = Carbon::today();
        $today6bulan = $today->clone()->addMonths(6);
        $today3bulan = $today->clone()->addMonths(3);

        // Ambil tanggal_berakhir TERBARU per id_kerjasama, hanya untuk kerjasama yang finalized
        $latestPeriode = DB::table('periode_kerjasama as p')
            ->select('p.id_kerjasama', DB::raw('MAX(p.tanggal_berakhir) as tanggal_berakhir_terbaru'))
            ->join('kerjasama as k', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.is_finalized', 1)
            ->groupBy('p.id_kerjasama')
            ->get();

        $totalKerjasama = DB::table('kerjasama')->where('is_finalized', 1)->count();

        $masaBerlakuKurang6bulan = $latestPeriode->filter(function ($row) use ($today, $today6bulan) {
            $tgl = Carbon::parse($row->tanggal_berakhir_terbaru);
            return $tgl->gte($today) && $tgl->lte($today6bulan);
        })->count();

        $masaBerlakuKurang3bulan = $latestPeriode->filter(function ($row) use ($today, $today3bulan) {
            $tgl = Carbon::parse($row->tanggal_berakhir_terbaru);
            return $tgl->gte($today) && $tgl->lte($today3bulan);
        })->count();

        $masaBerlakuHabis = $latestPeriode->filter(function ($row) use ($today) {
            $tgl = Carbon::parse($row->tanggal_berakhir_terbaru);
            return $tgl->lt($today);
        })->count();

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