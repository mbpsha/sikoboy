<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriKerjasamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            [
                'nama_kategori' => 'KSDD',
                'deskripsi' => 'Kerjasama daerah antar daerah',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'KSDPK',
                'deskripsi' => 'Kerjasama dengan pihak ketiga',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'NK/RK',
                'deskripsi' => 'Sinergi dengan pemerintah pusat atau lembaga',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'PERTEK',
                'deskripsi' => 'Perjanjian teknis',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'KSDPL',
                'deskripsi' => 'Kerjasama daerah dengan pemerintah daerah di luar negeri',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'KSDLL',
                'deskripsi' => 'Kerjasama daerah dengan lembaga di luar negeri',
                'file_template' => '-',
            ],
        ];

        foreach ($kategori as $item) {
            DB::table('kategori_kerjasama')->updateOrInsert(
                ['nama_kategori' => $item['nama_kategori']],
                $item
            );
        }
    }
}
