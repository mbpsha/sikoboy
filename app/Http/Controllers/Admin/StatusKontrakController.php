<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPersetujuan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePersetujuanRequest;
use App\Http\Requests\Admin\UpdateStatusKontrakRequest;
use App\Models\Kerjasama;
use App\Models\RiwayatStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatusKontrakController extends Controller
{
    /**
     * List all kerjasama that are NOT yet finalised (i.e. still in negotiation).
     * Only mitra-type kerjasama go through this approval process.
     */
    public function index(Request $request)
    {
        $query = Kerjasama::mitraTipe()
            ->where('is_finalized', false)
            ->with(['mitra', 'latestPeriode', 'dokumen', 'kategori']);

        $this->applyFilters($query, $request);

        [$sortBy, $sortDir] = $this->resolveSort($request);

        $kerjasama = $query->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $kerjasama->getCollection()->transform(fn ($k) => $this->formatRow($k));

        return Inertia::render('Admin/StatusKontrak/Index', [
            'kerjasama' => $kerjasama,
            'filters' => $request->only(['search', 'tahun', 'jenis_kerjasama', 'jenis_dokumen', 'status_persetujuan', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Update the free-text negotiation status (manual stage notes by admin).
     */
    public function update(int $id, UpdateStatusKontrakRequest $request)
    {
        $kerjasama = Kerjasama::mitraTipe()->where('is_finalized', false)->findOrFail($id);

        $kerjasama->update([
            'status_negosiasi' => $request->validated('status_negosiasi'),
        ]);

        return back()->with('success', 'Status kontrak berhasil diperbarui.');
    }

    /**
     * Update the approval status (disetujui / revisi / ditolak) with optional catatan.
     */
    public function updatePersetujuan(int $id, UpdatePersetujuanRequest $request)
    {
        $kerjasama = Kerjasama::mitraTipe()->where('is_finalized', false)->findOrFail($id);
        $validated = $request->validated();
        $adminId = (int) $request->user()->admin->id_admin;

        DB::transaction(function () use ($kerjasama, $validated, $adminId): void {
            $kerjasama->update([
                'catatan_persetujuan' => $validated['catatan_persetujuan'] ?? null,
            ]);

            RiwayatStatus::recordStatus(
                idKerjasama: (int) $kerjasama->id_kerjasama,
                jenisStatus: (string) $validated['status_persetujuan'],
                idAdmin: $adminId,
                catatan: $validated['catatan_persetujuan'] ?? null,
            );
        });

        return back()->with('success', 'Status persetujuan berhasil diperbarui.');
    }

    /**
     * Mark a kontrak as finalised — it will then appear in Riwayat Kerjasama (mitra).
     */
    public function finalize(int $id, Request $request)
    {
        $kerjasama = Kerjasama::mitraTipe()->where('is_finalized', false)->findOrFail($id);
        $adminId = (int) $request->user()->admin->id_admin;

        DB::transaction(function () use ($kerjasama, $adminId): void {
            $kerjasama->update([
                'is_finalized' => true,
            ]);

            RiwayatStatus::recordStatus(
                idKerjasama: (int) $kerjasama->id_kerjasama,
                jenisStatus: StatusPersetujuan::Disetujui->value,
                idAdmin: $adminId,
                catatan: 'Finalisasi kontrak',
            );
        });

        return back()->with('success', 'Kontrak berhasil difinalisasi dan masuk ke Riwayat Kerjasama.');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('nomor_suratM', 'like', "%{$search}%")
                    ->orWhere('nomor_suratP', 'like', "%{$search}%")
                    ->orWhere('urusan', 'like', "%{$search}%")
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

        if ($request->filled('status_persetujuan')) {
            if ($request->status_persetujuan === 'null') {
                $query->whereNull('status_persetujuan');
            } else {
                $query->where('status_persetujuan', $request->status_persetujuan);
            }
        }
    }

    private function formatRow(Kerjasama $k): array
    {
        $periode = $k->latestPeriode;

        $jangkaWaktu = null;
        if ($periode) {
            $mulai = Carbon::parse($periode->tanggal_mulai);
            $berakhir = Carbon::parse($periode->tanggal_berakhir);
            $jangkaWaktu = $mulai->diffInMonths($berakhir).' bulan';
        }

        return [
            'id_kerjasama' => $k->id_kerjasama,
            'tahun' => $periode ? Carbon::parse($periode->tanggal_mulai)->year : null,
            'mitra' => $k->mitra?->nama_perusahaan,
            'judul' => $k->judul,
            'nomor_surat' => $k->nomor_surat,
            'jenis_kerjasama' => $k->jenis_kerjasama,
            'jenis_dokumen' => $k->jenis_dokumen,
            'urusan' => $k->urusan,
            'tanggal_mulai' => $periode?->tanggal_mulai,
            'tanggal_berakhir' => $periode?->tanggal_berakhir,
            'jangka_waktu' => $jangkaWaktu,
            'status_negosiasi' => $k->status_negosiasi,
            'status_persetujuan' => $k->status_persetujuan?->value,
            'catatan_persetujuan' => $k->catatan_persetujuan,
            'files' => $k->dokumen->map(fn ($d) => [
                'id' => $d->id_dokumen,
                'nama_file' => $d->nama_file,
                'lokasi_file' => $d->lokasi_file,
                'versi_dokumen' => $d->versi_dokumen,
                'created_at' => $d->created_at,
            ])->values(),
        ];
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
