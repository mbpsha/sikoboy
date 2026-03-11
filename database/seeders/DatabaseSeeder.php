<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Mitra;
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
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        Admin::create([
            'id_user' => $admin->id_user,
            'nip' => '198001012010011001',
            'nama_lengkap' => 'Administrator Utama',
            'jabatan' => 'Administrator',
            'bagian' => 'IT',
            'no_handphone' => '081234567890',
        ]);

        // Create Sample Partner/Mitra
        $mitra = User::create([
            'email' => 'mitra@example.com',
            'password' => Hash::make('Mitra@12345'),
            'role' => 'mitra',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        Mitra::create([
            'id_user' => $mitra->id_user,
            'nama_perusahaan' => 'PT Contoh Mitra Indonesia',
            'npwp' => '01.234.567.8-901.000',
            'pic' => 'Budi Santoso',
            'jabatan_pic' => 'Direktur',
            'no_handphone' => '081234567891',
            'no_telepon' => '0276123456',
            'alamat' => 'Jl. Contoh No. 123, Boyolali',
            'provinsi' => 'Jawa Tengah',
            'kabupaten_kota' => 'Boyolali',
            'kecamatan' => 'Boyolali',
            'kode_pos' => '57311',
            'bidang_usaha' => 'Teknologi Informasi',
            'website' => 'https://example.com',
        ]);

        // Create Additional Sample Partners
        $mitra2 = User::create([
            'email' => 'partner2@example.com',
            'password' => Hash::make('Partner@12345'),
            'role' => 'mitra',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        Mitra::create([
            'id_user' => $mitra2->id_user,
            'nama_perusahaan' => 'CV Mitra Sejahtera',
            'npwp' => '02.345.678.9-012.000',
            'pic' => 'Siti Nurhaliza',
            'jabatan_pic' => 'Manager',
            'no_handphone' => '081234567892',
            'alamat' => 'Jl. Merdeka No. 45, Boyolali',
            'provinsi' => 'Jawa Tengah',
            'kabupaten_kota' => 'Boyolali',
            'bidang_usaha' => 'Perdagangan',
        ]);

        // Create Unverified Partner
        $mitra3 = User::create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('Partner@12345'),
            'role' => 'mitra',
            'email_verified_at' => null,
            'is_active' => true,
        ]);

        Mitra::create([
            'id_user' => $mitra3->id_user,
            'nama_perusahaan' => 'PT Belum Verifikasi',
            'pic' => 'Ahmad Dahlan',
            'no_handphone' => '081234567893',
            'alamat' => 'Jl. Pemuda No. 78, Boyolali',
            'provinsi' => 'Jawa Tengah',
            'kabupaten_kota' => 'Boyolali',
        ]);
    }
}

