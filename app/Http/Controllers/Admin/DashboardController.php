<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with archive metrics and chart data.
     */
    public function index()
    {
        $today     = Carbon::today()->toDateString();
        $threshold = Carbon::today()->addDays(30)->toDateString();

        // ---------------------------------------------------------------
        // Aggregate counters from the database in a single pass
        // Use the latest periode (by max tanggal_berakhir) for accurate status
        // ---------------------------------------------------------------
        $row = DB::table('kerjasama as k')
            ->leftJoin(
                DB::raw('(
                    SELECT p1.id_kerjasama,
                           p1.tanggal_mulai    AS tgl_mulai,
                           p1.tanggal_berakhir AS tgl_berakhir
                    FROM periode_kerjasama p1
                    INNER JOIN (
                        SELECT id_kerjasama, MAX(tanggal_berakhir) AS max_end
                        FROM periode_kerjasama
                        GROUP BY id_kerjasama
                    ) p2 ON p1.id_kerjasama = p2.id_kerjasama
                       AND p1.tanggal_berakhir = p2.max_end
                ) AS p'),
                'k.id_kerjasama', '=', 'p.id_kerjasama'
            )
            ->where('k.is_finalized', true)
            ->selectRaw("
                COUNT(*) AS total_kerjasama,
                SUM(CASE WHEN p.tgl_mulai <= ? AND p.tgl_berakhir >= ? THEN 1 ELSE 0 END) AS aktif,
                SUM(CASE WHEN p.tgl_berakhir > ? AND p.tgl_berakhir <= ?  THEN 1 ELSE 0 END) AS akan_berakhir,
                SUM(CASE WHEN p.tgl_berakhir < ?                          THEN 1 ELSE 0 END) AS berakhir
            ", [$today, $today, $today, $threshold, $today])
            ->first();

        $metrics = [
            'total_kerjasama' => (int) ($row->total_kerjasama ?? 0),
            'aktif'           => (int) ($row->aktif           ?? 0),
            'akan_berakhir'   => (int) ($row->akan_berakhir   ?? 0),
            'berakhir'        => (int) ($row->berakhir        ?? 0),
            'total_mitra'     => User::where('role', 'mitra')->count(),
            'total_dokumen'   => Dokumen::count(),
        ];

        // ---------------------------------------------------------------
        // Bar-chart: total finalized kerjasama per year (from periode)
        // ---------------------------------------------------------------
        $kerjasamaPerTahun = DB::table('kerjasama as k')
            ->join('periode_kerjasama as p', 'k.id_kerjasama', '=', 'p.id_kerjasama')
            ->where('k.is_finalized', true)
            ->selectRaw('YEAR(p.tanggal_mulai) AS tahun, COUNT(DISTINCT k.id_kerjasama) AS total')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get()
            ->map(fn($r) => ['tahun' => (int) $r->tahun, 'total' => (int) $r->total])
            ->values();

        // ---------------------------------------------------------------
        // Donut/Pie: kategori kerjasama percentage
        // ---------------------------------------------------------------
        $totalFinalized = $metrics['total_kerjasama'] ?: 1; // avoid division by zero

        $kategoriKerjasama = Kerjasama::query()
            ->finalized()
            ->leftJoin('kategori_kerjasama as kk', 'kk.id_kategori', '=', 'kerjasama.id_kategori')
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

        return Inertia::render('Admin/BerandaAdmin', [
            'metrics'          => $metrics,
            'kerjasama_per_tahun' => $kerjasamaPerTahun,
            'kategori_kerjasama' => $kategoriKerjasama,
        ]);
    }

    /**
     * Display list of partners (kept for backward compatibility).
     */
    public function partners(Request $request)
    {
        $query = User::where('role', 'mitra')->with('mitra');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($q) use ($search) {
                      $q->where('nama_perusahaan', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%");
                  });
            });
        }

        $partners = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/Partners/Index', [
            'partners' => $partners,
            'filters'  => $request->only(['search']),
        ]);
    }

    /**
     * Show partner detail (kept for backward compatibility).
     */
    public function showPartner($id)
    {
        $partner = User::where('role', 'mitra')
            ->with('mitra')
            ->findOrFail($id);

        return Inertia::render('Admin/Partners/Show', [
            'partner' => $partner,
        ]);
    }
}
