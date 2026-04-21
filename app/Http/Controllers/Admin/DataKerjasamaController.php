<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminKerjasamaRequest;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\PeriodeKerjasama;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    ->orWhere('nomor_suratM', 'like', "%{$search}%")
                    ->orWhere('nomor_suratP', 'like', "%{$search}%")
                    ->orWhere('urusan', 'like', "%{$search}%")
                    ->orWhere('nama_pihak_luar', 'like', "%{$search}%")
                    ->orWhereHas('mitra', fn ($q) => $q->where('nama_perusahaan', 'like', "%{$search}%"));
            });
        }

        $pemrakarsa = $request->input('pemrakarsa', $request->route('pemrakarsa'));
        if ($pemrakarsa && in_array($pemrakarsa, ['M', 'P'])) {
            $query->where('pemrakarsa', $pemrakarsa);
        }

        if ($request->filled('tahun')) {
            $query->whereHas('latestPeriode', fn ($q) => $q->whereYear('tanggal_mulai', $request->tahun));
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
            $today = Carbon::today()->toDateString();
            $threeMonths = Carbon::today()->addMonths(3)->toDateString();

            match ($request->status) {
                'aktif' => $query
                    ->where(function ($q) {
                        $q->where('pemrakarsa', 'P')
                            ->orWhere(function ($mitra) {
                                $mitra->where('pemrakarsa', 'M')->where('status_persetujuan', 'disetujui');
                            });
                    })
                    ->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '>', Carbon::today()->addMonths(3)->toDateString())),
                'berakhir' => $query
                    ->where(function ($q) {
                        $q->where('pemrakarsa', 'P')
                            ->orWhere(function ($mitra) {
                                $mitra->where('pemrakarsa', 'M')->where('status_persetujuan', 'disetujui');
                            });
                    })
                    ->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '<', $today)),
                'segera berakhir' => $query
                    ->where(function ($q) {
                        $q->where('pemrakarsa', 'P')
                            ->orWhere(function ($mitra) {
                                $mitra->where('pemrakarsa', 'M')->where('status_persetujuan', 'disetujui');
                            });
                    })
                    ->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '>=', $today)->where('tanggal_berakhir', '<=', $threeMonths)),
                'null' => $query->where('pemrakarsa', 'M')->whereNull('status_persetujuan'),
                default => null,
            };
        }

        $kerjasama = $query->paginate(15)->withQueryString();

        $kerjasama->getCollection()->transform(function (Kerjasama $k) {
            $periode = $k->latestPeriode;
            $jangkaWaktu = $this->formatJangkaWaktu($periode?->tanggal_mulai, $periode?->tanggal_berakhir);
            $statusKontrak = $this->computeStatusKontrak($k, $periode?->tanggal_berakhir);

            return [
                'id_kerjasama' => $k->id_kerjasama,
                'tahun' => $periode ? Carbon::parse($periode->tanggal_mulai)->year : null,
                'pemrakarsa' => $k->pemrakarsa,
                'mitra' => $k->mitra?->nama_perusahaan,
                'nama_pihak_luar' => $k->nama_pihak_luar,
                'pihak' => $k->pemrakarsa === 'M' ? $k->mitra?->nama_perusahaan : ($k->mitra?->nama_perusahaan ?? $k->nama_pihak_luar),
                'judul' => $k->judul,
                'nomor_surat' => $k->nomor_surat,
                'jenis_kerjasama' => $k->jenis_kerjasama,
                'jenis_dokumen' => $k->jenis_dokumen,
                'urusan' => $k->urusan,
                'daerah' => $k->daerah,
                'tanggal_mulai' => $periode?->tanggal_mulai,
                'tanggal_berakhir' => $periode?->tanggal_berakhir,
                'jangka_waktu' => $jangkaWaktu,
                'is_finalized' => $k->is_finalized,
                'status_negosiasi' => $k->status_negosiasi,
                'status_persetujuan' => $k->status_persetujuan?->value,
                'status_aktif' => $statusKontrak,
                'created_at' => $k->created_at?->format('d/m/Y'),
            ];
        });

        return Inertia::render('Admin/DataKerjasama', [
            'kerjasama' => $kerjasama,
            'mitras' => Mitra::orderBy('nama_perusahaan')
                ->get(['id_mitra', 'nama_perusahaan'])
                ->map(fn (Mitra $mitra) => [
                    'id_mitra' => $mitra->id_mitra,
                    'nama_perusahaan' => $mitra->nama_perusahaan,
                ]),
            'filters' => array_merge(
                $request->only([
                    'search',
                    'tahun',
                    'jenis_kerjasama',
                    'jenis_dokumen',
                    'is_finalized',
                    'status',
                ]),
                ['pemrakarsa' => $pemrakarsa]
            ),
        ]);
    }

    public function store(StoreAdminKerjasamaRequest $request)
    {
        $validated = $request->validated();
        $admin = $request->user()->admin;

        DB::transaction(function () use ($validated, $admin) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => $validated['id_mitra'],
                'id_admin' => $admin->id_admin,
                'id_kategori' => $validated['id_kategori'] ?? null,
                'judul' => $validated['judul'],
                'nomor_suratP' => $validated['nomor_suratP'] ?? null,
                'urusan' => $validated['urusan'] ?? '-',
                'daerah' => $validated['daerah'] ?? '-',
                'status_aktif' => 'aktif',
                'pemrakarsa' => 'P',
                'tipe' => 'pemerintah',
                'jenis_kerjasama' => $validated['jenis_kerjasama'] ?? null,
                'jenis_dokumen' => $validated['jenis_dokumen'] ?? null,
                'is_finalized' => true,
                'status_persetujuan' => 'disetujui',
            ]);

            PeriodeKerjasama::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_selesai'],
                'keterangan' => 'Admin input - '.$validated['jangka_waktu_bulan'].' bulan',
            ]);

            $file = $validated['dokumen_file'];
            $path = $file->store('dokumen-kerjasama', 'public');

            Dokumen::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'nama_file' => $file->getClientOriginalName(),
                'lokasi_file' => $path,
                'versi_dokumen' => 1,
                'created_by' => $admin->id_user,
            ]);
        });

        return redirect()
            ->route('admin.data-kerjasama.index')
            ->with('success', 'Data kerjasama berhasil ditambahkan.');
    }

    private function formatJangkaWaktu(?string $mulai, ?string $berakhir): ?string
    {
        if (! $mulai || ! $berakhir) {
            return null;
        }

        $start = Carbon::parse($mulai);
        $end = Carbon::parse($berakhir);
        $months = $start->diffInMonths($end);
        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) {
            return "{$years} tahun {$remainingMonths} bulan";
        }

        if ($years > 0) {
            return "{$years} tahun";
        }

        return "{$months} bulan";
    }

    private function computeStatusKontrak(Kerjasama $kerjasama, ?string $tanggalBerakhir): ?string
    {
        if (! $tanggalBerakhir) {
            return null;
        }

        if ($kerjasama->pemrakarsa === 'M' && $kerjasama->status_persetujuan?->value !== 'disetujui') {
            return null;
        }

        $today = Carbon::today();
        $end = Carbon::parse($tanggalBerakhir);

        if ($end->lt($today)) {
            return 'berakhir';
        }

        if ($today->diffInMonths($end, false) <= 3) {
            return 'segera berakhir';
        }

        return 'aktif';
    }
}
