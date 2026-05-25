<?php

namespace Database\Seeders;

use App\Enums\UrusanEnum;
use App\Models\Admin;
use App\Models\Adendum;
use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\PeriodeKerjasama;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiwayatKerjasamaSeeder extends Seeder
{
    private const JENIS_KERJASAMA = [
        'Kerjasama Daerah Antar Daerah (KSDD)',
        'Kerjasama Dengan Pihak Ketiga (KSDPK)',
        'Sinergi Dengan Pemerintah Pusat/Lembaga (NK/RK)',
        'Perjanjian Teknis (PERTEK)',
        'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)',
        'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)',
    ];

    private const JENIS_DOKUMEN = [
        'KSB',
        'Nota Kesepakatan',
        'Perjanjian Teknis',
        'PKS',
        'Rencana Kerja',
        'MOU',
        'RKT',
        'LOI',
    ];

    private const PEMBIAYAAN = [
        'APBN',
        'APBD',
        'PIHAK KETIGA',
        'PARA PIHAK',
        'SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN',
    ];

    public function run(): void
    {
        $admin = Admin::query()->first();
        if ($admin === null) {
            throw new \RuntimeException('Seeder riwayat kerjasama membutuhkan data admin terlebih dahulu.');
        }

        $mitras = Mitra::query()->get();
        if ($mitras->isEmpty()) {
            throw new \RuntimeException('Seeder riwayat kerjasama membutuhkan data mitra terlebih dahulu.');
        }

        $kategoriIds = DB::table('kategori_kerjasama')
            ->pluck('id_kategori');
        if ($kategoriIds->isEmpty()) {
            throw new \RuntimeException('Seeder riwayat kerjasama membutuhkan data kategori kerjasama terlebih dahulu.');
        }

        $urusanList = UrusanEnum::cases();

        // =========================
        // DATA PEMERINTAH
        // =========================

        for ($i = 1; $i <= 15; $i++) {

            $mitra = $mitras->random();
            $jenisKerjasama = $this->randomJenisKerjasama();
            $jenisDokumen = $this->randomJenisDokumen();
            $pembiayaan = $this->randomPembiayaan();

            $kerjasama = Kerjasama::create([
                'id_mitra' => null,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategoriIds->random(),

                'judul' => "Kerjasama Pemerintah #{$i}",

                'nomor_suratP' => 'SR-P-' . random_int(100, 999),

                'urusan' => $urusanList[array_rand($urusanList)],
                'daerah' => 'Boyolali',

                'jenis_kerjasama' => $jenisKerjasama,
                'jenis_dokumen' => $jenisDokumen,

                'pembiayaan' => $pembiayaan,

                'tipe' => 'pemerintah',
                'pemrakarsa' => 'P',

                'nama_pihak_luar' => $mitra->nama_perusahaan,

                'status_aktif' => 'aktif',

                'is_finalized' => true,

                'status_negosiasi' => null,

                'status_persetujuan' => 'disetujui',

                'catatan_persetujuan' => null,
            ]);

            $this->createPeriode($kerjasama->id_kerjasama, $i, $pembiayaan);

            $this->createDokumen($kerjasama, $admin, $jenisDokumen);

            if ($i % 4 == 0) {
                $this->createAdendum($kerjasama, $admin);
            }
        }

        // =========================
        // DATA MITRA
        // =========================

        for ($i = 1; $i <= 15; $i++) {

            $mitra = $mitras->random();
            $jenisKerjasama = $this->randomJenisKerjasama();
            $jenisDokumen = $this->randomJenisDokumen();
            $pembiayaan = $this->randomPembiayaan();

            $kerjasama = Kerjasama::create([
                'id_mitra' => $mitra->id_mitra,

                'id_admin' => $admin->id_admin,

                'id_kategori' => $kategoriIds->random(),

                'judul' => "Kerjasama Mitra #{$i}",

                'nomor_suratM' => 'SR-M-' . random_int(100, 999),

                'urusan' => $urusanList[array_rand($urusanList)],
                'daerah' => 'Boyolali',

                'jenis_kerjasama' => $jenisKerjasama,
                'jenis_dokumen' => $jenisDokumen,

                'pembiayaan' => $pembiayaan,

                'tipe' => 'mitra',
                'pemrakarsa' => 'M',

                'nama_pihak_luar' => $mitra->nama_perusahaan,

                'status_aktif' => 'aktif',

                'is_finalized' => true,

                'status_negosiasi' => null,

                'status_persetujuan' => 'disetujui',

                'catatan_persetujuan' => null,
            ]);

            $this->createPeriode($kerjasama->id_kerjasama, $i + 20, $pembiayaan);

            $this->createDokumen($kerjasama, $admin, $jenisDokumen);

            if ($i % 3 == 0) {
                $this->createAdendum($kerjasama, $admin);
            }
        }
    }

    private function randomJenisKerjasama(): string
    {
        return self::JENIS_KERJASAMA[array_rand(self::JENIS_KERJASAMA)];
    }

    private function randomJenisDokumen(): string
    {
        return self::JENIS_DOKUMEN[array_rand(self::JENIS_DOKUMEN)];
    }

    private function randomPembiayaan(): string
    {
        return self::PEMBIAYAAN[array_rand(self::PEMBIAYAAN)];
    }

    // =========================================================
    // PERIODE
    // =========================================================

    private function createPeriode($idKerjasama, $index, string $pembiayaan)
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

            'keterangan' => $pembiayaan,
        ]);
    }

    // =========================================================
    // DOKUMEN
    // =========================================================

    private function createDokumen($kerjasama, $admin, string $jenisDokumen)
    {
        Dokumen::create([
            'id_kerjasama' => $kerjasama->id_kerjasama,

            'jenis_dokumen' => $jenisDokumen,

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
