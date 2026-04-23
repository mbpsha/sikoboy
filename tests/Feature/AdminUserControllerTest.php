<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_admin_user_identity_fields(): void
    {
        $actor = $this->createAdminUser('actor@example.com');
        $target = $this->createAdminUser('target@example.com');

        $response = $this->actingAs($actor)->put(route('admin.users.update', $target->id_user), [
            'email' => 'target-updated@example.com',
            'username' => 'Nama Baru',
            'instansi' => 'Divisi Baru',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $target->id_user,
            'email' => 'target-updated@example.com',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('admins', [
            'id_user' => $target->id_user,
            'nama' => 'Nama Baru',
            'divisi' => 'Divisi Baru',
        ]);
    }

    public function test_admin_can_verify_pending_mitra_user(): void
    {
        $actor = $this->createAdminUser('actor@example.com');

        $mitraUser = User::create([
            'email' => 'pending-mitra@example.com',
            'password' => Hash::make('password'),
            'role' => 'mitra',
            'status_verifikasi' => 'pending',
        ]);

        Mitra::create([
            'id_user' => $mitraUser->id_user,
            'nama_perusahaan' => 'PT Pending Mitra',
            'no_handphone' => '081234567890',
            'pic' => 'PIC Pending',
            'alamat' => 'Boyolali',
        ]);

        $response = $this->actingAs($actor)->put(route('admin.users.verify', $mitraUser->id_user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $mitraUser->id_user,
            'status_verifikasi' => 'disetujui',
        ]);
    }

    private function createAdminUser(string $email): User
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Admin',
            'divisi' => 'Divisi',
        ]);

        return $user->fresh();
    }
}
