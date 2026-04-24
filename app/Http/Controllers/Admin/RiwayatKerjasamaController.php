<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKerjasamaPemerintahRequest;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        $kerjasama = $query->orderBy('id_kerjasama', 'asc')
            ->paginate(10)
            ->withQueryString();

        $offset = ($kerjasama->currentPage() - 1) * $kerjasama->perPage();
        $kerjasama->getCollection()->transform(fn ($k, $i) => $this->formatRow($k, $offset + $i));

        return Inertia::render('Admin/RiwayatKerjasama/Mitra', [
            'data' => $kerjasama,
            'filters' => request()->only(['search', 'tahun']),
            'years' => DB::table('periode_kerjasama')
                ->selectRaw('YEAR(tanggal_mulai) as tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun')
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

        $kerjasama = $query->orderBy('id_kerjasama', 'asc')
            ->paginate(10)
            ->withQueryString();

        $offset = ($kerjasama->currentPage() - 1) * $kerjasama->perPage();
        $kerjasama->getCollection()->transform(fn ($k, $i) => $this->formatRow($k, $offset + $i));

        return Inertia::render('Admin/RiwayatKerjasama/Pemerintah', [
            'data' => $kerjasama,
            'filters' => request()->only(['search', 'tahun']),
            'years' => DB::table('periode_kerjasama')
                ->selectRaw('YEAR(tanggal_mulai) as tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun')
        ]);
    }

    /**
     * Store a new pemerintah-type kerjasama (manual archiving).
     */
    public function storePemerintah(StoreKerjasamaPemerintahRequest $request)
    {
        $validated = $request->validated();
        $admin = $request->user()->admin;
        $idKategori = $validated['id_kategori']
            ?? DB::table('kategori_kerjasama')->orderBy('id_kategori')->value('id_kategori');

        if (! $idKategori) {
            throw ValidationException::withMessages([
                'id_kategori' => 'Kategori kerjasama belum tersedia. Silakan isi data kategori terlebih dahulu.',
            ]);
        }
        
        $path = null;
        $originalFileName = null;
        
        if ($request->hasFile('file')) {
            $originalFileName = $request->file('file')->getClientOriginalName();
            $path = $request->file('file')->store('cooperation_docs', 'public');
        }

        DB::transaction(function () use ($validated, $admin, $path, $idKategori, $originalFileName) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => null,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $idKategori,
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
                'status_persetujuan' => 'disetujui',
            ]);

            PeriodeKerjasama::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_berakhir'],
                'keterangan' => $path,
            ]);

            if ($path && $originalFileName) {
                Dokumen::create([
                    'id_kerjasama' => $kerjasama->id_kerjasama,
                    'nama_file' => $originalFileName,
                    'lokasi_file' => $path,
                    'versi_dokumen' => 1,
                    'created_by' => $admin->id_user,
                ]);
            }
        });

        $lastPage = (int) ceil(max(1, Kerjasama::pemerintahTipe()->count()) / 10);

        return redirect()
            ->route('admin.riwayat-kerjasama.pemerintah', ['page' => $lastPage])
            ->with('success', 'Data kerjasama pemerintah berhasil ditambahkan.');
    }

    /**
     * Update an existing pemerintah-type kerjasama.
     */
    public function updatePemerintah(int $id, StoreKerjasamaPemerintahRequest $request)
    {
        $kerjasama = Kerjasama::pemerintahTipe()->findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($kerjasama, $validated) {
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
        });

        return back()->with('success', 'Data kerjasama pemerintah berhasil diperbarui.');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

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
                'aktif' => $query->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_mulai', '<=', $today)->where('tanggal_berakhir', '>', $today)),
                'akan_berakhir' => $query->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '>', $today)->where('tanggal_berakhir', '<=', $threshold)),
                'berakhir' => $query->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '<=', $today)),
                default => null,
            };
        }
    }

    private function formatRow(Kerjasama $k, int $index = 0): array
    {
        $periode = $k->latestPeriode;

        $mulai = $periode?->tanggal_mulai;
        $berakhir = $periode?->tanggal_berakhir;

        $tahun = $mulai ? Carbon::parse($mulai)->year : null;

        // 🔥 STATUS OTOMATIS
        $status = 'Aktif';
        if ($berakhir) {
            $today = Carbon::today();
            $end = Carbon::parse($berakhir);

            if ($today->gte($end)) {
                $status = 'Berakhir';
            } elseif ($today->diffInDays($end) <= 30) {
                $status = 'Segera Berakhir';
            }
        }

        $jangkaWaktu = null;
        if ($mulai && $berakhir) {
            $years = Carbon::parse($mulai)->diffInYears($berakhir);
            if ($years > 0) {
                $formattedYears = rtrim(rtrim(number_format($years, 1, ',', ''), '0'), ',');
                $jangkaWaktu = $formattedYears . ' Tahun';
            } else {
                $jangkaWaktu = 'Kurang dari 1 Tahun';
            }
        }

        $namaMitra = null;
        if ($k->relationLoaded('mitra') && $k->mitra) {
            $namaMitra = $k->mitra->nama_perusahaan ?? null;
        }

        $storedFilePath = $k->finalDokumen?->lokasi_file;
        $storedFileName = $k->finalDokumen?->nama_file;

        if (! $storedFilePath && is_string($periode?->keterangan) && $periode->keterangan !== '') {
            $storedFilePath = $periode->keterangan;
            $storedFileName = basename($storedFilePath);
        }

        return [
            'no' => $index + 1,
            'tahun' => $tahun,
            'mitra' => $k->tipe === 'mitra'
                    ? ($namaMitra ?? $k->nama_pihak_luar ?? '-')
                    : ($k->nama_pihak_luar ?? '-'),
            'judul' => $k->judul,
            'mulai' => $mulai ? Carbon::parse($mulai)->translatedFormat('d F Y') : '-',
            'berakhir' => $berakhir ? Carbon::parse($berakhir)->translatedFormat('d F Y') : '-',
            'jangka_waktu' => $jangkaWaktu,
            'file_name' => $storedFileName,
            'file_url' => $this->resolveFileUrl($storedFilePath),
            'status' => $status,
        ];
    }

    private function resolveFileUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}