<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKerjasamaPemerintahRequest;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RiwayatKerjasamaController extends Controller
{
    // =========================================================================
    // Riwayat Kerjasama — Mitra
    // =========================================================================

    /**
     * List all finalised mitra-type kerjasama.
     */
    public function mitra(Request $request)
    {
        $query = Kerjasama::finalized()
            ->mitraTipe()
            ->with(['mitra', 'latestPeriode', 'finalDokumen', 'kategori']);

        $this->applyFilters($query, $request);

        [$sortBy, $sortDir] = $this->resolveSort($request);

        $kerjasama = $query->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $kerjasama->getCollection()->transform(fn ($k) => $this->formatRow($k, tipe: 'mitra'));

        return Inertia::render('Admin/RiwayatKerjasama/Mitra', [
            'kerjasama' => $kerjasama,
            'filters' => $request->only(['search', 'tahun', 'jenis_kerjasama', 'jenis_dokumen', 'status', 'sort_by', 'sort_dir']),
        ]);
    }

    // =========================================================================
    // Riwayat Kerjasama — Pemerintah Boyolali
    // =========================================================================

    /**
     * List all pemerintah-type kerjasama.
     */
    public function pemerintah(Request $request)
    {
        $query = Kerjasama::pemerintahTipe()
            ->with(['admin', 'latestPeriode', 'finalDokumen', 'kategori']);

        $this->applyFilters($query, $request);

        [$sortBy, $sortDir] = $this->resolveSort($request);

        $kerjasama = $query->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $kerjasama->getCollection()->transform(fn ($k) => $this->formatRow($k, tipe: 'pemerintah'));

        return Inertia::render('Admin/RiwayatKerjasama/Pemerintah', [
            'kerjasama' => $kerjasama,
            'filters' => $request->only(['search', 'tahun', 'jenis_kerjasama', 'jenis_dokumen', 'status', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Store a new pemerintah-type kerjasama (manual archiving).
     */
    public function storePemerintah(StoreKerjasamaPemerintahRequest $request)
    {
        $validated = $request->validated();
        $admin = $request->user()->admin;
        $request->validate([
            'dokumen_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        $file = $request->file('dokumen_file');

        DB::transaction(function () use ($validated, $admin, $file, $request) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => null,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $validated['id_kategori'] ?? null,
                'judul' => $validated['judul'],
                'nomor_suratP' => $validated['nomor_surat'],
                'urusan' => $validated['urusan'],
                'daerah' => $validated['daerah'],
                'jenis_kerjasama' => $validated['jenis_kerjasama'],
                'jenis_dokumen' => $validated['jenis_dokumen'],
                'pemrakarsa' => 'P',
                'tipe' => 'pemerintah',
                'nama_pihak_luar' => $validated['nama_pihak_luar'],
                'status_aktif' => 'aktif',
                'is_finalized' => true,
            ]);

            PeriodeKerjasama::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_berakhir'],
                'keterangan' => $validated['keterangan'] ?? '',
            ]);

            $this->storeDokumenVersion($kerjasama, $file, (int) $request->user()->id_user);
        });

        return redirect()
            ->route('admin.riwayat-kerjasama.pemerintah')
            ->with('success', 'Data kerjasama pemerintah berhasil ditambahkan.');
    }

    /**
     * Update an existing pemerintah-type kerjasama.
     */
    public function updatePemerintah(int $id, StoreKerjasamaPemerintahRequest $request)
    {
        $kerjasama = Kerjasama::pemerintahTipe()->findOrFail($id);
        $validated = $request->validated();
        $request->validate([
            'dokumen_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        $file = $request->file('dokumen_file');

        DB::transaction(function () use ($kerjasama, $validated, $file, $request) {
            $kerjasama->update([
                'judul' => $validated['judul'],
                'nomor_suratP' => $validated['nomor_surat'],
                'urusan' => $validated['urusan'],
                'daerah' => $validated['daerah'],
                'jenis_kerjasama' => $validated['jenis_kerjasama'],
                'jenis_dokumen' => $validated['jenis_dokumen'],
                'nama_pihak_luar' => $validated['nama_pihak_luar'],
                'id_kategori' => $validated['id_kategori'] ?? $kerjasama->id_kategori,
            ]);

            // Replace the latest periode with the updated dates
            $kerjasama->periodes()->orderByDesc('tanggal_mulai')->first()?->update([
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_berakhir'],
                'keterangan' => $validated['keterangan'] ?? '',
            ]);

            if ($file !== null) {
                $this->storeDokumenVersion($kerjasama, $file, (int) $request->user()->id_user);
            }
        });

        return back()->with('success', 'Data kerjasama pemerintah berhasil diperbarui.');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function storeDokumenVersion(Kerjasama $kerjasama, UploadedFile $file, int $createdBy): void
    {
        $nextVersion = ((int) $kerjasama->dokumen()->max('versi_dokumen')) + 1;
        $path = $file->store('dokumen-kerjasama', 'public');

        Dokumen::create([
            'id_kerjasama' => $kerjasama->id_kerjasama,
            'nama_file' => $file->getClientOriginalName(),
            'lokasi_file' => $path,
            'versi_dokumen' => $nextVersion,
            'created_by' => $createdBy,
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
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

        if ($request->filled('tahun')) {
            $query->whereHas('latestPeriode', fn ($q) => $q->whereYear('tanggal_mulai', $request->tahun));
        }

        if ($request->filled('jenis_kerjasama')) {
            $query->where('jenis_kerjasama', $request->jenis_kerjasama);
        }

        if ($request->filled('jenis_dokumen')) {
            $query->where('jenis_dokumen', $request->jenis_dokumen);
        }

        if ($request->filled('status')) {
            $today = Carbon::today()->toDateString();
            $threshold = Carbon::today()->addDays(30)->toDateString();

            match ($request->status) {
                'aktif' => $query->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_mulai', '<=', $today)->where('tanggal_berakhir', '>=', $today)),
                'akan_berakhir' => $query->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '>', $today)->where('tanggal_berakhir', '<=', $threshold)),
                'berakhir' => $query->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '<', $today)),
                default => null,
            };
        }
    }

    private function formatRow(Kerjasama $k, string $tipe): array
    {
        $periode = $k->latestPeriode;

        $jangkaWaktu = null;
        if ($periode) {
            $mulai = Carbon::parse($periode->tanggal_mulai);
            $berakhir = Carbon::parse($periode->tanggal_berakhir);
            $jangkaWaktu = $mulai->diffInMonths($berakhir).' bulan';
        }

        $row = [
            'id_kerjasama' => $k->id_kerjasama,
            'tahun' => $periode ? Carbon::parse($periode->tanggal_mulai)->year : null,
            'judul' => $k->judul,
            'nomor_surat' => $k->nomor_surat,
            'jenis_kerjasama' => $k->jenis_kerjasama,
            'jenis_dokumen' => $k->jenis_dokumen,
            'urusan' => $k->urusan,
            'daerah' => $k->daerah,
            'tanggal_mulai' => $periode?->tanggal_mulai,
            'tanggal_berakhir' => $periode?->tanggal_berakhir,
            'jangka_waktu' => $jangkaWaktu,
            'status' => $k->status_label,
        ];

        if ($tipe === 'mitra') {
            $row['mitra'] = $k->mitra?->nama_perusahaan;
            $row['file'] = $k->finalDokumen ? [
                'id' => $k->finalDokumen->id_dokumen,
                'nama_file' => $k->finalDokumen->nama_file,
                'lokasi_file' => $k->finalDokumen->lokasi_file,
            ] : null;
        } else {
            $row['nama_pihak_luar'] = $k->nama_pihak_luar;
            $row['file'] = $k->finalDokumen ? [
                'id' => $k->finalDokumen->id_dokumen,
                'nama_file' => $k->finalDokumen->nama_file,
                'lokasi_file' => $k->finalDokumen->lokasi_file,
            ] : null;
        }

        return $row;
    }

    private function resolveSort(Request $request): array
    {
        $allowedSort = ['created_at', 'judul', 'jenis_kerjasama', 'jenis_dokumen'];

        $sortBy = (string) $request->input('sort_by', 'created_at');
        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        return [$sortBy, $sortDir];
    }
}
