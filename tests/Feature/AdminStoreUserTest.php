<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStoreUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_mitra_user_with_profile_fields(): void
    {
        $actor = $this->createActorAdmin();

        $response = $this->actingAs($actor)->post(route('admin.pengguna.store'), [
            'role' => 'mitra',
            'email' => 'mitra-new@example.com',
            'password' => 'password123',
            'nama_perusahaan' => 'PT Baru',
            'pic' => 'Budi Santoso',
            'no_handphone' => '081234567890',
            'alamat' => 'Jl. Contoh No. 1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user = User::query()->where('email', 'mitra-new@example.com')->firstOrFail();
        $this->assertSame('mitra', $user->role);
        $this->assertDatabaseHas('mitras', [
            'id_user' => $user->id_user,
            'nama_perusahaan' => 'PT Baru',
            'pic' => 'Budi Santoso',
            'no_handphone' => '081234567890',
            'alamat' => 'Jl. Contoh No. 1',
        ]);
    }

    public function test_admin_can_store_admin_user_with_nama_and_divisi(): void
    {
        $actor = $this->createActorAdmin();

        $response = $this->actingAs($actor)->post(route('admin.pengguna.store'), [
            'role' => 'admin',
            'email' => 'admin-new@example.com',
            'password' => 'password123',
            'username' => 'Admin Baru',
            'instansi' => 'Bagian Hukum',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user = User::query()->where('email', 'admin-new@example.com')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertDatabaseHas('admins', [
            'id_user' => $user->id_user,
            'nama' => 'Admin Baru',
            'divisi' => 'Bagian Hukum',
        ]);
    }

    public function test_mitra_store_requires_mitra_fields_only(): void
    {
        $actor = $this->createActorAdmin();

        $response = $this->actingAs($actor)->post(route('admin.pengguna.store'), [
            'role' => 'mitra',
            'email' => 'incomplete@example.com',
            'password' => 'password123',
            'nama_perusahaan' => 'PT Tanpa PIC',
        ]);

        $response->assertSessionHasErrors(['pic', 'no_handphone', 'alamat']);
    }

    private function createActorAdmin(): User
    {
        $user = User::create([
            'email' => 'actor-admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Actor',
            'divisi' => 'IT',
        ]);

        return $user->fresh();
    }
}
