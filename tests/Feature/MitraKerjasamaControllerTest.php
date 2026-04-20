<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\KategoriKerjasama;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MitraKerjasamaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_can_submit_kerjasama_pengajuan(): void
    {
        Storage::fake('public');

        $this->createAdminUser();
        KategoriKerjasama::create([
            'nama_kategori' => 'Kesehatan',
            'deskripsi' => 'Kategori default',
            'file_template' => 'template.pdf',
        ]);
        $mitraUser = $this->createMitraUser();

        $response = $this->actingAs($mitraUser)->post(route('mitra.kerjasama.store'), [
            'jenis_kerjasama' => 'Kerjasama Daerah',
            'jenis_dokumen' => 'PKS',
            'judul' => 'Pengajuan Kerjasama Tahun 2026',
            'nama_pihak_luar' => 'PT Mitra Kerjasama',
            'nomor_suratM' => 'M-001/2026',
            'pembiayaan' => 'Mandiri',
            'urusan' => 'Kesehatan',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2027-05-01',
            'dokumen_file' => UploadedFile::fake()->create('pengajuan.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect(route('mitra.kerjasama.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kerjasama', [
            'id_mitra' => $mitraUser->mitra->id_mitra,
            'judul' => 'Pengajuan Kerjasama Tahun 2026',
            'pemrakarsa' => 'M',
            'status_persetujuan' => null,
            'is_finalized' => false,
        ]);
        $this->assertDatabaseHas('periode_kerjasama', [
            'tanggal_mulai' => '2026-05-01',
            'tanggal_berakhir' => '2027-05-01',
        ]);
        $this->assertDatabaseCount('dokumen', 1);

        $path = \App\Models\Dokumen::query()->firstOrFail()->lokasi_file;
        $this->assertTrue(Storage::disk('public')->exists($path));
    }

    private function createAdminUser(): User
    {
        $user = User::create([
            'email' => 'admin.mitra-test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Admin Mitra',
            'divisi' => 'Kerjasama',
        ]);

        return $user->fresh();
    }

    private function createMitraUser(): User
    {
        $user = User::create([
            'email' => 'mitra.controller@example.com',
            'password' => Hash::make('password'),
            'role' => 'mitra',
        ]);

        Mitra::create([
            'id_user' => $user->id_user,
            'nama_perusahaan' => 'PT Mitra Controller',
            'no_handphone' => '081111111111',
            'pic' => 'Andi',
            'alamat' => 'Boyolali',
        ]);

        return $user->fresh(['mitra']);
    }
}
