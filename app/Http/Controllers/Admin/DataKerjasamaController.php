<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kerjasama;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataKerjasamaController extends Controller
{
    /**
     * Combined list of all kerjasama (both mitra and pemerintah, all statuses)
     * with unified filtering/search.
     */
    public function index(Request $request)
    {
        $query = Kerjasama::with(['mitra', 'admin', 'latestPeriode', 'kategori'])
            ->orderBy('created_at', 'desc');

        // -----------------------------------------------------------------------
        // Filters
        // -----------------------------------------------------------------------
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('urusan', 'like', "%{$search}%")
                  ->orWhere('nama_pihak_luar', 'like', "%{$search}%")
                  ->orWhereHas('mitra', fn($q) => $q->where('nama_perusahaan', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('tipe') && in_array($request->tipe, ['mitra', 'pemerintah'])) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('tahun')) {
            $query->whereHas('latestPeriode', fn($q) => $q->whereYear('tanggal_mulai', $request->tahun));
        }

        if ($request->filled('jenis_kerjasama')) {
            $query->where('jenis_kerjasama', $request->jenis_kerjasama);
        }

        if ($request->filled('jenis_dokumen')) {
            $query->where('jenis_dokumen', $request->jenis_dokumen);
        }

        if ($request->filled('is_finalized') && $request->is_finalized !== '') {
            $query->where('is_finalized', (bool) $request->is_finalized);
        }

        if ($request->filled('status')) {
            $today     = Carbon::today()->toDateString();
            $threshold = Carbon::today()->addDays(30)->toDateString();

            match ($request->status) {
                'aktif'         => $query->whereHas('latestPeriode', fn($q) => $q->where('tanggal_mulai', '<=', $today)->where('tanggal_berakhir', '>=', $today)),
                'akan_berakhir' => $query->whereHas('latestPeriode', fn($q) => $q->where('tanggal_berakhir', '>', $today)->where('tanggal_berakhir', '<=', $threshold)),
                'berakhir'      => $query->whereHas('latestPeriode', fn($q) => $q->where('tanggal_berakhir', '<', $today)),
                default         => null,
            };
        }

        $kerjasama = $query->paginate(15)->withQueryString();

        $kerjasama->getCollection()->transform(function (Kerjasama $k) {
            $periode     = $k->latestPeriode;
            $jangkaWaktu = null;

            if ($periode) {
                $mulai       = Carbon::parse($periode->tanggal_mulai);
                $berakhir    = Carbon::parse($periode->tanggal_berakhir);
                $jangkaWaktu = $mulai->diffInMonths($berakhir) . ' bulan';
            }

            return [
                'id_kerjasama'       => $k->id_kerjasama,
                'tahun'              => $periode ? Carbon::parse($periode->tanggal_mulai)->year : null,
                'tipe'               => $k->tipe,
                'mitra'              => $k->mitra?->nama_perusahaan,
                'nama_pihak_luar'    => $k->nama_pihak_luar,
                'pihak'              => $k->tipe === 'mitra' ? $k->mitra?->nama_perusahaan : $k->nama_pihak_luar,
                'judul'              => $k->judul,
                'nomor_surat'        => $k->nomor_surat,
                'jenis_kerjasama'    => $k->jenis_kerjasama,
                'jenis_dokumen'      => $k->jenis_dokumen,
                'urusan'             => $k->urusan,
                'daerah'             => $k->daerah,
                'tanggal_mulai'      => $periode?->tanggal_mulai,
                'tanggal_berakhir'   => $periode?->tanggal_berakhir,
                'jangka_waktu'       => $jangkaWaktu,
                'is_finalized'       => $k->is_finalized,
                'status_negosiasi'   => $k->status_negosiasi,
                'status_persetujuan' => $k->status_persetujuan?->value,
                'status_label'       => $k->status_label,
                'created_at'         => $k->created_at?->format('d/m/Y'),
            ];
        });

        return Inertia::render('Admin/DataKerjasama/Index', [
            'kerjasama' => $kerjasama,
            'filters'   => $request->only([
                'search',
                'tipe',
                'tahun',
                'jenis_kerjasama',
                'jenis_dokumen',
                'is_finalized',
                'status',
            ]),
        ]);
    }
}
