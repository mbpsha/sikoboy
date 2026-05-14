<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\KategoriKerjasama;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\PeriodeKerjasama;
use App\Models\RiwayatStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class LocalKerjasamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // WARNING: this seeder will remove existing kerjasama-related data.
        // Run only on local/dev. Backup production data before running.

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('riwayat_status')->truncate();
        DB::table('periode_kerjasama')->truncate();
        DB::table('dokumen')->truncate();
        DB::table('kerjasama')->truncate();
        // keep mitras and users, but you may truncate mitras if desired
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Ensure at least one admin exists
        $admin = Admin::first();
        if (! $admin) {
            $admin = Admin::create([
                'id_user' => null,
                'nama' => 'Seeder Admin',
                'divisi' => 'Seeder',
            ]);
        }

        // Ensure at least one kategori exists
        $kategori = KategoriKerjasama::first();
        if (! $kategori) {
            $kategori = KategoriKerjasama::create([
                'nama_kategori' => 'Seeder Kategori',
                'deskripsi' => 'Dummy kategori dari seeder',
                'file_template' => null,
            ]);
        }

        // Create a few mitra records
        $mitraNames = [
            'PT Loss', 'PT Contoh Satu', 'CV Makmur', 'PT Mitra Sehat', 'PT DataSeed'
        ];

        $mitras = [];
        foreach ($mitraNames as $i => $name) {
            $mitras[] = Mitra::create([
                'id_user' => null,
                'nama_perusahaan' => $name,
                'no_handphone' => '0812345678' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT),
                'pic' => 'PIC ' . ($i + 1),
                'alamat' => 'Jl. Seeder No. ' . ($i + 1),
            ]);
        }

        // Create 10 kerjasama records distributed among mitras
        $now = Carbon::now();
        for ($i = 1; $i <= 10; $i++) {
            $mitra = $mitras[array_rand($mitras)];
            $judul = 'Kerjasama Sekunder #' . $i;
            $kData = [
                'id_mitra' => $mitra->id_mitra,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategori->id_kategori,
                'judul' => $judul,
            ];

            if (Schema::hasColumn('kerjasama', 'nomor_surat')) {
                $kData['nomor_surat'] = 'M-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
            } elseif (Schema::hasColumn('kerjasama', 'nomor_suratM')) {
                $kData['nomor_suratM'] = 'M-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
            }

            if (Schema::hasColumn('kerjasama', 'urusan')) {
                $kData['urusan'] = 'Kerjasama Pemerintah Daerah';
            }
            if (Schema::hasColumn('kerjasama', 'daerah')) {
                $kData['daerah'] = 'Daerah Seeder';
            }
            if (Schema::hasColumn('kerjasama', 'status_aktif')) {
                $kData['status_aktif'] = 'Aktif';
            }
            if (Schema::hasColumn('kerjasama', 'pembiayaan')) {
                $kData['pembiayaan'] = 'APBD';
            }
            if (Schema::hasColumn('kerjasama', 'pemrakarsa')) {
                $kData['pemrakarsa'] = 'M';
            }
            if (Schema::hasColumn('kerjasama', 'jenis_kerjasama')) {
                $kData['jenis_kerjasama'] = 'Kerjasama Daerah Antar Daerah (KSDD)';
            }
            if (Schema::hasColumn('kerjasama', 'jenis_dokumen')) {
                $kData['jenis_dokumen'] = $i % 2 === 0 ? 'MOU' : 'PKS';
            }
            if (Schema::hasColumn('kerjasama', 'tipe')) {
                $kData['tipe'] = 'mitra';
            }
            if (Schema::hasColumn('kerjasama', 'is_finalized')) {
                $kData['is_finalized'] = false;
            }
            if (Schema::hasColumn('kerjasama', 'status_negosiasi')) {
                $kData['status_negosiasi'] = null;
            }
            if (Schema::hasColumn('kerjasama', 'status_persetujuan')) {
                $kData['status_persetujuan'] = null;
            }

            $k = Kerjasama::create($kData);

            // Periode: start this year, end next year
            $start = $now->copy()->subMonths(rand(0, 12))->toDateString();
            $end = $now->copy()->addMonths(rand(6, 24))->toDateString();

            PeriodeKerjasama::create([
                'id_kerjasama' => $k->id_kerjasama,
                'tanggal_mulai' => $start,
                'tanggal_berakhir' => $end,
                'keterangan' => 'Periode awal seeder',
            ]);

            // Riwayat: record an initial proses entry
            RiwayatStatus::recordStatus(
                idKerjasama: $k->id_kerjasama,
                jenisStatus: 'proses',
                idAdmin: $admin->id_admin,
                catatan: 'Inisialisasi seeder: ' . $judul,
                penanggungJawab: $admin->divisi ?? $admin->nama
            );
        }

        $this->command->info('LocalKerjasamaSeeder: inserted 10 kerjasama rows and supporting data.');
    }
}
