<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use App\Models\Dokumen;
use App\Models\Adendum;
use App\Models\Admin;
use App\Models\Mitra;
use Carbon\Carbon;

class RiwayatKerjasamaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        $mitras = Mitra::all();

        $kategoriIds = DB::table('kategori_kerjasama')
            ->pluck('id_kategori');

        $jenisKerjasamaList = [
            'Kerjasama Daerah Antar Daerah (KSDD)',
            'Kerjasama Dengan Pihak Ketiga (KSDPK)',
            'Sinergi Dengan Pemerintah Pusat/Lembaga (NK/RK)',
            'Perjanjian Teknis (PERTEK)',
            'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)',
            'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)',
        ];

        $jenisDokumenList = [
            'KSB',
            'Nota Kesepakatan',
            'Perjanjian Teknis',
            'PKS',
            'Rencana Kerja',
            'MOU',
            'RKT',
            'LOI',
        ];

        // =========================
        // DATA PEMERINTAH
        // =========================

        for ($i = 1; $i <= 15; $i++) {

            $mitra = $mitras->random();

            $kerjasama = Kerjasama::create([
                'id_mitra' => null,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategoriIds->random(),

                'judul' => "Kerjasama Pemerintah #{$i}",

                'nomor_suratP' => 'SR-P-' . rand(100, 999),

                'urusan' => 'Kerjasama Pemerintah Daerah',
                'daerah' => 'Boyolali',

                'jenis_kerjasama' => $jenisKerjasamaList[array_rand($jenisKerjasamaList)],
                'jenis_dokumen' => $jenisDokumenList[array_rand($jenisDokumenList)],

                'tipe' => 'pemerintah',
                'pemrakarsa' => 'P',

                'nama_pihak_luar' => $mitra->nama_perusahaan,

                'status_aktif' => 'aktif',

                'is_finalized' => true,

                'status_negosiasi' => null,

                'status_persetujuan' => 'disetujui',

                'catatan_persetujuan' => null,
            ]);

            $periode = $this->createPeriode($kerjasama->id_kerjasama, $i);

            $this->createDokumen($kerjasama, $admin);

            if ($i % 4 == 0) {
                $this->createAdendum($kerjasama, $admin);
            }
        }

        // =========================
        // DATA MITRA
        // =========================

        for ($i = 1; $i <= 15; $i++) {

            $mitra = $mitras->random();

            $kerjasama = Kerjasama::create([
                'id_mitra' => $mitra->id_mitra,

                'id_admin' => $admin->id_admin,

                'id_kategori' => $kategoriIds->random(),

                'judul' => "Kerjasama Mitra #{$i}",

                'nomor_suratM' => 'SR-M-' . rand(100, 999),

                'urusan' => 'Kemitraan Daerah',
                'daerah' => 'Boyolali',

                'jenis_kerjasama' => $jenisKerjasamaList[array_rand($jenisKerjasamaList)],
                'jenis_dokumen' => $jenisDokumenList[array_rand($jenisDokumenList)],

                'tipe' => 'mitra',
                'pemrakarsa' => 'M',

                'nama_pihak_luar' => $mitra->nama_perusahaan,

                'status_aktif' => 'aktif',

                'is_finalized' => true,

                'status_negosiasi' => null,

                'status_persetujuan' => 'disetujui',

                'catatan_persetujuan' => null,
            ]);

            $periode = $this->createPeriode($kerjasama->id_kerjasama, $i + 20);

            $this->createDokumen($kerjasama, $admin);

            if ($i % 3 == 0) {
                $this->createAdendum($kerjasama, $admin);
            }
        }
    }

    // =========================================================
    // PERIODE
    // =========================================================

    private function createPeriode($idKerjasama, $index)
    {
        // BERAKHIR
        if ($index % 3 == 0) {

            $mulai = Carbon::now()->subYears(2);

            $berakhir = Carbon::now()->subDays(10);
        }

        // SEGERA BERAKHIR
        elseif ($index % 3 == 1) {

            $mulai = Carbon::now()->subMonths(8);

            $berakhir = Carbon::now()->addDays(20);
        }

        // AKTIF
        else {

            $mulai = Carbon::now()->subMonths(2);

            $berakhir = Carbon::now()->addYears(1);
        }

        return PeriodeKerjasama::create([
            'id_kerjasama' => $idKerjasama,

            'tanggal_mulai' => $mulai,

            'tanggal_berakhir' => $berakhir,

            'keterangan' => 'cooperation_docs/dummy.pdf',
        ]);
    }

    // =========================================================
    // DOKUMEN
    // =========================================================

    private function createDokumen($kerjasama, $admin)
    {
        Dokumen::create([
            'id_kerjasama' => $kerjasama->id_kerjasama,

            'nama_file' => 'dummy.pdf',

            'lokasi_file' => 'cooperation_docs/dummy.pdf',

            'versi_dokumen' => 1,

            'created_by' => $admin->id_user,
        ]);
    }

    // =========================================================
    // ADENDUM
    // =========================================================

    private function createAdendum($kerjasama, $admin)
    {
        Adendum::create([
            'id_kerjasama' => $kerjasama->id_kerjasama,

            'judul_adendum' => 'Adendum Kerjasama',

            'keterangan_adendum' => 'Perubahan isi kerjasama',

            'nama_file' => 'adendum.pdf',

            'lokasi_file' => 'adendum_docs/adendum.pdf',

            'created_by' => $admin->id_user,
        ]);
    }
}
