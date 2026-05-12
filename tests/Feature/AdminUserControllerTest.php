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

        $response = $this->actingAs($actor)->put(route('admin.pengguna.update', $target->id_user), [
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

        $otherMitraUser = User::create([
            'email' => 'pending-mitra-2@example.com',
            'password' => Hash::make('password'),
            'role' => 'mitra',
            'status_verifikasi' => 'pending',
        ]);

        $response = $this->actingAs($actor)->put(route('admin.pengguna.verify', $mitraUser->id_user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $mitraUser->id_user,
            'status_verifikasi' => 'disetujui',
        ]);

        $this->assertDatabaseHas('users', [
            'id_user' => $otherMitraUser->id_user,
            'status_verifikasi' => 'pending',
        ]);
    }

    public function test_admin_can_toggle_active_status_for_mitra_user(): void
    {
        $actor = $this->createAdminUser('actor@example.com');
        $mitraUser = $this->createMitraUser('mitra@example.com');

        $response = $this->actingAs($actor)->put(route('admin.pengguna.status', $mitraUser->id_user), [
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $mitraUser->id_user,
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_toggle_active_status_for_admin_user(): void
    {
        $actor = $this->createAdminUser('actor@example.com');
        $target = $this->createAdminUser('target@example.com');

        $response = $this->actingAs($actor)->put(route('admin.pengguna.status', $target->id_user), [
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id_user' => $target->id_user,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_terminate_mitra_user(): void
    {
        $actor = $this->createAdminUser('actor@example.com');
        $mitraUser = $this->createMitraUser('mitra@example.com');

        $response = $this->actingAs($actor)->delete(route('admin.pengguna.terminate', $mitraUser->id_user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id_user' => $mitraUser->id_user,
        ]);

        $this->assertDatabaseMissing('mitras', [
            'id_user' => $mitraUser->id_user,
        ]);
    }

    public function test_admin_cannot_terminate_admin_user(): void
    {
        $actor = $this->createAdminUser('actor@example.com');
        $target = $this->createAdminUser('target@example.com');

        $response = $this->actingAs($actor)->delete(route('admin.pengguna.terminate', $target->id_user));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id_user' => $target->id_user,
            'email' => 'target@example.com',
        ]);
    }

    private function createAdminUser(string $email): User
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
            'is_active' => true,
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Admin',
            'divisi' => 'Divisi',
        ]);

        return $user->fresh();
    }

    private function createMitraUser(string $email): User
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'mitra',
            'status_verifikasi' => 'disetujui',
            'is_active' => true,
        ]);

        Mitra::create([
            'id_user' => $user->id_user,
            'nama_perusahaan' => 'PT Contoh Mitra',
            'no_handphone' => '081234567890',
            'pic' => 'PIC Mitra',
            'alamat' => 'Boyolali',
        ]);

        return $user->fresh();
    }
}
