<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\TemplateDokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManajemenDokumenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_pdf_template_to_private_storage(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->post(route('admin.manajemen-dokumen.store'), [
            'template_file' => UploadedFile::fake()->create('template-kerjasama.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $template = TemplateDokumen::query()->firstOrFail();
        $this->assertTrue(Storage::disk('local')->exists($template->lokasi_file));
        $this->assertFalse(Storage::disk('public')->exists($template->lokasi_file));
    }

    public function test_upload_rejects_non_pdf_template(): void
    {
        Storage::fake('local');
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->post(route('admin.manajemen-dokumen.store'), [
            'template_file' => UploadedFile::fake()->create('template.txt', 10, 'text/plain'),
        ]);

        $response->assertSessionHasErrors('template_file');
        $this->assertDatabaseCount('template_dokumen', 0);
    }

    public function test_public_download_works_for_template_document(): void
    {
        Storage::fake('local');
        $adminUser = $this->createAdminUser();
        $templatePath = Storage::disk('local')->putFileAs('template-dokumen', UploadedFile::fake()->create('dokumen.pdf', 50, 'application/pdf'), 'dokumen.pdf');

        $template = TemplateDokumen::create([
            'id_admin' => $adminUser->admin->id_admin,
            'nama_file' => 'dokumen.pdf',
            'lokasi_file' => $templatePath,
        ]);

        $response = $this->get(route('template-dokumen.download', $template->id_template_dokumen));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_public_preview_works_for_template_document(): void
    {
        Storage::fake('local');
        $adminUser = $this->createAdminUser();
        $templatePath = Storage::disk('local')->putFileAs('template-dokumen', UploadedFile::fake()->create('preview.pdf', 50, 'application/pdf'), 'preview.pdf');

        $template = TemplateDokumen::create([
            'id_admin' => $adminUser->admin->id_admin,
            'nama_file' => 'preview.pdf',
            'lokasi_file' => $templatePath,
        ]);

        $response = $this->get(route('template-dokumen.preview', $template->id_template_dokumen));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_admin_can_delete_template_and_file(): void
    {
        Storage::fake('local');
        $adminUser = $this->createAdminUser();
        $templatePath = Storage::disk('local')->putFileAs('template-dokumen', UploadedFile::fake()->create('hapus.pdf', 50, 'application/pdf'), 'hapus.pdf');

        $template = TemplateDokumen::create([
            'id_admin' => $adminUser->admin->id_admin,
            'nama_file' => 'hapus.pdf',
            'lokasi_file' => $templatePath,
        ]);

        $response = $this->actingAs($adminUser)->delete(route('admin.manajemen-dokumen.destroy', $template->id_template_dokumen));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('template_dokumen', ['id_template_dokumen' => $template->id_template_dokumen]);
        $this->assertFalse(Storage::disk('local')->exists($templatePath));
    }

    private function createAdminUser(): User
    {
        $user = User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Admin Boyolali',
            'divisi' => 'Bagian Kerjasama',
        ]);

        return $user->fresh();
    }
}
