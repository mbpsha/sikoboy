<?php

namespace App\Http\Controllers\Mitra;

use App\Enums\UrusanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StoreKerjasamaRequest;
use App\Models\Admin;
use App\Models\Dokumen;
use App\Models\KategoriKerjasama;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    /**
     * Show pengajuan step 1 form.
     */
    public function createStep1(Request $request)
    {
        $mitra = $request->user()->mitra;
        return Inertia::render('Mitra/Pengajuan/Step1', [
            'mitra' => $mitra ? [
                'id_mitra' => $mitra->id_mitra,
                'nama_perusahaan' => $mitra->nama_perusahaan,
                'no_handphone' => $mitra->no_handphone,
                'alamat' => $mitra->alamat,
                'pic' => $mitra->pic,
            ] : null,
        ]);
    }

    /**
     * Show pengajuan step 2 form.
     */
    public function createStep2(Request $request)
    {
        $mitra = $request->user()->mitra;
        // In a real flow you'd validate session/previous step data here
        $kategoris = KategoriKerjasama::query()->orderBy('nama_kategori')->get()->map(function ($k) {
            return [
                'id_kategori' => $k->id_kategori,
                'nama_kategori' => $k->nama_kategori,
            ];
        })->values();

        $urusanOptions = UrusanEnum::cases();

        $jenisDokumen = [
            'KSB',
            'Nota Kesepakatan',
            'Perjanjian Teknis',
            'PKS',
            'Rencana Kerja',
            'MOU',
            'RKT',
            'LOI',
        ];

        return Inertia::render('Mitra/Pengajuan/Step2', [
            'mitra' => $mitra ? [
                'id_mitra' => $mitra->id_mitra,
                'nama_perusahaan' => $mitra->nama_perusahaan,
            ] : null,
            // also expose step1Data for form prefill
            'step1Data' => $mitra ? [
                'id_mitra' => $mitra->id_mitra,
                'nama_perusahaan' => $mitra->nama_perusahaan,
                'no_handphone' => $mitra->no_handphone,
                'alamat' => $mitra->alamat,
                'pic' => $mitra->pic,
            ] : null,
            'kategoris' => $kategoris,
            'jenisDokumen' => $jenisDokumen,
            'urusanOptions' => $urusanOptions,
        ]);
    }

    /**
     * Handle POST from pengajuan step 1 — validate and save to session, then go to step2.
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'nama_mitra' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:50'],
            'alamat' => ['required', 'string', 'max:1000'],
            'pic' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $mitra = $user?->mitra;

        $data = [
            'nama_perusahaan' => $validated['nama_mitra'],
            'no_handphone' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'pic' => $validated['pic'],
        ];

        if ($mitra) {
            $mitra->update($data);
        } else {
            // create minimal mitra record and associate with user
            $mitra = \App\Models\Mitra::create(array_merge(['id_user' => $user->id_user], $data));
        }

        // Persist draft mitra id in session so Step2 can reference if needed
        $request->session()->put('pengajuan.mitra_id', $mitra->id_mitra);

        return redirect()->route('mitra.pengajuan.step2');
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
                'pembiayaan' => $validated['pembiayaan'],
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
            $randomName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('dokumen-kerjasama', $randomName, 'public');

            Dokumen::create([
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'nama_file' => $randomName,
                'lokasi_file' => $path,
                'versi_dokumen' => 1,
                'created_by' => $request->user()->id_user,
                'tipe_dokumen' => 'mitra',
            ]);

            RiwayatStatus::recordStatus(
                idKerjasama: (int) $kerjasama->id_kerjasama,
                jenisStatus: 'diajukan',
                idAdmin: (int) $admin->id_admin,
                catatan: 'Pengajuan baru dari mitra',
                file: null,
            );
        });

        return redirect()
            ->route('mitra.profile.index')
            ->with('success', 'Pengajuan kerjasama berhasil dikirim.');
    }

    /**
     * Handle mitra uploading a revision file for an existing kerjasama.
     */
    public function uploadRevision(Request $request, int $id)
    {
        $kerjasama = Kerjasama::findOrFail($id);
        $mitra     = $request->user()->mitra;

        if (! $mitra || $kerjasama->id_mitra !== $mitra->id_mitra) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file        = $request->file('file');
        $randomName  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path        = $file->storeAs('dokumen-kerjasama', $randomName, 'public');
        $nextVersion = ((int) $kerjasama->dokumen()->max('versi_dokumen')) + 1;

        $dok = Dokumen::create([
            'id_kerjasama'  => $kerjasama->id_kerjasama,
            'nama_file'     => $randomName,
            'lokasi_file'   => $path,
            'versi_dokumen' => $nextVersion,
            'created_by'    => $request->user()->id_user,
            'tipe_dokumen'  => 'mitra',  // ✅ pastikan migration sudah dijalankan
        ]);

        // ✅ Return JSON bukan redirect
        return response()->json([
            'success'    => true,
            'message'    => 'File revisi berhasil diunggah.',
            'lokasi_file' => $dok->lokasi_file,
        ]);
    }
}