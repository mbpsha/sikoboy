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
        // Create Admin User
        $admin = User::create([
            'email' => 'admin@sikoboy.go.id',
            'password' => Hash::make('Admin@12345'),
            'role' => 'admin',
        ]);

        Admin::create([
            'id_user' => $admin->id_user,
            'nama' => 'Administrator Utama',
            'divisi' => 'IT',
        ]);

        // Create Sample Partner/Mitra
        $mitra = User::create([
            'email' => 'mitra@example.com',
            'password' => Hash::make('Mitra@12345'),
            'role' => 'mitra',
        ]);

        Mitra::create([
            'id_user' => $mitra->id_user,
            'nama_perusahaan' => 'PT Contoh Mitra Indonesia',
            'no_handphone' => '081234567891',
            'pic' => 'Budi Santoso',
            'alamat' => 'Jl. Contoh No. 123, Boyolali',
        ]);

        // Create Additional Sample Partners
        $mitra2 = User::create([
            'email' => 'partner2@example.com',
            'password' => Hash::make('Partner@12345'),
            'role' => 'mitra',
        ]);

        Mitra::create([
            'id_user' => $mitra2->id_user,
            'nama_perusahaan' => 'CV Mitra Sejahtera',
            'no_handphone' => '081234567892',
            'pic' => 'Siti Nurhaliza',
            'alamat' => 'Jl. Merdeka No. 45, Boyolali',
        ]);

        // Create Unverified Partner
        $mitra3 = User::create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('Partner@12345'),
            'role' => 'mitra',
        ]);

        Mitra::create([
            'id_user' => $mitra3->id_user,
            'nama_perusahaan' => 'PT Belum Verifikasi',
            'no_handphone' => '081234567893',
            'pic' => 'Ahmad Dahlan',
            'alamat' => 'Jl. Pemuda No. 78, Boyolali',
        ]);

        // Seed kategori kerjasama templates
        $this->call(KategoriKerjasamaSeeder::class);
    }
}
