<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UrusanEnum;
use App\Exports\RiwayatKerjasamaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKerjasamaPemerintahRequest;
use App\Models\Adendum;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\PeriodeKerjasama;
use App\Models\RiwayatStatus;
use App\Support\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            ->with(['mitra', 'latestPeriode', 'finalDokumen', 'dokumen', 'kategori', 'adendum']);

        $this->applyFilters($query, $request);

        $perPage = $request->input('per_page', 10);

        // Jika per_page besar (mode show all), gunakan get() untuk menampilkan semua
        if ($perPage >= 5000) {
            $collection = $query->orderBy('id_kerjasama', 'desc')->get();
            $items = [];
            foreach ($collection as $i => $k) {
                $items[] = $this->formatRow($k, $i);
            }
            $total = count($items);

            // Format sebagai pagination object dengan last_page = 1
            $kerjasama = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total, // total items
                $total, // per_page = total (sehingga last_page = 1)
                1, // current page
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                    'fragment' => null,
                ]
            );
        } else {
            $kerjasama = $query->orderBy('id_kerjasama', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            $offset = ($kerjasama->currentPage() - 1) * $kerjasama->perPage();
            $kerjasama->getCollection()->transform(fn($k, $i) => $this->formatRow($k, $offset + $i));
        }

        return Inertia::render('Admin/RiwayatKerjasama/Gabungan', [
            'data' => $kerjasama,
            'filters' => request()->only(['search', 'tahun']),
            'years' => DB::table('periode_kerjasama')
                ->selectRaw('YEAR(tanggal_mulai) as tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun'),
            'mitras' => $this->mitraOptions(),
            'jenisKerjasamaOptions' => $this->jenisKerjasamaOptions(),
            'jenisDokumenOptions' => $this->jenisDokumenOptions(),
            'urusanOptions' => $this->urusanOptions(),
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
            ->with(['mitra', 'latestPeriode', 'finalDokumen', 'dokumen', 'kategori', 'adendum']);

        $this->applyFilters($query, $request);

        $perPage = $request->input('per_page', 10);

        // Jika per_page besar (mode show all), gunakan get() untuk menampilkan semua
        if ($perPage >= 5000) {
            $collection = $query->orderBy('id_kerjasama', 'desc')->get();
            $items = [];
            foreach ($collection as $i => $k) {
                $items[] = $this->formatRow($k, $i);
            }
            $total = count($items);

            // Format sebagai pagination object dengan last_page = 1
            $kerjasama = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total, // total items
                $total, // per_page = total (sehingga last_page = 1)
                1, // current page
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                    'fragment' => null,
                ]
            );
        } else {
            $kerjasama = $query->orderBy('id_kerjasama', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            $offset = ($kerjasama->currentPage() - 1) * $kerjasama->perPage();
            $kerjasama->getCollection()->transform(fn($k, $i) => $this->formatRow($k, $offset + $i));
        }

        return Inertia::render('Admin/RiwayatKerjasama/Mitra', [
            'data' => $kerjasama,
            'filters' => request()->only(['search', 'tahun']),
            'years' => DB::table('periode_kerjasama')
                ->selectRaw('YEAR(tanggal_mulai) as tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun'),
            'mitras' => $this->mitraOptions(),
            'jenisKerjasamaOptions' => $this->jenisKerjasamaOptions(),
            'jenisDokumenOptions' => $this->jenisDokumenOptions(),
            'urusanOptions' => $this->urusanOptions(),
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
        Log::info("🔍 PEMERINTAH REQUEST", [
            'per_page' => $request->input('per_page'),
            'search' => $request->input('search'),
            'tahun' => $request->input('tahun'),
        ]);

        $query = Kerjasama::pemerintahTipe()
            ->with(['admin', 'latestPeriode', 'finalDokumen', 'dokumen', 'kategori', 'adendum']);

        $this->applyFilters($query, $request);

        $perPage = $request->input('per_page', 10);

        Log::info("📊 After applyFilters, perPage:", ['perPage' => $perPage]);

        // Jika per_page besar (mode show all), gunakan get() untuk menampilkan semua
        if ($perPage >= 5000) {
            $collection = $query->orderBy('id_kerjasama', 'desc')->get();
            $items = [];
            foreach ($collection as $i => $k) {
                $items[] = $this->formatRow($k, $i);
            }
            $total = count($items);

            // Format sebagai pagination object dengan last_page = 1
            $kerjasama = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total, // total items
                $total, // per_page = total (sehingga last_page = 1)
                1, // current page
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                    'fragment' => null,
                ]
            );
        } else {
            $kerjasama = $query->orderBy('id_kerjasama', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            $offset = ($kerjasama->currentPage() - 1) * $kerjasama->perPage();
            $kerjasama->getCollection()->transform(fn($k, $i) => $this->formatRow($k, $offset + $i));
        }

        return Inertia::render('Admin/RiwayatKerjasama/Pemerintah', [
            'data' => $kerjasama,
            'filters' => request()->only(['search', 'tahun']),
            'years' => DB::table('periode_kerjasama')
                ->selectRaw('YEAR(tanggal_mulai) as tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun'),
            'mitras' => $this->mitraOptions(),
            'jenisKerjasamaOptions' => $this->jenisKerjasamaOptions(),
            'jenisDokumenOptions' => $this->jenisDokumenOptions(),
            'urusanOptions' => $this->urusanOptions(),
        ]);
    }

    public function exportGabungan(Request $request): StreamedResponse|BinaryFileResponse
    {
        return $this->exportByType($request, 'gabungan');
    }

    public function exportMitra(Request $request): StreamedResponse|BinaryFileResponse
    {
        return $this->exportByType($request, 'mitra');
    }

    public function exportPemerintah(Request $request): StreamedResponse|BinaryFileResponse
    {
        return $this->exportByType($request, 'pemerintah');
    }

    private function jenisKerjasamaOptions(): array
    {
        return [
            ['value' => 'KSDD', 'label' => 'Kerjasama Daerah Antar Daerah (KSDD)'],
            ['value' => 'KSDPK', 'label' => 'Kerjasama Dengan Pihak Ketiga (KSDPK)'],
            ['value' => 'NK/RK', 'label' => 'Sinergi Dengan Pemerintah Pusat/Lembaga (NK/RK)'],
            ['value' => 'PERTEK', 'label' => 'Perjanjian Teknis (PERTEK)'],
            ['value' => 'KSDPL', 'label' => 'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)'],
            ['value' => 'KSDLL', 'label' => 'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)'],
        ];
    }

    private function urusanOptions(): array
    {
        return UrusanEnum::cases();
    }

    private function jenisDokumenOptions(): array
    {
        return [
            'KSB',
            'Nota Kesepakatan',
            'Perjanjian Teknis',
            'PKS',
            'Rencana Kerja',
            'MOU',
            'RKT',
            'LOI',
        ];
    }

    private function mitraOptions()
    {
        return Mitra::query()
            ->orderBy('nama_perusahaan')
            ->get()
            ->map(fn(Mitra $mitra) => [
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
        $uploaded = FileUpload::storeAsOriginal($file, 'cooperation_docs', 'public');
        $originalFileName = $uploaded['nama_file'];
        $path = $uploaded['lokasi_file'];

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
                'status_aktif' => 'Aktif',
                'is_finalized' => true,
                'status_persetujuan' => 'disetujui',
            ]);

            PeriodeKerjasama::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_selesai'],
                'keterangan' => $path,
            ]);

            $this->createDokumen([
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

        return redirect()
            ->route('admin.riwayat-kerjasama.pemerintah', ['page' => 1])
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
        $currentPage = $request->input('page', 1);

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

        return redirect()
            ->route('admin.riwayat-kerjasama.pemerintah')
            ->with(['success' => 'Data kerjasama pemerintah berhasil diperbarui.', 'page' => $currentPage])
            ->withQueryString();
    }

    /**
     * Store a new mitra-type kerjasama.
     */
    public function storeMitra(Request $request)
    {
        $validated = $request->validate([
            'id_mitra' => ['nullable', 'integer', 'exists:mitras,id_mitra'],
            'mitra' => ['required', 'string', 'max:255'],
            'tahun' => ['required', 'numeric'],
            'judul' => ['required', 'string'],
            'jangka' => ['required', 'string'],
            'nomor_suratM' => ['required', 'string'],
            'nomor_suratP' => ['required', 'string'],
            'pembiayaan' => ['required', 'in:APBN,APBD,PIHAK KETIGA,PARA PIHAK,SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN'],
            'urusan' => ['required', 'string'],
            'daerah' => ['required', 'string'],
            'jenis_kerjasama' => ['required', 'string', Rule::in(array_column($this->jenisKerjasamaOptions(), 'value'))],
            'jenis_dokumen' => ['required', 'string', Rule::in($this->jenisDokumenOptions())],
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

        $mitra = ! empty($validated['id_mitra'])
            ? Mitra::findOrFail((int) $validated['id_mitra'])
            : Mitra::firstOrCreate(
                ['nama_perusahaan' => trim((string) $validated['mitra'])],
                ['alamat' => '-']
            );

        $path = null;
        $originalFileName = null;

        if ($request->hasFile('file')) {
            $uploaded = FileUpload::storeAsOriginal($request->file('file'), 'cooperation_docs', 'public');
            $originalFileName = $uploaded['nama_file'];
            $path = $uploaded['lokasi_file'];
        }

        DB::transaction(function () use ($validated, $admin, $mitra, $path, $idKategori, $originalFileName) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => $mitra->id_mitra,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $idKategori,
                'judul' => $validated['judul'],
                'nomor_suratM' => $validated['nomor_suratM'],
                'nomor_suratP' => $validated['nomor_suratP'],
                'pembiayaan' => $validated['pembiayaan'],
                'urusan' => $validated['urusan'],
                'daerah' => $validated['daerah'],
                'jenis_kerjasama' => $validated['jenis_kerjasama'],
                'jenis_dokumen' => $validated['jenis_dokumen'],
                'pemrakarsa' => 'M',
                'tipe' => 'mitra',
                'nama_pihak_luar' => $validated['mitra'],
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

            $dok = null;
            if ($path && $originalFileName) {
                $dok = $this->createDokumen([
                    'id_kerjasama' => $kerjasama->id_kerjasama,
                    'jenis_dokumen' => $validated['jenis_dokumen'],
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
                file: $dok ? $dok->lokasi_file : null,
            );
        });

        return redirect()
            ->route('admin.riwayat-kerjasama.mitra', ['page' => 1])
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
            'nomor_suratM' => ['required', 'string'],
            'nomor_suratP' => ['required', 'string'],
            'pembiayaan' => ['required', 'in:APBN,APBD,PIHAK KETIGA,PARA PIHAK,SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN'],
            'urusan' => ['required', 'string'],
            'daerah' => ['required', 'string'],
            'jenis_kerjasama' => [
                'required',
                'string',
                'in:KSDD,KSDPK,NK/RK,PERTEK,KSDPL,KSDLL'
            ],
            'tipe_pengajuan' => ['required', 'in:mitra,pemerintah'],
            'jenis_dokumen' => ['required', 'string', Rule::in($this->jenisDokumenOptions())],
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
            $uploaded = FileUpload::storeAsOriginal($request->file('file'), 'cooperation_docs', 'public');
            $originalFileName = $uploaded['nama_file'];
            $path = $uploaded['lokasi_file'];
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
                    'nomor_suratM' => $validated['nomor_suratM'],
                    'nomor_suratP' => $validated['nomor_suratP'],
                    'pembiayaan' => $validated['pembiayaan'],
                    'urusan' => $validated['urusan'],
                    'daerah' => $validated['daerah'],
                    'jenis_kerjasama' => $validated['jenis_kerjasama'],
                    'jenis_dokumen' => $validated['jenis_dokumen'],
                    'pemrakarsa' => 'M',
                    'tipe' => 'mitra',
                    'nama_pihak_luar' => $validated['nama_pihak_luar'],
                    'status_aktif' => 'Aktif',
                    'is_finalized' => true,
                    'status_persetujuan' => 'disetujui',
                ]);

                PeriodeKerjasama::create([
                    'id_kerjasama' => $kerjasama->id_kerjasama,
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_berakhir' => $validated['tanggal_berakhir'],
                    'keterangan' => $path,
                ]);

                $dok = null;
                if ($path && $originalFileName) {
                    $dok = $this->createDokumen([
                        'id_kerjasama' => $kerjasama->id_kerjasama,
                        'jenis_dokumen' => $validated['jenis_dokumen'],
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
                    file: $dok ? $dok->lokasi_file : null,
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
                    'nomor_suratM' => $validated['nomor_suratM'],
                    'nomor_suratP' => $validated['nomor_suratP'],
                    'pembiayaan' => $validated['pembiayaan'],
                    'urusan' => $validated['urusan'],
                    'daerah' => $validated['daerah'],
                    'jenis_kerjasama' => $validated['jenis_kerjasama'],
                    'jenis_dokumen' => $validated['jenis_dokumen'],
                    'pemrakarsa' => 'P',
                    'tipe' => 'pemerintah',
                    'nama_pihak_luar' => $validated['nama_pihak_luar'],
                    'status_aktif' => 'Aktif',
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
                    $this->createDokumen([
                        'id_kerjasama' => $kerjasama->id_kerjasama,
                        'jenis_dokumen' => $validated['jenis_dokumen'],
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

        return redirect()
            ->route('admin.riwayat-kerjasama.gabungan', ['page' => 1])
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
                $uploaded = FileUpload::storeAsOriginal($request->file('file'), 'cooperation_docs', 'public');
                $originalFileName = $uploaded['nama_file'];
                $path = $uploaded['lokasi_file'];
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
                    // Create a riwayat entry so mitra can see admin-uploaded file in timeline
                    RiwayatStatus::recordStatus(
                        idKerjasama: (int) $kerjasama->id_kerjasama,
                        jenisStatus: 'revisi',
                        idAdmin: (int) $admin->id_admin,
                        catatan: 'Admin mengganti dokumen kerjasama',
                        file: $path,
                    );
                } else {
                    $this->createDokumen([
                        'id_kerjasama' => $kerjasama->id_kerjasama,
                        'nama_file' => $originalFileName,
                        'lokasi_file' => $path,
                        'versi_dokumen' => 1,
                        'created_by' => $admin->id_user,
                    ]);
                    RiwayatStatus::recordStatus(
                        idKerjasama: (int) $kerjasama->id_kerjasama,
                        jenisStatus: 'revisi',
                        idAdmin: (int) $admin->id_admin,
                        catatan: 'Admin menambahkan dokumen kerjasama',
                        file: $path,
                    );
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
            'mitra' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'digits:4'],
            'judul_adendum' => ['required', 'string', 'max:255'],
            'keterangan_adendum' => ['nullable', 'string'],
            'nomor_surat_mitra_baru' => ['nullable', 'string', 'max:255'],
            'nomor_surat_pemerintah_baru' => ['nullable', 'string', 'max:255'],
            'nomor_surat_mitra_lama' => ['nullable', 'string', 'max:255'],
            'nomor_surat_pemerintah_lama' => ['nullable', 'string', 'max:255'],
            'urusan' => ['nullable', 'string', 'max:255'],
            'jangka_waktu' => ['nullable', 'string', 'max:255'],
            'jenis_kerjasama' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'pembiayaan' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $admin = $request->user()->admin;
        $kerjasama = Kerjasama::with(['mitra', 'latestPeriode'])->findOrFail((int) $validated['id_kerjasama']);
        $periode = $kerjasama->latestPeriode;
        $path = null;
        $originalFileName = null;

        if ($request->hasFile('file')) {
            $uploaded = FileUpload::storeAsOriginal($request->file('file'), 'adendum_docs', 'public');
            $originalFileName = $uploaded['nama_file'];
            $path = $uploaded['lokasi_file'];
        }

        \App\Models\Adendum::create([
            'id_kerjasama' => $validated['id_kerjasama'],
            'mitra' => $validated['mitra']
                ?? ($kerjasama->mitra->nama_perusahaan ?? $kerjasama->nama_pihak_luar ?? null),
            'tahun' => $validated['tahun']
                ?? ($periode?->tanggal_mulai ? Carbon::parse($periode->tanggal_mulai)->format('Y') : null),
            'judul_adendum' => $validated['judul_adendum'],
            'keterangan_adendum' => $validated['keterangan_adendum'] ?? null,
            'nomor_surat_mitra_baru' => $validated['nomor_surat_mitra_baru'] ?? null,
            'nomor_surat_pemerintah_baru' => $validated['nomor_surat_pemerintah_baru'] ?? null,
            'nomor_surat_mitra_lama' => $validated['nomor_surat_mitra_lama'] ?? $kerjasama->nomor_suratM,
            'nomor_surat_pemerintah_lama' => $validated['nomor_surat_pemerintah_lama'] ?? $kerjasama->nomor_suratP,
            'urusan' => $validated['urusan'] ?? $kerjasama->urusan,
            'jangka_waktu' => $validated['jangka_waktu'] ?? null,
            'jenis_kerjasama' => $validated['jenis_kerjasama'] ?? $kerjasama->jenis_kerjasama,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? $periode?->tanggal_mulai,
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? $periode?->tanggal_berakhir,
            'pembiayaan' => $validated['pembiayaan'] ?? $kerjasama->pembiayaan,
            'nama_file' => $originalFileName,
            'lokasi_file' => $path,
            'created_by' => $admin->id_user,
        ]);

        return back()->with('success', 'Adendum berhasil ditambahkan.');
    }

    /**
     * Update status kerjasama.
     */
    public function updateStatus(int $id, Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Segera Berakhir,Berakhir,Dibatalkan'],
        ]);

        $kerjasama = Kerjasama::findOrFail($id);
        $admin = $request->user()->admin;

        abort_if($admin === null, 403, 'User harus memiliki akses admin.');

        // Find id_status from status table
        $statusRecord = \App\Models\Status::where('jenis_status', $validated['status'])->firstOrFail();

        DB::transaction(function () use ($kerjasama, $validated, $admin, $statusRecord) {
            // Update status di kerjasama - simpan string status, bukan boolean!
            $kerjasama->update([
                'status_aktif' => $validated['status'],
            ]);

            // Catat perubahan status di riwayat_status
            RiwayatStatus::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'id_status' => $statusRecord->id_status,
                'id_admin' => $admin->id_admin,
                'catatan' => '',
                'tanggal' => now(),
            ]);
        });

        return back();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function storeDokumenVersion(Kerjasama $kerjasama, UploadedFile $file, int $createdBy): void
    {
        $nextVersion = ((int) $kerjasama->dokumen()->max('versi_dokumen')) + 1;
        $uploaded = FileUpload::storeAsOriginal($file, 'dokumen-kerjasama', 'public');
        $jenisDokumen = $kerjasama->jenis_dokumen ?: ($kerjasama->finalDokumen?->jenis_dokumen ?: 'KSB');

        $this->createDokumen([
            'id_kerjasama' => $kerjasama->id_kerjasama,
            'jenis_dokumen' => $jenisDokumen,
            'nama_file' => $uploaded['nama_file'],
            'lokasi_file' => $uploaded['lokasi_file'],
            'versi_dokumen' => $nextVersion,
            'created_by' => $createdBy,
        ]);
        // Also create a riwayat entry so timelines show the uploaded file.
        $adminId = \App\Models\Admin::where('id_user', $createdBy)->value('id_admin');
        RiwayatStatus::recordStatus(
            idKerjasama: (int) $kerjasama->id_kerjasama,
            jenisStatus: 'revisi',
            idAdmin: $adminId ? (int) $adminId : null,
            catatan: 'Dokumen versi baru diunggah oleh admin',
            file: $uploaded['lokasi_file'],
        );
    }

    private function createDokumen(array $attributes): Dokumen
    {
        $payload = [
            'id_kerjasama' => $attributes['id_kerjasama'],
            'nama_file' => $attributes['nama_file'],
            'lokasi_file' => $attributes['lokasi_file'],
            'versi_dokumen' => $attributes['versi_dokumen'] ?? 1,
            'created_by' => $attributes['created_by'],
        ];

        if (Schema::hasColumn('dokumen', 'jenis_dokumen') && array_key_exists('jenis_dokumen', $attributes)) {
            $payload['jenis_dokumen'] = $attributes['jenis_dokumen'];
        }

        if (Schema::hasColumn('dokumen', 'tipe_dokumen') && array_key_exists('tipe_dokumen', $attributes)) {
            $payload['tipe_dokumen'] = $attributes['tipe_dokumen'];
        }

        return Dokumen::create($payload);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('nomor_suratM', 'like', "%{$search}%")
                    ->orWhere('nomor_suratP', 'like', "%{$search}%")
                    ->orWhere('urusan', 'like', "%{$search}%")
                    ->orWhere('daerah', 'like', "%{$search}%")
                    ->orWhere('nama_pihak_luar', 'like', "%{$search}%")
                    ->orWhere('jenis_kerjasama', 'like', "%{$search}%")
                    ->orWhere('jenis_dokumen', 'like', "%{$search}%");

                $q->orWhereHas('mitra', function ($mitra) use ($search) {
                    $mitra->where('nama_perusahaan', 'like', "%{$search}%");
                });

                if (is_numeric($search)) {
                    $q->orWhereHas('latestPeriode', function ($periode) use ($search) {
                        $periode->whereYear('tanggal_mulai', $search);
                    });
                }
            });
        }

        if ($request->filled('tahun')) {
            $query->whereHas('latestPeriode', function ($q) use ($request) {
                $q->whereYear('tanggal_mulai', $request->tahun);
            });
        }
    }

    private function exportByType(Request $request, string $type): StreamedResponse|BinaryFileResponse
    {
        $query = match ($type) {
            'mitra' => Kerjasama::finalized()
                ->mitraTipe()
                ->with(['mitra', 'latestPeriode', 'finalDokumen', 'dokumen', 'kategori', 'adendum']),
            'pemerintah' => Kerjasama::pemerintahTipe()
                ->with(['admin', 'latestPeriode', 'finalDokumen', 'dokumen', 'kategori', 'adendum']),
            default => Kerjasama::finalized()
                ->with(['mitra', 'latestPeriode', 'finalDokumen', 'dokumen', 'kategori', 'adendum']),
        };

        $this->applyFilters($query, $request);
        $this->applyExportColumnFilters($query, $request);

        $rows = $query->orderBy('id_kerjasama', 'desc')
            ->get()
            ->values()
            ->map(fn(Kerjasama $k, int $i) => $this->formatRow($k, $i));

        $format = strtolower((string) $request->query('format', 'csv'));
        $baseFilename = 'riwayat-kerjasama-' . $type . '-' . now()->format('Ymd_His');

        if ($format === 'xlsx') {
            return Excel::download(new RiwayatKerjasamaExport($rows), $baseFilename . '.xlsx');
        }

        $filename = $baseFilename . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Tahun',
                'Tipe',
                'Pemrakarsa',
                'Mitra/Pihak',
                'Judul',
                'Tanggal Mulai',
                'Tanggal Berakhir',
                'Jangka Waktu',
                'Status',
                'Jenis Kerjasama',
                'Jenis Dokumen',
                'Nomor Surat Mitra',
                'Nomor Surat Pemerintah',
                'Urusan',
                'Pembiayaan',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['no'] ?? '',
                    $row['tahun'] ?? '',
                    $row['tipe'] ?? '',
                    $row['pemrakarsa'] ?? '',
                    $row['mitra'] ?? '',
                    $row['judul'] ?? '',
                    $row['tanggal_mulai'] ?? '',
                    $row['tanggal_berakhir'] ?? '',
                    $row['jangka_waktu'] ?? '',
                    $row['status'] ?? '',
                    $row['jenis_kerjasama'] ?? '',
                    $row['jenis_dokumen'] ?? '',
                    $row['nomor_suratM'] ?? '',
                    $row['nomor_suratP'] ?? '',
                    $row['urusan'] ?? '',
                    $row['pembiayaan'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function applyExportColumnFilters($query, Request $request): void
    {
        $tahunColumns = collect($request->input('tahun_column', []))
            ->filter(fn($value) => is_numeric($value))
            ->map(fn($value) => (int) $value)
            ->unique()
            ->values();

        if ($tahunColumns->isNotEmpty()) {
            $query->whereHas('latestPeriode', function ($q) use ($tahunColumns) {
                $q->whereIn(DB::raw('YEAR(tanggal_mulai)'), $tahunColumns->all());
            });
        }

        $tipe = collect($request->input('tipe', []))
            ->map(fn($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($tipe->isNotEmpty()) {
            $query->whereIn('tipe', $tipe->all());
        }

        $mitra = collect($request->input('mitra', []))
            ->map(fn($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($mitra->isNotEmpty()) {
            $query->where(function ($q) use ($mitra) {
                $q->whereHas('mitra', function ($mitraQuery) use ($mitra) {
                    $mitraQuery->whereIn('nama_perusahaan', $mitra->all());
                })->orWhereIn('nama_pihak_luar', $mitra->all());
            });
        }

        $jenisKerjasama = collect($request->input('jenis_kerjasama', []))
            ->map(fn($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($jenisKerjasama->isNotEmpty()) {
            $query->whereIn('jenis_kerjasama', $jenisKerjasama->all());
        }

        $status = collect($request->input('status', []))
            ->map(fn($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($status->isNotEmpty()) {
            $query->whereIn('status_aktif', $status->all());
        }
    }

    private function formatRow(Kerjasama $k, int $index = 0): array
    {
        $periode = $k->latestPeriode;

        $mulai = $periode?->tanggal_mulai;
        $berakhir = $periode?->tanggal_berakhir;

        $tahun = $mulai ? Carbon::parse($mulai)->year : null;

        // 🔥 AMBIL STATUS DARI DATABASE (bukan otomatis dari tanggal)
        $status = $k->status_aktif ?? 'Aktif';

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

        // For mitra-originated kerjasama: show the last document uploaded by mitra
        // (tipe_dokumen = 'mitra') as the reference file in riwayat, because that
        // is the agreed-upon final version submitted by the partner.
        // For admin-originated kerjasama: use the highest-version dokumen directly.
        if ($k->pemrakarsa === 'M') {
            $mitraDok = collect($k->relationLoaded('dokumen') ? $k->dokumen : [])
                ->where('tipe_dokumen', 'mitra')
                ->sortByDesc('versi_dokumen')
                ->first();
            if ($mitraDok) {
                $storedFilePath = $mitraDok->lokasi_file;
                $storedFileName = $mitraDok->nama_file;
            }
        }

        $dokumenVersions = collect($k->relationLoaded('dokumen') ? $k->dokumen : [])
            ->sortBy(fn($dokumen) => (int) $dokumen->versi_dokumen)
            ->values()
            ->map(function ($dokumen) {
                return [
                    'id_dokumen' => $dokumen->id_dokumen,
                    'versi_dokumen' => (int) $dokumen->versi_dokumen,
                    'nama_file' => $dokumen->nama_file,
                    'file_url' => $this->resolveFileUrl($dokumen->lokasi_file),
                    'lokasi_file' => $dokumen->lokasi_file,
                    'created_at' => $dokumen->created_at
                        ? Carbon::parse($dokumen->created_at)->translatedFormat('d F Y H:i')
                        : null,
                    'tipe_dokumen' => $dokumen->tipe_dokumen,
                ];
            })
            ->all();

        if (! $storedFilePath && is_string($periode?->keterangan) && $periode->keterangan !== '') {
            $storedFilePath = $periode->keterangan;
            $storedFileName = basename($storedFilePath);
        }

        $adendumItems = collect($k->relationLoaded('adendum') ? $k->adendum : [])
            ->sortBy('id_adendum')
            ->values()
            ->map(function ($adendum, int $index) {
                return [
                    'id_adendum' => $adendum->id_adendum,
                    'urutan' => $index + 1,
                    'judul_adendum' => $adendum->judul_adendum,
                    'keterangan_adendum' => $adendum->keterangan_adendum,
                    'mitra' => $adendum->mitra,
                    'tahun' => $adendum->tahun,
                    'nomor_surat_mitra_baru' => $adendum->nomor_surat_mitra_baru,
                    'nomor_surat_pemerintah_baru' => $adendum->nomor_surat_pemerintah_baru,
                    'nomor_surat_mitra_lama' => $adendum->nomor_surat_mitra_lama,
                    'nomor_surat_pemerintah_lama' => $adendum->nomor_surat_pemerintah_lama,
                    'urusan' => $adendum->urusan,
                    'jangka_waktu' => $adendum->jangka_waktu,
                    'jenis_kerjasama' => $adendum->jenis_kerjasama,
                    'tanggal_mulai' => $adendum->tanggal_mulai,
                    'tanggal_berakhir' => $adendum->tanggal_berakhir,
                    'pembiayaan' => $adendum->pembiayaan,
                    'file_name' => $adendum->nama_file,
                    'file_url' => $this->resolveFileUrl($adendum->lokasi_file),
                    'created_at' => $adendum->created_at
                        ? Carbon::parse($adendum->created_at)->translatedFormat('d F Y')
                        : null,
                ];
            });
        $hasAdendum = $adendumItems->isNotEmpty();

        // 🔥 HITUNG SISA HARI
        $daysRemaining = null;
        if ($berakhir) {
            $today = Carbon::today();
            $end = Carbon::parse($berakhir);
            $daysRemaining = $today->diffInDays($end, false);
        }

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
            'tanggal_mulai' => $mulai ? Carbon::parse($mulai)->format('Y-m-d') : null,
            'tanggal_berakhir' => $berakhir ? Carbon::parse($berakhir)->format('Y-m-d') : null,
            'jangka_waktu' => $jangkaWaktu,
            'file_name' => $storedFileName,
            'file_url' => $this->resolveFileUrl($storedFilePath),
            'dokumen_versions' => $dokumenVersions,
            'status' => $status,
            'days_remaining' => $daysRemaining,
            'jenis_kerjasama' => $k->jenis_kerjasama,
            'jenis_dokumen' => $k->jenis_dokumen,
            'has_adendum' => $hasAdendum,
            'adendum_count' => $adendumItems->count(),
            'adendum_items' => $adendumItems->all(),
            'nomor_suratM' => $k->nomor_suratM,
            'nomor_suratP' => $k->nomor_suratP,
            'urusan' => $k->urusan,
            'pembiayaan' => $k->pembiayaan,
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