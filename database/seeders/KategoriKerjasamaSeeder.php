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
                'nama_kategori' => 'Kerjasama Daerah Antar Daerah (KSDD)',
                'deskripsi' => 'Kerjasama daerah antar daerah',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'Kerjasama Dengan Pihak Ketiga (KSDPK)',
                'deskripsi' => 'Kerjasama dengan pihak ketiga',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'Sinergi Dengan Pemerintah Pusat / Lembaga (NK/RK)',
                'deskripsi' => 'Sinergi dengan pemerintah pusat atau lembaga',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'Perjanjian Teknis (PERTEK)',
                'deskripsi' => 'Perjanjian teknis',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)',
                'deskripsi' => 'Kerjasama daerah dengan pemerintah daerah di luar negeri',
                'file_template' => '-',
            ],
            [
                'nama_kategori' => 'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)',
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
