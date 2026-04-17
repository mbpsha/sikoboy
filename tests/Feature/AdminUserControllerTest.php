<?php

namespace Tests\Feature;

use App\Models\Admin;
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

    private function createAdminUser(string $email): User
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Admin',
            'divisi' => 'Divisi',
        ]);

        return $user->fresh();
    }
}
