<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminKerjasamaRequest;
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
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DataKerjasamaController extends Controller
{
    public function index(Request $request)
    {
        $query = Kerjasama::with(['mitra', 'admin', 'latestPeriode', 'kategori', 'riwayatStatus']);

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

                // Related models
                $q->orWhereHas('kategori', fn ($q) => $q->where('nama_kategori', 'like', "%{$search}%"))
                  ->orWhereHas('mitra', function ($q) use ($search) {
                        $q->where('nama_perusahaan', 'like', "%{$search}%");
                        if (Schema::hasColumn('mitras', 'pic')) {
                            $q->orWhere('pic', 'like', "%{$search}%");
                        }
                        if (Schema::hasColumn('mitras', 'no_handphone')) {
                            $q->orWhere('no_handphone', 'like', "%{$search}%");
                        }
                        if (Schema::hasColumn('mitras', 'alamat')) {
                            $q->orWhere('alamat', 'like', "%{$search}%");
                        }
                  })
                                ->orWhereHas('riwayatStatus', function ($q) use ($search) {
                                                $q->where('catatan', 'like', "%{$search}%")
                                                    ->orWhere('judul', 'like', "%{$search}%");
                                    })
                                    // If the search is numeric, also allow searching by year on the latest periode
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

                'null'  => $query->where('pemrakarsa', 'M')->whereNull('status_persetujuan'),
                default => null,
            };
        }

        [$sortBy, $sortDir] = $this->resolveSort($request);

        $kerjasama = $query->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $kerjasama->getCollection()->transform(function (Kerjasama $k) {
            $periode       = $k->latestPeriode;
            $jangkaWaktu   = $this->formatJangkaWaktu($periode?->tanggal_mulai, $periode?->tanggal_berakhir);
            $statusKontrak = $this->computeStatusKontrak($k, $periode?->tanggal_berakhir);

            $prosesList = $k->riwayatStatus->map(function ($r) {
                // Prefer stored title (`judul`) if present, otherwise fall back to catatan or status name
                $label = $r->judul ?: ($r->catatan ?: ($r->status?->jenis_status ?? null));
                
                // Prefer divisi from Eloquent relation if available
                $divisi = $r->admin?->divisi ?? null;

                // Fallback: attempt to resolve penanggung_jawab string to an Admin
                if (! $divisi && $r->penanggung_jawab) {
                    $adminUser = Admin::whereHas('user', function ($q) use ($r) {
                        $q->where('email', $r->penanggung_jawab);
                        if (Schema::hasColumn('users', 'username')) {
                            $q->orWhere('username', $r->penanggung_jawab);
                        }
                        if (Schema::hasColumn('users', 'name')) {
                            $q->orWhere('name', $r->penanggung_jawab);
                        }
                    })->first();

                    if (! $adminUser) {
                        $adminUser = Admin::where('nama', $r->penanggung_jawab)
                            ->orWhere('divisi', $r->penanggung_jawab)
                            ->first();
                    }

                    $divisi = $adminUser?->divisi ?? $r->penanggung_jawab;
                }
                
                return [
                    'id' => $r->id_riwayat,
                    // show the stored catatan (which will be the entered title when created)
                    'title' => $label,
                    'label' => $label,
                    'catatan' => $r->catatan,
                    'penanggung' => $divisi,
                    'tanggal' => $r->tanggal,
                    'file' => $r->file,
                ];
            })->toArray();

            $riwayatCount = $k->riwayatStatus->count();

            // If finalized, clearly mark as Selesai so frontend can display orange badge
            if ($k->is_finalized) {
                $statusDisplay = 'Selesai';
            } else {
                if ($riwayatCount > 0) {
                    $statusDisplay = 'Proses ' . $riwayatCount;
                } else {
                    $statusDisplay = $k->status_persetujuan?->value === 'disetujui' ? 'Diterima' : ($k->status_persetujuan?->value ?? 'Proses');
                }
            }

            return [
                'id_kerjasama'       => $k->id_kerjasama,
                'tahun'              => $periode ? Carbon::parse($periode->tanggal_mulai)->year : null,
                'pemrakarsa'         => $k->pemrakarsa,
                'mitra'              => $k->mitra?->nama_perusahaan,
                'nama_pihak_luar'    => $k->nama_pihak_luar,
                'pihak'              => $k->pemrakarsa === 'M'
                                        ? $k->mitra?->nama_perusahaan
                                        : ($k->mitra?->nama_perusahaan ?? $k->nama_pihak_luar),
                'judul'              => $k->judul,
                'nomor_surat'        => $k->nomor_surat     ?? null,
                'nomor_suratM'       => $k->nomor_suratM    ?? $k->nomor_surat ?? null,
                'nomor_suratP'       => $k->nomor_suratP    ?? null,
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
                'status_display' => $statusDisplay,
                'status_aktif'       => $statusKontrak,
                'created_at'         => $k->created_at?->format('d/m/Y'),
                'proses'             => $prosesList,
                'latest_mitra_revision' => (function () use ($k) {
                    $row = DB::table('dokumen as d')
                        ->join('users as u', 'u.id_user', '=', 'd.created_by')
                        ->where('d.id_kerjasama', $k->id_kerjasama)
                        ->where('u.role', 'mitra')
                        ->orderByDesc('d.created_at')
                        ->first(['d.id_dokumen', 'd.nama_file', 'd.lokasi_file', 'd.versi_dokumen', 'd.created_at']);

                    if (! $row) return null;

                    return [
                        'id_dokumen' => $row->id_dokumen,
                        'nama_file' => $row->nama_file,
                        'lokasi_file' => $row->lokasi_file,
                        'versi' => $row->versi_dokumen,
                        'created_at' => $row->created_at,
                    ];
                })(),
            ];
        });

        return Inertia::render('Admin/DataKerjasama', [
            'kerjasama' => $kerjasama,
            'mitras'    => Mitra::orderBy('nama_perusahaan')
                ->get(['id_mitra', 'nama_perusahaan'])
                ->map(fn (Mitra $mitra) => [
                    'id_mitra'        => $mitra->id_mitra,
                    'nama_perusahaan' => $mitra->nama_perusahaan,
                ]),
            'filters' => array_merge(
                $request->only([
                    'search', 'tahun', 'jenis_kerjasama', 'jenis_dokumen',
                    'pembiayaan', 'is_finalized', 'status', 'sort_by', 'sort_dir',
                ]),
                ['pemrakarsa' => $pemrakarsa]
            ),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers untuk penanggung jawab
    // -------------------------------------------------------------------------

    private function resolvePenanggung(Request $request): string
    {
        // Prioritas 1: dari request (dikirim Vue)
        $fromRequest = trim((string) $request->input('penanggung', ''));
        if ($fromRequest !== '') {
            return $fromRequest;
        }

        // Prioritas 2: dari data admin yang login
        $admin = $request->user()?->admin;
        if ($admin) {
            $divisi = trim($admin->divisi ?? '');
            if ($divisi !== '') return $divisi;
        }

        // Prioritas 3: email user
        return $request->user()?->email ?? 'Admin';
    }

    // -------------------------------------------------------------------------
    // Proses: store
    // -------------------------------------------------------------------------

    public function storeProcess(Request $request, int $id)
    {
        $kerjasama = Kerjasama::findOrFail($id);
        $admin     = $request->user()?->admin;

        if (! $admin) {
            $admin = Admin::firstOrCreate([
                'id_user' => $request->user()->id_user,
            ], [
                'nama' => $request->user()->email ?? 'Admin',
                'divisi' => 'Auto-generated',
            ]);
        }

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'file'        => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'penanggung'  => ['nullable', 'string'],
            'catatan'     => ['nullable', 'string'],
            'is_finished' => ['nullable'],
        ]);

        $file        = $request->file('file');
        $isFinished  = $request->boolean('is_finished', false);
        $penanggung  = $this->resolvePenanggung($request);

        $createdDokumen = null;
        $createdRiwayat = null;

        DB::transaction(function () use ($kerjasama, $file, $request, $admin, $isFinished, $penanggung, &$createdDokumen, &$createdRiwayat) {
            $title   = (string) $request->input('title');
            $catatan = $request->input('catatan');

            // Simpan PDF jika ada
            if ($file instanceof UploadedFile) {
                $nextVersion = ((int) $kerjasama->dokumen()->max('versi_dokumen')) + 1;
                $path        = $file->store('dokumen-kerjasama', 'public');

                $createdDokumen = Dokumen::create([
                    'id_kerjasama'  => $kerjasama->id_kerjasama,
                    'nama_file'     => $file->getClientOriginalName(),
                    'lokasi_file'   => $path,
                    'versi_dokumen' => $nextVersion,
                    'created_by'    => $admin?->id_user ?? $request->user()->id_user,
                ]);
            }

            // Tentukan jenis status
            $lower       = mb_strtolower($title);
            $jenisStatus = $isFinished ? 'disetujui'
                : (str_contains($lower, 'diterima') || str_contains($lower, 'selesai') ? 'disetujui'
                : (str_contains($lower, 'ditolak') ? 'ditolak'
                : (str_contains($lower, 'revisi')  ? 'revisi'
                : 'proses')));

            $createdRiwayat = RiwayatStatus::recordStatus(
                idKerjasama:     (int) $kerjasama->id_kerjasama,
                jenisStatus:     $jenisStatus,
                idAdmin:         (int) $admin->id_admin,
                catatan:         $catatan,
                penanggungJawab: $penanggung,
                judul:           $title,
                file:            $createdDokumen ? $createdDokumen->lokasi_file : null,
            );

            \Log::info('RiwayatStatus created', [
                'id_riwayat' => $createdRiwayat->id_riwayat ?? null,
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'jenis_status' => $jenisStatus,
            ]);

            if ($isFinished) {
                $kerjasama->update([
                    'status_negosiasi'   => 'Selesai',
                    'is_finalized'       => true,
                    'tipe'               => 'mitra',
                    'pemrakarsa'         => 'M',
                    'status_aktif'       => 'aktif',
                    'status_persetujuan' => 'disetujui',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Proses berhasil disimpan.');
    }

    // -------------------------------------------------------------------------
    // Proses: update
    // -------------------------------------------------------------------------

    public function updateProcess(Request $request, int $id, int $prosesId)
    {
        $kerjasama = Kerjasama::findOrFail($id);
        $admin     = $request->user()?->admin;

        if (! $admin) {
            $admin = Admin::firstOrCreate([
                'id_user' => $request->user()->id_user,
            ], [
                'nama' => $request->user()->email ?? 'Admin',
                'divisi' => 'Auto-generated',
            ]);
        }

        $request->validate([
            'title'       => ['nullable', 'string', 'max:255'],
            'file'        => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'penanggung'  => ['nullable', 'string'],
            'catatan'     => ['nullable', 'string'],
            'is_finished' => ['nullable'],
        ]);

        $file       = $request->file('file');
        $isFinished = $request->boolean('is_finished', false);
        $penanggung = $this->resolvePenanggung($request);

        $createdDokumen = null;
        $createdRiwayat = null;

        DB::transaction(function () use ($kerjasama, $file, $request, $admin, $isFinished, $penanggung) {
            $title   = (string) $request->input('title', '');
            $catatan = $request->input('catatan');

            // Simpan PDF jika ada
            if ($file instanceof UploadedFile) {
                $nextVersion = ((int) $kerjasama->dokumen()->max('versi_dokumen')) + 1;
                $path        = $file->store('dokumen-kerjasama', 'public');

                $createdDokumen = Dokumen::create([
                    'id_kerjasama'  => $kerjasama->id_kerjasama,
                    'nama_file'     => $file->getClientOriginalName(),
                    'lokasi_file'   => $path,
                    'versi_dokumen' => $nextVersion,
                    'created_by'    => $admin?->id_user ?? $request->user()->id_user,
                ]);
            }

            // Tentukan jenis status
            $lower       = mb_strtolower($title);
            $jenisStatus = $isFinished ? 'disetujui'
                : (str_contains($lower, 'diterima') || str_contains($lower, 'selesai') ? 'disetujui'
                : (str_contains($lower, 'ditolak') ? 'ditolak'
                : (str_contains($lower, 'revisi')  ? 'revisi'
                : 'proses')));

            $createdRiwayat = RiwayatStatus::recordStatus(
                idKerjasama:     (int) $kerjasama->id_kerjasama,
                jenisStatus:     $jenisStatus,
                idAdmin:         (int) $admin->id_admin,
                catatan:         $catatan,
                penanggungJawab: $penanggung,
                judul:           $title,
                file:            $createdDokumen ? $createdDokumen->lokasi_file : null,
            );

            \Log::info('RiwayatStatus updated/created', [
                'id_riwayat' => $createdRiwayat->id_riwayat ?? null,
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'jenis_status' => $jenisStatus,
            ]);

            if ($isFinished) {
                // mark negotiation finished and move to riwayat (finalized)
                $kerjasama->update([
                    'status_negosiasi' => 'Selesai',
                    'is_finalized'     => true,
                    'tipe'             => 'mitra',
                    'status_aktif'     => 'aktif',
                    'pemrakarsa'       => 'M',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Proses berhasil disimpan.');
    }

    // -------------------------------------------------------------------------
    // Store kerjasama baru (pemerintah)
    // -------------------------------------------------------------------------

    public function store(StoreAdminKerjasamaRequest $request)
    {
        $validated = $request->validated();
        $admin     = $request->user()->admin;

        DB::transaction(function () use ($validated, $admin) {
            $jenisDokumen = $validated['jenis_dokumen'] ?? 'KSB';

            $kerjasama = Kerjasama::create([
                'id_mitra'           => $validated['id_mitra'],
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

            PeriodeKerjasama::create([
                'id_kerjasama'     => $kerjasama->id_kerjasama,
                'tanggal_mulai'    => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_selesai'],
                'keterangan'       => 'Admin input - ' . $validated['jangka_waktu_bulan'] . ' bulan',
            ]);

            $file = $validated['dokumen_file'];
            $path = $file->store('dokumen-kerjasama', 'public');

            Dokumen::create([
                'id_kerjasama'  => $kerjasama->id_kerjasama,
                'jenis_dokumen' => $jenisDokumen,
                'nama_file'     => $file->getClientOriginalName(),
                'lokasi_file'   => $path,
                'versi_dokumen' => 1,
                'created_by'    => $admin->id_user,
            ]);

            // Do not create an initial riwayat/proses entry for admin-created kerjasama.
            // New admin entries should start with no proses; status_persetujuan is set to 'disetujui'.
        });

        return redirect()
            ->route('admin.data-kerjasama.index')
            ->with('success', 'Data kerjasama berhasil ditambahkan.');
    }

    public function updateNomorSurat(Request $request, int $id)
    {
        $validated = $request->validate([
            'nomor_suratM' => ['nullable', 'string', 'max:100'],
            'nomor_suratP' => ['nullable', 'string', 'max:100'],
        ]);

        $kerjasama = Kerjasama::findOrFail($id);

        $updates = [];
        if (array_key_exists('nomor_suratM', $validated) && $validated['nomor_suratM'] !== null) {
            $updates['nomor_suratM'] = $validated['nomor_suratM'];
        }
        if (array_key_exists('nomor_suratP', $validated) && $validated['nomor_suratP'] !== null) {
            $updates['nomor_suratP'] = $validated['nomor_suratP'];
        }

        if ($updates === []) {
            return back()->withErrors([
                'nomor_surat' => 'Nomor surat belum diisi.',
            ]);
        }

        $kerjasama->update($updates);

        return back()->with('success', 'Nomor surat berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function formatJangkaWaktu(?string $mulai, ?string $berakhir): ?string
    {
        if (! $mulai || ! $berakhir) return null;

        $start           = Carbon::parse($mulai);
        $end             = Carbon::parse($berakhir);
        $months          = $start->diffInMonths($end);
        $years           = intdiv($months, 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) return "{$years} tahun {$remainingMonths} bulan";
        if ($years > 0) return "{$years} tahun";
        return "{$months} bulan";
    }

    private function computeStatusKontrak(Kerjasama $kerjasama, ?string $tanggalBerakhir): ?string
    {
        if (! $tanggalBerakhir) return null;

        if ($kerjasama->pemrakarsa === 'M' && $kerjasama->status_persetujuan?->value !== 'disetujui') {
            return null;
        }

        $today = Carbon::today();
        $end   = Carbon::parse($tanggalBerakhir);

        if ($end->lt($today)) return 'berakhir';
        if ($today->diffInMonths($end, false) <= 3) return 'segera berakhir';
        return 'aktif';
    }

    private function resolveSort(Request $request): array
    {
        $allowedSort = ['created_at', 'judul', 'jenis_kerjasama', 'jenis_dokumen', 'urusan', 'pemrakarsa'];

        $sortBy = (string) $request->input('sort_by', 'created_at');
        if (! in_array($sortBy, $allowedSort, true)) $sortBy = 'created_at';

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        return [$sortBy, $sortDir];
    }
}
