<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StoreKerjasamaRequest;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\Admin;
use App\Models\PeriodeKerjasama;
use App\Models\RiwayatStatus;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DataKerjasamaController extends Controller
{
    public function index(Request $request)
    {
        $query = Kerjasama::with(['mitra', 'admin', 'latestPeriode', 'kategori', 'riwayatStatus', 'dokumen', 'finalDokumen']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%");

                if (Schema::hasColumn('kerjasama', 'nomor_suratM')) {
                    $q->orWhere('nomor_suratM', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'nomor_suratP')) {
                    $q->orWhere('nomor_suratP', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'urusan')) {
                    $q->orWhere('urusan', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'jenis_kerjasama')) {
                    $q->orWhere('jenis_kerjasama', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'jenis_dokumen')) {
                    $q->orWhere('jenis_dokumen', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'pembiayaan')) {
                    $q->orWhere('pembiayaan', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'daerah')) {
                    $q->orWhere('daerah', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('kerjasama', 'nama_pihak_luar')) {
                    $q->orWhere('nama_pihak_luar', 'like', "%{$search}%");
                }

                $q->orWhereHas('kategori', fn ($q) => $q->where('nama_kategori', 'like', "%{$search}%"))
                  ->orWhereHas('mitra', function ($q) use ($search) {
                        $q->where('nama_perusahaan', 'like', "%{$search}%");
                        if (Schema::hasColumn('mitras', 'pic')) { $q->orWhere('pic', 'like', "%{$search}%"); }
                        if (Schema::hasColumn('mitras', 'no_handphone')) { $q->orWhere('no_handphone', 'like', "%{$search}%"); }
                        if (Schema::hasColumn('mitras', 'alamat')) { $q->orWhere('alamat', 'like', "%{$search}%"); }
                  })
                  ->orWhereHas('riwayatStatus', function ($q) use ($search) {
                        $q->where('catatan', 'like', "%{$search}%")->orWhere('judul', 'like', "%{$search}%");
                  })
                  ->orWhere(function ($q) use ($search) {
                        if (is_numeric($search)) {
                            $q->orWhereHas('latestPeriode', fn ($p) => $p->whereYear('tanggal_mulai', $search));
                        }
                  });
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
        if ($request->filled('pembiayaan')) {
            $query->where('pembiayaan', $request->pembiayaan);
        }
        if ($request->filled('is_finalized') && $request->is_finalized !== '') {
            $query->where('is_finalized', (bool) $request->is_finalized);
        }

        if ($request->filled('status')) {
            $today       = Carbon::today()->toDateString();
            $threeMonths = Carbon::today()->addMonths(3)->toDateString();

            match ($request->status) {
                'aktif' => $query->where(function ($q) {
                        $q->where('pemrakarsa', 'P')->orWhere(fn($m) => $m->where('pemrakarsa', 'M')->where('status_persetujuan', 'disetujui'));
                    })->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '>', Carbon::today()->addMonths(3)->toDateString())),
                'berakhir' => $query->where(function ($q) {
                        $q->where('pemrakarsa', 'P')->orWhere(fn($m) => $m->where('pemrakarsa', 'M')->where('status_persetujuan', 'disetujui'));
                    })->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '<', $today)),
                'segera berakhir' => $query->where(function ($q) {
                        $q->where('pemrakarsa', 'P')->orWhere(fn($m) => $m->where('pemrakarsa', 'M')->where('status_persetujuan', 'disetujui'));
                    })->whereHas('latestPeriode', fn ($q) => $q->where('tanggal_berakhir', '>=', $today)->where('tanggal_berakhir', '<=', $threeMonths)),
                'null'  => $query->where('pemrakarsa', 'M')->whereNull('status_persetujuan'),
                default => null,
            };
        }

        [$sortBy, $sortDir] = $this->resolveSort($request);
        $perPage = min(max((int) $request->input('per_page', 15), 1), 10000);

        $kerjasama = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        $kerjasama->getCollection()->transform(function (Kerjasama $k) {
            $periode       = $k->latestPeriode;
            $jangkaWaktu   = $this->formatJangkaWaktu($periode?->tanggal_mulai, $periode?->tanggal_berakhir);
            $statusKontrak = $this->computeStatusKontrak($k, $periode?->tanggal_berakhir);

            // 1. Cari dokumen terbaru dari tabel relasi dokumen langsung
            $latestDocument = Dokumen::where('id_kerjasama', $k->id_kerjasama)
                ->orderByDesc('versi_dokumen')
                ->orderByDesc('created_at')
                ->first();

            $prosesList = $k->riwayatStatus->map(function ($r) use ($k) {
                $label  = $r->judul ?: ($r->catatan ?: ($r->status?->jenis_status ?? null));
                $divisi = $r->admin?->divisi ?? null;

                if (! $divisi && $r->penanggung_jawab) {
                    $adminUser = Admin::whereHas('user', function ($q) use ($r) {
                        $q->where('email', $r->penanggung_jawab);
                    })->first() ?? Admin::where('nama', $r->penanggung_jawab)->orWhere('divisi', $r->penanggung_jawab)->first();
                    $divisi = $adminUser?->divisi ?? $r->penanggung_jawab;
                }

                $dokumenMitra = Dokumen::where('id_kerjasama', $k->id_kerjasama)
                    ->where('id_riwayat', $r->id_riwayat)
                    ->latest('created_at')
                    ->first();
                
                return [
                    'id'         => $r->id_riwayat,
                    'title'      => $label,
                    'label'      => $label,
                    'catatan'    => $r->catatan,
                    'penanggung' => $divisi,
                    'tanggal'    => $r->tanggal,
                    'file'       => $r->file,
                    'file_mitra' => $dokumenMitra?->lokasi_file,
                ];
            })->toArray();

            $riwayatCount = $k->riwayatStatus->count();
            $statusDisplay = $k->is_finalized ? 'Selesai' : ($riwayatCount > 0 ? 'Proses ' . $riwayatCount : ($k->status_persetujuan?->value === 'disetujui' ? 'Diterima' : ($k->status_persetujuan?->value ?? 'Proses')));

            // 2. Tentukan Path File Utama Secara Presisi (Dokumen -> Final Dokumen -> Fallback Keterangan Periode)
            $storedFilePath = $latestDocument?->lokasi_file ?? ($k->finalDokumen?->lokasi_file ?? null);
            $storedFileName = $latestDocument?->nama_file ?? ($k->finalDokumen?->nama_file ?? null);

            if (! $storedFilePath && is_string($periode?->keterangan) && str_contains($periode->keterangan, 'dokumen-kerjasama/')) {
                $storedFilePath = $periode->keterangan;
                $storedFileName = basename($storedFilePath);
            }

            $dokumenVersions = collect($k->relationLoaded('dokumen') ? $k->dokumen : [])
                ->sortBy(fn ($dokumen) => (int) $dokumen->versi_dokumen)
                ->values()
                ->map(fn ($dokumen) => [
                    'id_dokumen'    => $dokumen->id_dokumen,
                    'versi_dokumen' => (int) $dokumen->versi_dokumen,
                    'nama_file'     => $dokumen->nama_file,
                    'file_url'      => $this->resolveFileUrl($dokumen->lokasi_file),
                    'lokasi_file'   => $dokumen->lokasi_file,
                    'created_at'    => $dokumen->created_at ? Carbon::parse($dokumen->created_at)->translatedFormat('d F Y H:i') : null,
                    'tipe_dokumen'  => $dokumen->tipe_dokumen,
                ])->all();

            if (empty($dokumenVersions) && $latestDocument) {
                $dokumenVersions = [[
                    'id_dokumen'    => $latestDocument->id_dokumen,
                    'versi_dokumen' => (int) $latestDocument->versi_dokumen,
                    'nama_file'     => $latestDocument->nama_file,
                    'file_url'      => $this->resolveFileUrl($latestDocument->lokasi_file),
                    'lokasi_file'   => $latestDocument->lokasi_file,
                    'created_at'    => $latestDocument->created_at ? Carbon::parse($latestDocument->created_at)->translatedFormat('d F Y H:i') : null,
                    'tipe_dokumen'  => $latestDocument->tipe_dokumen,
                ]];
            }

            return [
                'id_kerjasama'       => $k->id_kerjasama,
                'tahun'              => $periode ? Carbon::parse($periode->tanggal_mulai)->year : null,
                'pemrakarsa'         => $k->pemrakarsa,
                'mitra'              => $k->mitra?->nama_perusahaan,
                'nama_pihak_luar'    => $k->nama_pihak_luar,
                'pihak'              => $k->pemrakarsa === 'M' ? $k->mitra?->nama_perusahaan : ($k->mitra?->nama_perusahaan ?? $k->nama_pihak_luar),
                'judul'              => $k->judul,
                'nomor_surat'        => $k->nomor_surat ?? null,
                'nomor_suratM'       => $k->nomor_suratM ?? $k->nomor_surat ?? null,
                'nomor_suratP'       => $k->nomor_suratP ?? null,
                'jenis_kerjasama'    => $k->jenis_kerjasama,
                'jenis_dokumen'      => $k->jenis_dokumen,
                'pembiayaan'         => $k->pembiayaan,
                'urusan'             => $k->urusan,
                'daerah'             => $k->daerah,
                'tanggal_mulai'      => $periode?->tanggal_mulai,
                'tanggal_berakhir'   => $periode?->tanggal_berakhir,
                'jangka_waktu'       => $jangkaWaktu,
                'is_finalized'       => $k->is_finalized,
                'status_negosiasi'   => $k->status_negosiasi,
                'status_persetujuan' => $k->status_persetujuan?->value,
                'status_display'     => $statusDisplay,
                'status_aktif'       => $statusKontrak,
                'created_at'         => $k->created_at?->format('d/m/Y'),
                'proses'             => $prosesList,
                'file_name'          => $storedFileName ?? null,
                'file_url'           => $this->resolveFileUrl($storedFilePath),
                'dokumen_versions'   => $dokumenVersions,
            ];
        });

        $currentYear = (int) date('Y');

        return Inertia::render('Admin/DataKerjasama', [
            'kerjasama' => $kerjasama,
            'years'     => array_map(fn (int $offset) => $currentYear - $offset, range(0, 5)),
            'mitras'    => Mitra::orderBy('nama_perusahaan')->get(['id_mitra', 'nama_perusahaan']),
            'filters'   => array_merge($request->only(['search', 'tahun', 'jenis_kerjasama', 'jenis_dokumen', 'pembiayaan', 'is_finalized', 'status', 'sort_by', 'sort_dir']), ['pemrakarsa' => $pemrakarsa]),
        ]);
    }

    public function store(StoreKerjasamaRequest $request)
    {
        $validated = $request->validated();
        
        $admin = $request->user()->admin;
        if (!$admin) {
            $admin = Admin::firstOrCreate(
                ['id_user' => $request->user()->id_user ?? $request->user()->id],
                ['nama' => $request->user()->name ?? $request->user()->email ?? 'Admin', 'divisi' => 'Auto-generated']
            );
        }

        try {
            DB::transaction(function () use ($validated, $admin, $request) {
                $jenisDokumen = $validated['jenis_dokumen'] ?? 'KSB';

                // 1. Simpan ke tabel Kerjasama
                $kerjasama = Kerjasama::create([
                    'id_mitra'           => $validated['id_mitra'] ?? null,
                    'id_admin'           => $admin->id_admin,
                    'id_kategori'        => $validated['id_kategori'] ?? null,
                    'judul'              => $validated['judul'],
                    'nomor_suratM'       => $validated['nomor_suratM'] ?? null,
                    'urusan'             => $validated['urusan'] ?? '-',
                    'daerah'             => $validated['daerah'] ?? '-',
                    'status_aktif'       => 'aktif',
                    'pembiayaan'         => $validated['pembiayaan'] ?? 'APBN',
                    'pemrakarsa'         => 'M',
                    'tipe'               => 'mitra',
                    'jenis_kerjasama'    => $validated['jenis_kerjasama'] ?? null,
                    'jenis_dokumen'      => $jenisDokumen,
                    'is_finalized'       => true,
                    'status_persetujuan' => 'disetujui',
                ]);

                // 2. Cek file dan tentukan path-nya
                $tglBerakhir = $validated['tanggal_selesai'] ?? ($validated['tanggal_berakhir'] ?? null);
                $pathKeterangan = 'Admin input - ' . ($validated['jangka_waktu_bulan'] ?? 0) . ' bulan';
                $file = $request->file('dokumen_file') ?? $request->file('file');
                
                if ($file && $file->isValid()) {
                    $pathKeterangan = $file->store('dokumen-kerjasama', 'public');
                }

                // 3. Simpan ke tabel Periode Kerjasama
                PeriodeKerjasama::create([
                    'id_kerjasama'     => $kerjasama->id_kerjasama,
                    'tanggal_mulai'    => $validated['tanggal_mulai'],
                    'tanggal_berakhir' => $tglBerakhir,
                    'keterangan'       => $pathKeterangan,
                ]);

                // 4. Simpan ke tabel Dokumen
                if ($file && $file->isValid()) {
                    Dokumen::create([
                        'id_kerjasama'  => $kerjasama->id_kerjasama,
                        'jenis_dokumen' => $jenisDokumen,
                        'nama_file'     => $file->getClientOriginalName(),
                        'lokasi_file'   => $pathKeterangan,
                        'versi_dokumen' => 1,
                        'tipe_dokumen'  => 'mitra',
                        'created_by'    => $request->user()->id_user ?? $request->user()->id,
                    ]);
                }
            });

            return redirect()->route('admin.data-kerjasama.index')->with('success', 'Data kerjasama berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan Data Kerjasama: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function storeProcess(Request $request, int $id)
    {
        $kerjasama = Kerjasama::findOrFail($id);
        $admin     = $request->user()?->admin ?? Admin::firstOrCreate(['id_user' => $request->user()->id_user], ['nama' => $request->user()->email ?? 'Admin', 'divisi' => 'Auto-generated']);

        $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'file'    => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'catatan' => ['nullable', 'string'],
        ]);

        $file       = $request->file('file');
        $isFinished = $request->boolean('is_finished', false);
        $penanggung = $this->resolvePenanggung($request);

        DB::transaction(function () use ($kerjasama, $file, $request, $admin, $isFinished, $penanggung) {
            $title    = (string) $request->input('title');
            $catatan  = $request->input('catatan');
            $filePath = $file instanceof UploadedFile ? $file->store('dokumen-kerjasama', 'public') : null;

            $lower       = mb_strtolower($title);
            $jenisStatus = $isFinished ? 'disetujui' : (str_contains($lower, 'diterima') || str_contains($lower, 'selesai') ? 'disetujui' : (str_contains($lower, 'ditolak') ? 'ditolak' : (str_contains($lower, 'revisi') ? 'revisi' : 'proses')));

            $riwayat = RiwayatStatus::recordStatus((int)$kerjasama->id_kerjasama, $jenisStatus, (int)$admin->id_admin, $catatan, $penanggung, $title, $filePath);

            if ($filePath) {
                $latestVersion = Dokumen::where('id_kerjasama', $kerjasama->id_kerjasama)->max('versi_dokumen') ?? 0;
                Dokumen::create([
                    'id_kerjasama'  => $kerjasama->id_kerjasama,
                    'id_riwayat'    => $riwayat->id_riwayat,
                    'jenis_dokumen' => $kerjasama->jenis_dokumen ?? 'KSB',
                    'nama_file'     => $file->getClientOriginalName(),
                    'lokasi_file'   => $filePath,
                    'versi_dokumen' => $latestVersion + 1,
                    'tipe_dokumen'  => 'mitra',
                    'created_by'    => $request->user()->id_user,
                ]);
            }

            if ($isFinished) {
                $kerjasama->update(['status_negosiasi' => 'Selesai', 'is_finalized' => true, 'status_aktif' => 'aktif', 'status_persetujuan' => 'disetujui']);
            }
        });

        return redirect()->back()->with('success', 'Proses berhasil disimpan.');
    }

    public function updateProcess(Request $request, int $id, int $prosesId)
    {
        $kerjasama = Kerjasama::findOrFail($id);
        $admin     = $request->user()?->admin ?? Admin::firstOrCreate(['id_user' => $request->user()->id_user], ['nama' => $request->user()->email ?? 'Admin', 'divisi' => 'Auto-generated']);

        $request->validate([
            'title'   => ['nullable', 'string', 'max:255'],
            'file'    => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'catatan' => ['nullable', 'string'],
        ]);

        $file       = $request->file('file');
        $isFinished = $request->boolean('is_finished', false);
        $penanggung = $this->resolvePenanggung($request);

        DB::transaction(function () use ($kerjasama, $file, $request, $admin, $isFinished, $penanggung, $prosesId) {
            $title   = (string) $request->input('title', '');
            $catatan = $request->input('catatan');
            $riwayat = RiwayatStatus::findOrFail($prosesId);

            if ($file instanceof UploadedFile) {
                $filePath = $file->store('dokumen-kerjasama', 'public');
                $riwayat->update(['file' => $filePath]);

                $latestVersion = Dokumen::where('id_kerjasama', $kerjasama->id_kerjasama)->max('versi_dokumen') ?? 0;
                Dokumen::create([
                    'id_kerjasama'  => $kerjasama->id_kerjasama,
                    'id_riwayat'    => $riwayat->id_riwayat,
                    'jenis_dokumen' => $kerjasama->jenis_dokumen ?? 'KSB',
                    'nama_file'     => $file->getClientOriginalName(),
                    'lokasi_file'   => $filePath,
                    'versi_dokumen' => $latestVersion + 1,
                    'tipe_dokumen'  => 'mitra',
                    'created_by'    => $request->user()->id_user,
                ]);
            }

            if ($title !== '') { $riwayat->update(['judul' => $title]); }
            if ($catatan !== null) { $riwayat->update(['catatan' => $catatan]); }
            if ($isFinished) {
                $kerjasama->update(['status_negosiasi' => 'Selesai', 'is_finalized' => true, 'status_aktif' => 'aktif']);
            }
        });

        return redirect()->back()->with('success', 'Proses berhasil disimpan.');
    }

    public function updateNomorSurat(Request $request, int $id)
    {
        $validated = $request->validate([
            'nomor_suratM' => ['nullable', 'string', 'max:100'],
            'nomor_suratP' => ['nullable', 'string', 'max:100'],
        ]);

        $kerjasama = Kerjasama::findOrFail($id);
        $updates   = array_filter($validated, fn($value) => $value !== null);

        if (empty($updates)) {
            return back()->withErrors(['nomor_surat' => 'Nomor surat belum diisi.']);
        }

        $kerjasama->update($updates);
        return back()->with('success', 'Nomor surat berhasil diperbarui.');
    }

    private function resolvePenanggung(Request $request): string
    {
        $fromRequest = trim((string) $request->input('penanggung', ''));
        if ($fromRequest !== '') return $fromRequest;

        $admin = $request->user()?->admin;
        if ($admin && trim($admin->divisi ?? '') !== '') return $admin->divisi;

        return $request->user()?->email ?? 'Admin';
    }

    private function formatJangkaWaktu(?string $mulai, ?string $berakhir): ?string
    {
        if (! $mulai || ! $berakhir) return null;
        $start  = Carbon::parse($mulai);
        $end    = Carbon::parse($berakhir);
        $months = $start->diffInMonths($end);
        $years  = intdiv($months, 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) return "{$years} tahun {$remainingMonths} bulan";
        if ($years > 0) return "{$years} tahun";
        return "{$months} bulan";
    }

    private function computeStatusKontrak(Kerjasama $kerjasama, ?string $tanggalBerakhir): ?string
    {
        if (! $tanggalBerakhir) return null;
        if ($kerjasama->pemrakarsa === 'M' && $kerjasama->status_persetujuan?->value !== 'disetujui') return null;

        $today = Carbon::today();
        $end   = Carbon::parse($tanggalBerakhir);

        if ($end->lt($today)) return 'berakhir';
        if ($today->diffInMonths($end, false) <= 3) return 'segera berakhir';
        return 'aktif';
    }

    private function resolveSort(Request $request): array
    {
        $allowedSort = ['created_at', 'judul', 'jenis_kerjasama', 'jenis_dokumen', 'urusan', 'pemrakarsa'];
        $sortBy      = (string) $request->input('sort_by', 'created_at');
        if (! in_array($sortBy, $allowedSort, true)) $sortBy = 'created_at';

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        return [$sortBy, $sortDir === 'asc' ? 'asc' : 'desc'];
    }

    private function resolveFileUrl(?string $path): ?string
    {
        if (! $path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        if (str_starts_with($path, '/')) return url($path);
        if (str_starts_with($path, 'storage/')) return asset($path);
        return asset('storage/' . ltrim($path, '/'));
    }
}