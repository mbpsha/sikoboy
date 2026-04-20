<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin login requested by team.
        $admin = User::updateOrCreate(
            ['email' => 'AdminSikoboy123@admin.com'],
            [
                'password' => Hash::make('sikoboybukansikejam'),
                'role' => 'admin',
            ]
        );

        Admin::updateOrCreate(
            ['id_user' => $admin->id_user],
            [
                'nama' => 'Admin SIKOBOY',
                'divisi' => 'Administrator',
            ]
        );

        // Create Sample Partner/Mitra
        $mitra = User::updateOrCreate([
            'email' => 'mitra@example.com',
        ], [
            'password' => Hash::make('Mitra@12345'),
            'role' => 'mitra',
        ]);

        Mitra::updateOrCreate([
            'id_user' => $mitra->id_user,
        ], [
            'nama_perusahaan' => 'PT Contoh Mitra Indonesia',
            'no_handphone' => '081234567891',
            'pic' => 'Budi Santoso',
            'alamat' => 'Jl. Contoh No. 123, Boyolali',
        ]);

        // Create Additional Sample Partners
        $mitra2 = User::updateOrCreate([
            'email' => 'partner2@example.com',
        ], [
            'password' => Hash::make('Partner@12345'),
            'role' => 'mitra',
        ]);

        Mitra::updateOrCreate([
            'id_user' => $mitra2->id_user,
        ], [
            'nama_perusahaan' => 'CV Mitra Sejahtera',
            'no_handphone' => '081234567892',
            'pic' => 'Siti Nurhaliza',
            'alamat' => 'Jl. Merdeka No. 45, Boyolali',
        ]);

        // Create Unverified Partner
        $mitra3 = User::updateOrCreate([
            'email' => 'unverified@example.com',
        ], [
            'password' => Hash::make('Partner@12345'),
            'role' => 'mitra',
        ]);

        Mitra::updateOrCreate([
            'id_user' => $mitra3->id_user,
        ], [
            'nama_perusahaan' => 'PT Belum Verifikasi',
            'no_handphone' => '081234567893',
            'pic' => 'Ahmad Dahlan',
            'alamat' => 'Jl. Pemuda No. 78, Boyolali',
        ]);
    }
}
