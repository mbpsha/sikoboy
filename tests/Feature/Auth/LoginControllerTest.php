<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $adminUser = User::create([
            'email' => 'AdminSikoboy123@admin.com',
            'password' => Hash::make('sikoboybukansikejam'),
            'role' => 'admin',
        ]);

        Admin::create([
            'id_user' => $adminUser->id_user,
            'nama' => 'Admin SIKOBOY',
            'divisi' => 'Administrator',
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => 'adminsikoboy123@admin.com',
            'password' => 'sikoboybukansikejam',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($adminUser);
    }

    public function test_mitra_login_redirects_to_home(): void
    {
        $mitraUser = User::create([
            'email' => 'mitra@example.com',
            'password' => Hash::make('mitra-password'),
            'role' => 'mitra',
            'status_verifikasi' => 'disetujui',
        ]);

        Mitra::create([
            'id_user' => $mitraUser->id_user,
            'nama_perusahaan' => 'PT Mitra Test',
            'no_handphone' => '081234567890',
            'pic' => 'PIC Mitra',
            'alamat' => 'Boyolali',
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => 'mitra@example.com',
            'password' => 'mitra-password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($mitraUser);
    }

    public function test_unverified_mitra_cannot_login(): void
    {
        $mitraUser = User::create([
            'email' => 'pending@example.com',
            'password' => Hash::make('mitra-password'),
            'role' => 'mitra',
            'status_verifikasi' => 'pending',
        ]);

        Mitra::create([
            'id_user' => $mitraUser->id_user,
            'nama_perusahaan' => 'PT Pending',
            'no_handphone' => '081234567890',
            'pic' => 'PIC Pending',
            'alamat' => 'Boyolali',
        ]);

        $response = $this->from(route('login'))->post(route('login.attempt'), [
            'email' => 'pending@example.com',
            'password' => 'mitra-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
