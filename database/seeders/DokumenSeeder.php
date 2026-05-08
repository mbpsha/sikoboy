<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Kerjasama;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = Admin::query()->orderBy('id_admin')->value('id_admin');

        if ($adminId === null) {
            return;
        }

        $kerjasamaList = Kerjasama::query()->orderBy('id_kerjasama')->get();

        if ($kerjasamaList->isEmpty()) {
            return;
        }

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

        $rows = [];

        foreach ($kerjasamaList as $index => $kerjasama) {
            $jenis = $jenisDokumen[$index % count($jenisDokumen)];
            $filename = 'dokumen_' . $kerjasama->id_kerjasama . '.pdf';

            $rows[] = [
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'jenis_dokumen' => $jenis,
                'nama_file' => $filename,
                'lokasi_file' => 'cooperation_docs/' . $filename,
                'versi_dokumen' => 1,
                'created_by' => $adminId,
                'created_at' => now(),
            ];
        }

        DB::table('dokumen')->insert($rows);
    }
}
