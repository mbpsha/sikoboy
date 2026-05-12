<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::updateOrCreate(
            ['jenis_status' => 'Aktif'],
            ['jenis_status' => 'Aktif']
        );

        Status::updateOrCreate(
            ['jenis_status' => 'Segera Berakhir'],
            ['jenis_status' => 'Segera Berakhir']
        );

        Status::updateOrCreate(
            ['jenis_status' => 'Berakhir'],
            ['jenis_status' => 'Berakhir']
        );
    }
}
