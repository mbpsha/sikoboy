<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKerjasamaPemerintahRequest;
use App\Models\Adendum;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\PeriodeKerjasama;
use App\Models\RiwayatStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class RiwayatKerjasamaController extends Controller
{
    // =========================================================================
    // Riwayat Kerjasama — Combined (Mitra & Pemerintah)
    // =========================================================================

    /**
     * List all finalised kerjasama (both mitra and pemerintah types combined).
     */
    public function index(Request $request)
    {
        $query = Kerjasama::finalized()
            ->with(['mitra', 'latestPeriode', 'finalDokumen', 'kategori', 'adendum']);

        $this->applyFilters($query, $request);

        $kerjasama = $query->orderBy('id_kerjasama', 'asc')
            ->paginate(10)
            ->withQueryString();

        $offset = ($kerjasama->currentPage() - 1) * $kerjasama->perPage();
        $kerjasama->getCollection()->transform(fn ($k, $i) => $this->formatRow($k, $offset + $i));

        return Inertia::render('Admin/RiwayatKerjasama/Gabungan', [
            'data' => $kerjasama,
            'filters' => request()->only(['search', 'tahun']),
            'years' => DB::table('periode_kerjasama')
                ->selectRaw('YEAR(tanggal_mulai) as tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun'),
            'mitras' => $this->mitraOptions(),
        ]);
    }

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
                ->pluck('tahun'),
            'mitras' => $this->mitraOptions(),
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
                ->pluck('tahun'),
            'mitras' => $this->mitraOptions(),
        ]);
    }

    private function mitraOptions()
    {
        return Mitra::query()
            ->orderBy('nama_perusahaan')
            ->get()
            ->map(fn (Mitra $mitra) => [
                'id_mitra' => $mitra->id_mitra,
                'nama_perusahaan' => $mitra->nama_perusahaan,
                'npwp' => $mitra->getAttribute('npwp'),
                'pic' => $mitra->pic,
                'no_handphone' => $mitra->no_handphone,
                'alamat' => $mitra->alamat,
            ])
            ->values();
    }

    /**
     * Store a new pemerintah-type kerjasama (manual archiving).
     */
    public function storePemerintah(StoreKerjasamaPemerintahRequest $request)
    {
        $validated = $request->validated();
        $admin = $request->user()->admin;

        // Validate admin exists
        abort_if($admin === null, 403, 'User harus memiliki akses admin.');

        $idKategori = $validated['id_kategori']
            ?? DB::table('kategori_kerjasama')->orderBy('id_kategori')->value('id_kategori');

        if (! $idKategori) {
            throw ValidationException::withMessages([
                'id_kategori' => 'Kategori kerjasama belum tersedia. Silakan isi data kategori terlebih dahulu.',
            ]);
        }

        $file = $validated['dokumen_file'];
        $originalFileName = $file->getClientOriginalName();
        $path = $file->store('cooperation_docs', 'public');

        DB::transaction(function () use ($validated, $admin, $path, $idKategori, $originalFileName) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => null,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $idKategori,
                'judul' => $validated['judul'],
                'nomor_suratP' => $validated['nomor_suratP'] ?? null,
                'urusan' => $validated['urusan'] ?? '-',
                'daerah' => $validated['daerah'] ?? '-',
                'jenis_kerjasama' => $validated['jenis_kerjasama'] ?? null,
                'jenis_dokumen' => $validated['jenis_dokumen'] ?? null,
                'pembiayaan' => $validated['pembiayaan'] ?? 'APBN',
                'pemrakarsa' => 'P',
                'tipe' => 'pemerintah',
                'nama_pihak_luar' => $validated['nama_pihak_luar'] ?? null,
                'status_aktif' => 'aktif',
                'is_finalized' => true,
                'status_persetujuan' => 'disetujui',
            ]);

            PeriodeKerjasama::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_selesai'],
                'keterangan' => $path,
            ]);

            Dokumen::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'jenis_dokumen' => $kerjasama->jenis_dokumen,
                'nama_file' => $originalFileName,
                'lokasi_file' => $path,
                'versi_dokumen' => 1,
                'created_by' => $admin->id_user,
            ]);

            RiwayatStatus::recordStatus(
                idKerjasama: (int) $kerjasama->id_kerjasama,
                jenisStatus: 'disetujui',
                idAdmin: (int) $admin->id_admin,
                catatan: 'Kerjasama pemerintah ditambahkan ke riwayat',
            );
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
        $file = $request->file('dokumen_file');

        DB::transaction(function () use ($kerjasama, $validated, $file, $request) {
            $kerjasama->update([
                'judul' => $validated['judul'],
                'nomor_suratP' => $validated['nomor_suratP'] ?? $kerjasama->nomor_suratP,
                'urusan' => $validated['urusan'] ?? $kerjasama->urusan,
                'daerah' => $validated['daerah'] ?? $kerjasama->daerah,
                'jenis_kerjasama' => $validated['jenis_kerjasama'] ?? $kerjasama->jenis_kerjasama,
                'jenis_dokumen' => $validated['jenis_dokumen'] ?? $kerjasama->jenis_dokumen,
                'pembiayaan' => $validated['pembiayaan'] ?? $kerjasama->pembiayaan,
                'nama_pihak_luar' => $validated['nama_pihak_luar'] ?? $kerjasama->nama_pihak_luar,
                'id_kategori' => $validated['id_kategori'] ?? $kerjasama->id_kategori,
            ]);

            // Replace the latest periode with the updated dates
            $periode = $kerjasama->periodes()->orderByDesc('tanggal_mulai')->first();
            if ($periode) {
                $periode->update([
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_berakhir' => $validated['tanggal_selesai'],
                    'keterangan' => $periode->keterangan,
                ]);
            }

            if ($file !== null) {
                $this->storeDokumenVersion($kerjasama, $file, (int) $request->user()->id_user);
            }
        });

        return back()->with('success', 'Data kerjasama pemerintah berhasil diperbarui.');
    }

    /**
     * Store a new mitra-type kerjasama.
     */
    public function storeMitra(Request $request)
    {
        $validated = $request->validate([
            'id_mitra' => ['required', 'integer', 'exists:mitras,id_mitra'],
            'tahun' => ['required', 'numeric'],
            'judul' => ['required', 'string'],
            'jangka' => ['required', 'string'],
            'nomor_surat' => ['required', 'string'],
            'urusan' => ['required', 'string'],
            'daerah' => ['required', 'string'],
            'jenis_kerjasama' => ['required', 'string'],
            'jenis_dokumen' => ['required', 'string'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $admin = $request->user()->admin;

        // Validate admin exists
        abort_if($admin === null, 403, 'User harus memiliki akses admin.');

        $idKategori = DB::table('kategori_kerjasama')->orderBy('id_kategori')->value('id_kategori');

        if (! $idKategori) {
            throw ValidationException::withMessages([
                'id_kategori' => 'Kategori kerjasama belum tersedia. Silakan isi data kategori terlebih dahulu.',
            ]);
        }

        $mitra = Mitra::findOrFail((int) $validated['id_mitra']);

        $path = null;
        $originalFileName = null;

        if ($request->hasFile('file')) {
            $originalFileName = $request->file('file')->getClientOriginalName();
            $path = $request->file('file')->store('cooperation_docs', 'public');
        }

        DB::transaction(function () use ($validated, $admin, $mitra, $path, $idKategori, $originalFileName) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => $mitra->id_mitra,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $idKategori,
                'judul' => $validated['judul'],
                'nomor_suratM' => $validated['nomor_surat'],
                'urusan' => $validated['urusan'],
                'daerah' => $validated['daerah'],
                'jenis_kerjasama' => $validated['jenis_kerjasama'],
                'jenis_dokumen' => $validated['jenis_dokumen'],
                'pemrakarsa' => 'M',
                'tipe' => 'mitra',
                'nama_pihak_luar' => $mitra->nama_perusahaan,
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

            RiwayatStatus::recordStatus(
                idKerjasama: (int) $kerjasama->id_kerjasama,
                jenisStatus: 'disetujui',
                idAdmin: (int) $admin->id_admin,
                catatan: 'Kerjasama mitra ditambahkan ke riwayat',
            );
        });

        $lastPage = (int) ceil(max(1, Kerjasama::mitraTipe()->count()) / 10);

        return redirect()
            ->route('admin.riwayat-kerjasama.mitra', ['page' => $lastPage])
            ->with('success', 'Data kerjasama mitra berhasil ditambahkan.');
    }

    /**
     * Store a new kerjasama from Gabungan page (can be mitra or pemerintah type).
     */
    public function storeGabungan(Request $request)
    {
        $validated = $request->validate([
            'mitra' => ['required', 'string'],
            'tahun' => ['required', 'numeric'],
            'judul' => ['required', 'string'],
            'jangka' => ['required', 'string'],
            'nomor_surat' => ['required', 'string'],
            'urusan' => ['required', 'string'],
            'daerah' => ['required', 'string'],
            'jenis_kerjasama' => [
                'required',
                'string',
                'in:KSDD,KSDPK,NK/RK,PERTEK,KSDPL,KSDLL'
            ],
            'tipe_pengajuan' => ['required', 'in:mitra,pemerintah'],
            'jenis_dokumen' => ['required', 'string'],
            'nama_pihak_luar' => ['required', 'string'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $admin = $request->user()->admin;

        // Validate admin exists
        abort_if($admin === null, 403, 'User harus memiliki akses admin.');

        $idKategori = DB::table('kategori_kerjasama')->orderBy('id_kategori')->value('id_kategori');

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

        $isMitra = $validated['tipe_pengajuan'] === 'mitra';

        if ($isMitra) {
            // Find or create mitra
            $mitra = Mitra::where('nama_perusahaan', $validated['mitra'])->first();
            if (! $mitra) {
                $mitra = Mitra::create([
                    'nama_perusahaan' => $validated['mitra'],
                    'alamat' => '-',
                ]);
            }

            DB::transaction(function () use ($validated, $admin, $mitra, $path, $idKategori, $originalFileName) {
                $kerjasama = Kerjasama::create([
                    'id_mitra' => $mitra->id_mitra,
                    'id_admin' => $admin->id_admin,
                    'id_kategori' => $idKategori,
                    'judul' => $validated['judul'],
                    'nomor_suratM' => $validated['nomor_surat'],
                    'urusan' => $validated['urusan'],
                    'daerah' => $validated['daerah'],
                    'jenis_kerjasama' => $validated['jenis_kerjasama'],
                    'jenis_dokumen' => $validated['jenis_dokumen'],
                    'pemrakarsa' => 'M',
                    'tipe' => 'mitra',
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

                RiwayatStatus::recordStatus(
                    idKerjasama: (int) $kerjasama->id_kerjasama,
                    jenisStatus: 'disetujui',
                    idAdmin: (int) $admin->id_admin,
                    catatan: 'Kerjasama mitra ditambahkan ke riwayat',
                );
            });
        } else {
            // Pemerintah
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

                RiwayatStatus::recordStatus(
                    idKerjasama: (int) $kerjasama->id_kerjasama,
                    jenisStatus: 'disetujui',
                    idAdmin: (int) $admin->id_admin,
                    catatan: 'Kerjasama pemerintah ditambahkan ke riwayat',
                );
            });
        }

        $lastPage = (int) ceil(max(1, Kerjasama::finalized()->count()) / 10);

        return redirect()
            ->route('admin.riwayat-kerjasama.gabungan', ['page' => $lastPage])
            ->with('success', 'Data kerjasama berhasil ditambahkan.');
    }

    /**
     * Update an existing gabungan-type kerjasama (can be mitra or pemerintah type).
     */
    public function updateGabungan(int $id, Request $request)
    {
        $kerjasama = Kerjasama::finalized()->findOrFail($id);

        $validated = $request->validate([
            'mitra' => ['required', 'string'],
            'tahun' => ['required', 'numeric'],
            'judul' => ['required', 'string'],
            'jangka' => ['required', 'string'],
            'nomor_surat' => ['required', 'string'],
            'urusan' => ['required', 'string'],
            'daerah' => ['required', 'string'],
            'jenis_kerjasama' => ['required', 'string', 'in:Mitra,Pemerintah'],
            'jenis_dokumen' => ['required', 'string'],
            'nama_pihak_luar' => ['required', 'string'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $admin = $request->user()->admin;

        DB::transaction(function () use ($kerjasama, $validated, $admin, $request) {
            $path = null;
            $originalFileName = null;

            if ($request->hasFile('file')) {
                $originalFileName = $request->file('file')->getClientOriginalName();
                $path = $request->file('file')->store('cooperation_docs', 'public');
            }

            // Update kerjasama
            $isMitra = $validated['jenis_kerjasama'] === 'mitra';

            if ($isMitra) {
                // Find or create mitra
                $mitra = Mitra::where('nama_perusahaan', $validated['mitra'])->first();
                if (! $mitra) {
                    $mitra = Mitra::create([
                        'nama_perusahaan' => $validated['mitra'],
                        'alamat' => '-',
                    ]);
                }

                $kerjasama->update([
                    'id_mitra' => $mitra->id_mitra,
                    'id_admin' => null,
                    'judul' => $validated['judul'],
                    'nomor_suratM' => $validated['nomor_surat'],
                    'urusan' => $validated['urusan'],
                    'daerah' => $validated['daerah'],
                    'jenis_kerjasama' => $validated['jenis_kerjasama'],
                    'jenis_dokumen' => $validated['jenis_dokumen'],
                    'pemrakarsa' => 'M',
                    'tipe' => 'mitra',
                    'nama_pihak_luar' => $validated['nama_pihak_luar'],
                ]);
            } else {
                // Pemerintah
                $kerjasama->update([
                    'id_mitra' => null,
                    'id_admin' => $admin->id_admin,
                    'judul' => $validated['judul'],
                    'nomor_suratP' => $validated['nomor_surat'],
                    'urusan' => $validated['urusan'],
                    'daerah' => $validated['daerah'],
                    'jenis_kerjasama' => $validated['jenis_kerjasama'],
                    'jenis_dokumen' => $validated['jenis_dokumen'],
                    'pemrakarsa' => 'P',
                    'tipe' => 'pemerintah',
                    'nama_pihak_luar' => $validated['nama_pihak_luar'],
                ]);
            }

            // Update periode
            $periode = $kerjasama->latestPeriode;
            if ($periode) {
                $periode->update([
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_berakhir' => $validated['tanggal_berakhir'],
                    'keterangan' => $path ?? $periode->keterangan,
                ]);
            }

            // Update dokumen if file is provided
            if ($path && $originalFileName) {
                $dokumen = $kerjasama->finalDokumen;
                if ($dokumen) {
                    $dokumen->update([
                        'nama_file' => $originalFileName,
                        'lokasi_file' => $path,
                    ]);
                } else {
                    Dokumen::create([
                        'id_kerjasama' => $kerjasama->id_kerjasama,
                        'nama_file' => $originalFileName,
                        'lokasi_file' => $path,
                        'versi_dokumen' => 1,
                        'created_by' => $admin->id_user,
                    ]);
                }
            }
        });

        return back()->with('success', 'Data kerjasama berhasil diperbarui.');
    }

    /**
     * Store a new adendum for a kerjasama.
     */
    public function storeAdendum(Request $request)
    {
        $validated = $request->validate([
            'id_kerjasama' => ['required', 'integer', 'exists:kerjasama,id_kerjasama'],
            'judul_adendum' => ['required', 'string', 'max:255'],
            'keterangan_adendum' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $admin = $request->user()->admin;
        $path = null;
        $originalFileName = null;

        if ($request->hasFile('file')) {
            $originalFileName = $request->file('file')->getClientOriginalName();
            $path = $request->file('file')->store('adendum_docs', 'public');
        }

        \App\Models\Adendum::create([
            'id_kerjasama' => $validated['id_kerjasama'],
            'judul_adendum' => $validated['judul_adendum'],
            'keterangan_adendum' => $validated['keterangan_adendum'],
            'nama_file' => $originalFileName,
            'lokasi_file' => $path,
            'created_by' => $admin->id_user,
        ]);

        return back()->with('success', 'Adendum berhasil ditambahkan.');
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
        // SIMPLE SEARCH - Focus on main fields
        if ($request->filled('search')) {
            $search = trim($request->search);
            
            \Log::info("🔍 SEARCH FILTER", [
                'search' => $search,
                'filled' => $request->filled('search'),
            ]);

            $query->where(function ($q) use ($search) {
                // Main table fields
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_suratM', 'like', "%{$search}%")
                  ->orWhere('nomor_suratP', 'like', "%{$search}%")
                  ->orWhere('urusan', 'like', "%{$search}%")
                  ->orWhere('daerah', 'like', "%{$search}%")
                  ->orWhere('nama_pihak_luar', 'like', "%{$search}%")
                  ->orWhere('jenis_kerjasama', 'like', "%{$search}%")
                  ->orWhere('jenis_dokumen', 'like', "%{$search}%");
                
                // Mitra name search
                $q->orWhereHas('mitra', function ($mitra) use ($search) {
                    $mitra->where('nama_perusahaan', 'like', "%{$search}%");
                });
                
                // Year search
                if (is_numeric($search)) {
                    $q->orWhereHas('latestPeriode', function ($periode) use ($search) {
                        $periode->whereYear('tanggal_mulai', $search);
                    });
                }
            });
        }

        // TAHUN FILTER
        if ($request->filled('tahun')) {
            $query->whereHas('latestPeriode', function ($q) use ($request) {
                $q->whereYear('tanggal_mulai', $request->tahun);
            });
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
            } elseif ($end->greaterThan($today) && $today->diffInDays($end, false) <= 30) {
                $status = 'Segera Berakhir';
            }
        }

        $jangkaWaktu = '-';
        if ($mulai && $berakhir) {

            $start = Carbon::parse($mulai);
            $end = Carbon::parse($berakhir);

            $months = (int) $start->diffInMonths($end);

            if ($months >= 12) {

                // Dibulatkan ke bawah tanpa desimal
                $years = floor($months / 12);

                // Minimal 1 tahun
                $years = max($years, 1);

                $jangkaWaktu = $years . ' Tahun';

            } else {

                // Minimal 1 bulan
                $months = max($months, 1);

                $jangkaWaktu = $months . ' Bulan';
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

        $hasAdendum = $k->relationLoaded('adendum') && $k->adendum->count() > 0;

        return [
            'no' => $index + 1,
            'id_kerjasama' => $k->id_kerjasama,
            'tahun' => $tahun,
            'tipe' => $k->tipe,
            'pemrakarsa' => $k->pemrakarsa,
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
            'jenis_kerjasama' => $k->jenis_kerjasama,
            'has_adendum' => $hasAdendum,
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

        return asset('storage/'.ltrim($path, '/'));
    }
}
