<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Dokumen;
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
            'nama_kategori' => 'KSDPK',
            'deskripsi' => 'Kategori default',
            'file_template' => 'template.pdf',
        ]);
        $mitraUser = $this->createMitraUser();

        $response = $this->actingAs($mitraUser)->post(route('mitra.kerjasama.store'), [
            'jenis_kerjasama' => 'KSDPK',
            'jenis_dokumen' => 'PKS',
            'judul' => 'Pengajuan Kerjasama Tahun 2026',
            'nama_pihak_luar' => 'PT Mitra Kerjasama',
            'nomor_suratM' => 'M-001/2026',
            'pembiayaan' => 'APBN',
            'urusan' => 'KESEHATAN',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2027-05-01',
            'dokumen_file' => UploadedFile::fake()->create('pengajuan.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect(route('mitra.profile.index'));
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
            'keterangan' => 'APBN',
        ]);
        $this->assertDatabaseCount('dokumen', 1);

        $path = Dokumen::query()->firstOrFail()->lokasi_file;
        $this->assertTrue(Storage::disk('public')->exists($path));
    }

    public function test_mitra_can_submit_docx_kerjasama_pengajuan(): void
    {
        Storage::fake('public');

        $this->createAdminUser();
        KategoriKerjasama::create([
            'nama_kategori' => 'KSDPK',
            'deskripsi' => 'Kategori default',
            'file_template' => 'template.pdf',
        ]);
        $mitraUser = $this->createMitraUser();

        $response = $this->actingAs($mitraUser)->post(route('mitra.kerjasama.store'), [
            'jenis_kerjasama' => 'KSDPK',
            'jenis_dokumen' => 'PKS',
            'judul' => 'Pengajuan Kerjasama Tahun 2026',
            'nama_pihak_luar' => 'PT Mitra Kerjasama',
            'nomor_suratM' => 'M-001/2026',
            'pembiayaan' => 'APBN',
            'urusan' => 'KESEHATAN',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2027-05-01',
            'dokumen_file' => UploadedFile::fake()->create('pengajuan.docx', 120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

        $response->assertRedirect(route('mitra.profile.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('dokumen', 1);
    }

    public function test_mitra_can_upload_docx_revision_and_receive_success_json(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $mitraUser = $this->createMitraUser();
        $kategori = KategoriKerjasama::create([
            'nama_kategori' => 'KSDPK',
            'deskripsi' => 'Kategori default',
            'file_template' => 'template.pdf',
        ]);

        $kerjasama = \App\Models\Kerjasama::create([
            'id_mitra' => $mitraUser->mitra->id_mitra,
            'id_admin' => $admin->admin->id_admin,
            'id_kategori' => $kategori->id_kategori,
            'judul' => 'Test Kerjasama',
            'nomor_suratM' => 'M-123',
            'pemrakarsa' => 'M',
            'tipe' => 'mitra',
            'is_finalized' => false,
            'urusan' => 'KESEHATAN',
            'daerah' => '-',
            'status_aktif' => 'aktif',
            'jenis_kerjasama' => 'KSDPK',
            'jenis_dokumen' => 'PKS',
            'pembiayaan' => 'APBN',
            'nama_pihak_luar' => 'Pihak Luar',
        ]);

        $response = $this->actingAs($mitraUser)->postJson(route('mitra.kerjasama.revisi.upload', $kerjasama->id_kerjasama), [
            'file' => UploadedFile::fake()->create('revisi.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Pengajuan kerjasama berhasil',
        ]);
        $this->assertDatabaseCount('dokumen', 1);
    }

    public function test_mitra_upload_revision_fails_for_invalid_file_and_receives_failure_json(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $mitraUser = $this->createMitraUser();
        $kategori = KategoriKerjasama::create([
            'nama_kategori' => 'KSDPK',
            'deskripsi' => 'Kategori default',
            'file_template' => 'template.pdf',
        ]);

        $kerjasama = \App\Models\Kerjasama::create([
            'id_mitra' => $mitraUser->mitra->id_mitra,
            'id_admin' => $admin->admin->id_admin,
            'id_kategori' => $kategori->id_kategori,
            'judul' => 'Test Kerjasama',
            'nomor_suratM' => 'M-123',
            'pemrakarsa' => 'M',
            'tipe' => 'mitra',
            'is_finalized' => false,
            'urusan' => 'KESEHATAN',
            'daerah' => '-',
            'status_aktif' => 'aktif',
            'jenis_kerjasama' => 'KSDPK',
            'jenis_dokumen' => 'PKS',
            'pembiayaan' => 'APBN',
            'nama_pihak_luar' => 'Pihak Luar',
        ]);

        $response = $this->actingAs($mitraUser)->postJson(route('mitra.kerjasama.revisi.upload', $kerjasama->id_kerjasama), [
            'file' => UploadedFile::fake()->create('revisi.txt', 10, 'text/plain'),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Pengajuan kerjasama gagal',
        ]);
        $response->assertJsonStructure(['errors']);
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
