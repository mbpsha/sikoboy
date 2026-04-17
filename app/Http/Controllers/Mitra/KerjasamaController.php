<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerjasama;
use App\Models\Kerjasama;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KerjasamaController extends Controller
{
    /**
     * Show form pengajuan tahap 1 (Data Mitra)
     */
    public function createStep1()
    {
        return Inertia::render('Mitra/Pengajuan/Step1');
    }

    /**
     * Store step 1 data in session
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'pic' => 'required|string|max:255',
        ]);

        // Store in session for step 2
        session(['pengajuan_step1' => $validated]);

        return redirect()->route('mitra.pengajuan.step2');
    }

    /**
     * Show form pengajuan tahap 2 (Data Kerjasama)
     */
    public function createStep2()
    {
        $step1Data = session('pengajuan_step1', []);
        
        if (empty($step1Data)) {
            return redirect()->route('mitra.pengajuan.step1');
        }

        $kategoris = KategoriKerjasama::all();

        return Inertia::render('Mitra/Pengajuan/Step2', [
            'step1Data' => $step1Data,
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Store complete kerjasama submission
     */
    public function store(Request $request)
    {
        $step1Data = session('pengajuan_step1', []);
        
        if (empty($step1Data)) {
            return redirect()->route('mitra.pengajuan.step1');
        }

        $user = $request->user();
        
        $validated = $request->validate([
            'jenis_kerjasama' => 'required|exists:kategori_kerjasama,id_kategori',
            'jenis_dokumen' => 'required|string|max:255',
            'judul_dokumen' => 'required|string|max:255',
            'mitra_kerjasama' => 'required|string|max:255',
            'nomor_dokumen' => 'required|string|max:255',
            'pembayaan' => 'nullable|string|max:255',
            'ususan' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date|date_format:m/d/Y',
            'tanggal_selesai' => 'required|date|date_format:m/d/Y',
            'dokumen_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('dokumen_file')) {
            $filePath = $request->file('dokumen_file')->store('kerjasama', 'public');
        }

        // Create kerjasama
        $kerjasama = new Kerjasama();
        $kerjasama->id_mitra = $user->mitra->id_mitra;
        $kerjasama->id_kategori = $validated['jenis_kerjasama'];
        $kerjasama->nama_kerjasama = $validated['mitra_kerjasama'];
        $kerjasama->nomor_dokumen = $validated['nomor_dokumen'];
        $kerjasama->tgl_mulai = $validated['tanggal_mulai'];
        $kerjasama->tgl_selesai = $validated['tanggal_selesai'];
        $kerjasama->file_dokumen = $filePath;
        $kerjasama->status = 'pengajuan';
        $kerjasama->save();

        // Clear session
        session()->forget('pengajuan_step1');

        return redirect()->route('portal-mitra')->with('success', 'Pengajuan kerjasama berhasil dikirim!');
    }
}
