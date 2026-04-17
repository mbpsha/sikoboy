<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriKerjasamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['nama_kategori' => 'Kerjasama Daerah Antar Daerah (KSDD)'],
            ['nama_kategori' => 'Kerjasama Dengan Pihak Ketiga (KSDPK)'],
            ['nama_kategori' => 'Sinergi Dengan Pemerintah Pusat / Lembaga (NK/RK)'],
            ['nama_kategori' => 'Perjanjian Teknis (PERTEK)'],
            ['nama_kategori' => 'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)'],
            ['nama_kategori' => 'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)'],
        ];

        foreach ($kategoris as $kategori) {
            DB::table('kategori_kerjasama')->insertOrIgnore($kategori);
        }
    }
}
