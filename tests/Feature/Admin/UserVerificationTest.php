<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test verifying a mitra generates id_mitra sequentially.
     */
    public function test_verify_mitra_generates_id_mitra(): void
    {
        // Create admin user
        $adminUser = User::create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
            'is_active' => true,
        ]);
        Admin::create([
            'id_user' => $adminUser->id_user,
            'nama' => 'Test Admin',
            'divisi' => 'IT',
        ]);

        // Create first mitra (pending)
        $mitra1 = User::create([
            'email' => 'mitra1@test.com',
            'password' => bcrypt('password'),
            'role' => 'mitra',
            'status_verifikasi' => 'pending',
            'is_active' => true,
        ]);
        Mitra::create([
            'id_user' => $mitra1->id_user,
            'nama_perusahaan' => 'Perusahaan 1',
            'pic' => 'PIC 1',
            'no_handphone' => '081234567890',
            'alamat' => 'Alamat 1',
        ]);

        // Create second mitra (pending)
        $mitra2 = User::create([
            'email' => 'mitra2@test.com',
            'password' => bcrypt('password'),
            'role' => 'mitra',
            'status_verifikasi' => 'pending',
            'is_active' => true,
        ]);
        Mitra::create([
            'id_user' => $mitra2->id_user,
            'nama_perusahaan' => 'Perusahaan 2',
            'pic' => 'PIC 2',
            'no_handphone' => '081234567891',
            'alamat' => 'Alamat 2',
        ]);

        // Login as admin
        $this->actingAs($adminUser);

        // Verify first mitra
        $response = $this->put(route('admin.pengguna.verify', $mitra1->id_user));
        $response->assertRedirect();

        // Check first mitra has id_mitra = 1
        $mitra1->refresh();
        $this->assertEquals(1, $mitra1->mitra->id_mitra);
        $this->assertEquals('disetujui', $mitra1->status_verifikasi);

        // Verify second mitra
        $response = $this->put(route('admin.pengguna.verify', $mitra2->id_user));
        $response->assertRedirect();

        // Check second mitra has id_mitra = 2
        $mitra2->refresh();
        $this->assertEquals(2, $mitra2->mitra->id_mitra);
        $this->assertEquals('disetujui', $mitra2->status_verifikasi);
    }

    /**
     * Test verifying already verified mitra doesn't change id_mitra.
     */
    public function test_verify_already_verified_mitra_keeps_id_mitra(): void
    {
        // Create admin user
        $adminUser = User::create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
            'is_active' => true,
        ]);
        Admin::create([
            'id_user' => $adminUser->id_user,
            'nama' => 'Test Admin',
            'divisi' => 'IT',
        ]);

        // Create mitra with existing id_mitra
        $mitra = User::create([
            'email' => 'mitra@test.com',
            'password' => bcrypt('password'),
            'role' => 'mitra',
            'status_verifikasi' => 'disetujui',
            'is_active' => true,
        ]);
        Mitra::create([
            'id_user' => $mitra->id_user,
            'id_mitra' => 5,
            'nama_perusahaan' => 'Perusahaan Test',
            'pic' => 'PIC Test',
            'no_handphone' => '081234567890',
            'alamat' => 'Alamat Test',
        ]);

        // Login as admin
        $this->actingAs($adminUser);

        // Try to verify already verified mitra
        $response = $this->put(route('admin.pengguna.verify', $mitra->id_user));
        $response->assertSessionHas('success', 'Akun mitra sudah terverifikasi.');

        // Check id_mitra remains unchanged
        $mitra->refresh();
        $this->assertEquals(5, $mitra->mitra->id_mitra);
    }

    /**
     * Test creating mitra via admin with status disetujui generates id_mitra.
     */
    public function test_create_mitra_via_admin_with_status_disetujui_generates_id_mitra(): void
    {
        // Create admin user
        $adminUser = User::create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
            'is_active' => true,
        ]);
        Admin::create([
            'id_user' => $adminUser->id_user,
            'nama' => 'Test Admin',
            'divisi' => 'IT',
        ]);

        // Login as admin
        $this->actingAs($adminUser);

        // Create mitra via admin store endpoint
        $response = $this->post(route('admin.pengguna.store'), [
            'role' => 'mitra',
            'email' => 'mitra@test.com',
            'password' => 'password123',
            'nama_perusahaan' => 'Test Company',
            'pic' => 'John Doe',
            'no_handphone' => '081234567890',
            'alamat' => 'Test Address',
        ]);

        // Get created mitra
        $user = User::where('email', 'mitra@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('mitra', $user->role);
        $this->assertEquals('disetujui', $user->status_verifikasi);

        // Check id_mitra is generated
        $this->assertNotNull($user->mitra->id_mitra);
        $this->assertEquals(1, $user->mitra->id_mitra);
    }
}
