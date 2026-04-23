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
        // Insert or update all .docx templates found in public/docs
        $files = glob(base_path('public/docs') . '/*.docx');
        foreach ($files as $f) {
            $base = basename($f);
            $storagePath = '/storage/docs/' . $base;

            // derive a readable category name from filename
            $nama = '';
            if (stripos($base, 'PKS') !== false) {
                // e.g. 'Template PKS KSDD.docx' => 'PERJANJIAN KERJA SAMA (PKS KSDD)'
                if (preg_match('/PKS\s*([A-Za-z0-9]+)/i', $base, $m)) {
                    $suf = $m[1];
                    $nama = 'PERJANJIAN KERJA SAMA (PKS ' . strtoupper($suf) . ')';
                } else {
                    $nama = 'PERJANJIAN KERJA SAMA';
                }
            } elseif (stripos($base, 'KESEPAKATAN BERSAMA') !== false) {
                // e.g. 'Template KESEPAKATAN BERSAMA KSDD.docx' => 'KESEPAKATAN BERSAMA (KSDD)'
                if (preg_match('/KESEPAKATAN BERSAMA\s*([A-Za-z0-9]+)/i', $base, $m)) {
                    $suf = $m[1];
                    $nama = 'KESEPAKATAN BERSAMA (' . strtoupper($suf) . ')';
                } else {
                    $nama = 'KESEPAKATAN BERSAMA';
                }
            } elseif (stripos($base, 'NOTA KESEPAKATAN') !== false) {
                $nama = 'NOTA KESEPAKATAN';
            } else {
                // fallback: filename without extension
                $nama = preg_replace('/\.[^.]+$/', '', $base);
            }

            $deskripsi = 'Template ' . $nama;

            DB::table('kategori_kerjasama')->updateOrInsert(
                ['file_template' => $storagePath],
                ['nama_kategori' => $nama, 'deskripsi' => $deskripsi]
            );
        }
    }
}
