<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StoreKerjasamaRequest;
use App\Models\Admin;
use App\Models\Dokumen;
use App\Models\KategoriKerjasama;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KerjasamaController extends Controller
{
    public function index(Request $request)
    {
        $mitra = $request->user()->mitra;

        $query = Kerjasama::query()
            ->where('id_mitra', $mitra->id_mitra)
            ->with(['latestPeriode'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('nomor_suratM', 'like', "%{$search}%")
                    ->orWhere('jenis_kerjasama', 'like', "%{$search}%")
                    ->orWhere('jenis_dokumen', 'like', "%{$search}%");
            });
        }

        $kerjasama = $query->paginate(10)->withQueryString();

        $kerjasama->getCollection()->transform(function (Kerjasama $item) {
            return [
                'id_kerjasama' => $item->id_kerjasama,
                'judul' => $item->judul,
                'jenis_kerjasama' => $item->jenis_kerjasama,
                'jenis_dokumen' => $item->jenis_dokumen,
                'nomor_suratM' => $item->nomor_suratM,
                'urusan' => $item->urusan,
                'pembiayaan' => $item->latestPeriode?->keterangan,
                'daerah' => $item->daerah,
                'tanggal_mulai' => $item->latestPeriode?->tanggal_mulai,
                'tanggal_selesai' => $item->latestPeriode?->tanggal_berakhir,
                'status_persetujuan' => $item->status_persetujuan?->value ?? 'menunggu',
                'status_negosiasi' => $item->status_negosiasi,
                'catatan_persetujuan' => $item->catatan_persetujuan,
                'is_finalized' => $item->is_finalized,
                'created_at' => $item->created_at?->format('d/m/Y'),
            ];
        });

        return Inertia::render('Mitra/Kerjasama/Index', [
            'mitra' => [
                'id_mitra' => $mitra->id_mitra,
                'nama_perusahaan' => $mitra->nama_perusahaan,
            ],
            'kerjasama' => $kerjasama,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreKerjasamaRequest $request)
    {
        $mitra = $request->user()->mitra;
        $validated = $request->validated();
        $defaultAdminEmail = (string) config('services.default_admin_email');

        $admin = Admin::query()
            ->whereHas('user', fn ($query) => $query->where('email', $defaultAdminEmail))
            ->first();
        $admin ??= Admin::query()->orderBy('id_admin')->first();
        abort_if($admin === null, 422, 'Belum ada admin yang dapat memproses pengajuan.');
        $kategori = KategoriKerjasama::query()
            ->firstOrCreate(
                ['nama_kategori' => $validated['jenis_kerjasama']],
                ['deskripsi' => 'Kategori dari pengajuan mitra', 'file_template' => '-']
            );

        DB::transaction(function () use ($validated, $request, $mitra, $admin, $kategori) {
            $kerjasama = Kerjasama::create([
                'id_mitra' => $mitra->id_mitra,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategori->id_kategori,
                'judul' => $validated['judul'],
                'nomor_suratM' => $validated['nomor_suratM'],
                'nomor_suratP' => null,
                'urusan' => $validated['urusan'],
                'daerah' => '-',
                'status_aktif' => 'aktif',
                'pemrakarsa' => 'M',
                'jenis_kerjasama' => $validated['jenis_kerjasama'],
                'jenis_dokumen' => $validated['jenis_dokumen'],
                'tipe' => 'mitra',
                'nama_pihak_luar' => $validated['nama_pihak_luar'],
                'is_finalized' => false,
                'status_negosiasi' => 'Pengajuan baru',
                'status_persetujuan' => null,
                'catatan_persetujuan' => null,
            ]);

            PeriodeKerjasama::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_selesai'],
                'keterangan' => $validated['pembiayaan'],
            ]);

            $file = $validated['dokumen_file'];
            $path = $file->store('dokumen-kerjasama', 'public');

            Dokumen::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'nama_file' => $file->getClientOriginalName(),
                'lokasi_file' => $path,
                'versi_dokumen' => 1,
                'created_by' => $request->user()->id_user,
            ]);
        });

        return redirect()
            ->route('mitra.kerjasama.index')
            ->with('success', 'Pengajuan kerjasama berhasil dikirim.');
    }

    /**
     * Show step1 of pengajuan (fallback).
     */
    public function createStep1(Request $request)
    {
        return Inertia::render('Mitra/Pengajuan/Step1');
    }

    /**
     * Handle step1 POST (fallback) and redirect to step2 or index.
     */
    public function storeStep1(Request $request)
    {
        // Validate step1 fields (exclude file upload which is handled in final submit)
        $validated = $request->validate([
            'jenis_kerjasama' => ['required', 'string', 'max:100'],
            'jenis_dokumen' => ['required', 'string', 'max:100'],
            'judul' => ['required', 'string', 'max:255'],
            'nama_pihak_luar' => ['required', 'string', 'max:255'],
            'nomor_suratM' => ['required', 'string', 'max:100'],
            'pembiayaan' => ['required', 'string', 'max:255'],
            'urusan' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date'],
        ]);

        // Store partial pengajuan in session for step flow
        $request->session()->put('pengajuan.step1', $validated);

        return redirect()->route('mitra.pengajuan.step2');
    }

    /**
     * Show step2 of pengajuan (fallback).
     */
    public function createStep2(Request $request)
    {
        $step1 = $request->session()->get('pengajuan.step1', []);

        // Load kategoris for the select options
        $kategoris = KategoriKerjasama::query()->get()->map(function ($k) {
            return [
                'id_kategori' => $k->id_kategori ?? $k->id,
                'nama_kategori' => $k->nama_kategori ?? $k->nama_kategori,
            ];
        })->values();

        return Inertia::render('Mitra/Pengajuan/Step2', [
            'step1' => $step1,
            'kategoris' => $kategoris,
        ]);
    }
}