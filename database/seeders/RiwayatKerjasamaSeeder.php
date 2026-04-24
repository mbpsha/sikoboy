<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use App\Models\Admin;
use App\Models\Mitra;
use Carbon\Carbon;

class RiwayatKerjasamaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();
        $mitras = Mitra::all();

        // ambil kategori
        $kategori = DB::table('kategori_kerjasama')
            ->orderBy('id_kategori')
            ->pluck('id_kategori');

        if ($kategori->isEmpty()) {
            throw new \Exception('Kategori kosong!');
        }

        // =========================
        // PEMERINTAH
        // =========================
        for ($i = 1; $i <= 10; $i++) {

            $mitra = $mitras->random();

            $kerjasama = Kerjasama::create([
                'id_mitra' => null, // sesuai controller
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategori->random(),

                'judul' => "Kerjasama Pemerintah #$i",

                'nomor_suratP' => 'SR-P-' . rand(100,999),
                'urusan' => 'Kerjasama Daerah',
                'daerah' => 'Boyolali',

                'jenis_kerjasama' => 'MoU',
                'jenis_dokumen' => 'PDF',

                'pemrakarsa' => 'P',
                'tipe' => 'pemerintah',

                'nama_pihak_luar' => $mitra->nama_perusahaan,

                'status_aktif' => 'aktif',
                'is_finalized' => true,
                'status_persetujuan' => 'disetujui',
            ]);

            $this->createPeriode($kerjasama->id_kerjasama, $i);
        }

        // =========================
        // MITRA
        // =========================
        for ($i = 1; $i <= 10; $i++) {

            $mitra = $mitras->random();

            $kerjasama = Kerjasama::create([
                'id_mitra' => $mitra->id_mitra,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategori->random(),

                'judul' => "Kerjasama Mitra #$i",

                'nomor_suratM' => 'SR-M-' . rand(100,999),
                'urusan' => 'Kemitraan',
                'daerah' => 'Boyolali',

                'jenis_kerjasama' => 'PKS',
                'jenis_dokumen' => 'PDF',

                'pemrakarsa' => 'M',
                'tipe' => 'mitra',

                'nama_pihak_luar' => $mitra->nama_perusahaan,

                'status_aktif' => 'aktif',
                'is_finalized' => true,
                'status_persetujuan' => 'disetujui',
            ]);

            $this->createPeriode($kerjasama->id_kerjasama, $i + 10);
        }
    }

    private function createPeriode($idKerjasama, $index)
    {
        $today = Carbon::today();

        // Variasi status
        if ($index % 3 === 0) {
            $mulai = $today->copy()->subYears(2);
            $berakhir = $today->copy()->subDays(5);
        } elseif ($index % 3 === 1) {
            $mulai = $today->copy()->subYears(1);
            $berakhir = $today->copy()->addDays(10);
        } else {
            $mulai = $today->copy()->subMonths(6);
            $berakhir = $today->copy()->addYears(1);
        }

        PeriodeKerjasama::create([
            'id_kerjasama' => $idKerjasama,
            'tanggal_mulai' => $mulai,
            'tanggal_berakhir' => $berakhir,
            'keterangan' => 'cooperation_docs/dummy.pdf',
        ]);
    }
}