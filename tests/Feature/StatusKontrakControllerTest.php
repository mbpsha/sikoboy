<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\KategoriKerjasama;
use App\Models\Kerjasama;
use App\Models\Mitra;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StatusKontrakControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_persetujuan_records_riwayat_and_syncs_snapshot(): void
    {
        $adminUser = $this->createAdminUser();
        $kerjasama = $this->createMitraKerjasama($adminUser);

        $response = $this->actingAs($adminUser)->put(
            route('admin.status-kontrak.persetujuan', ['id' => $kerjasama->id_kerjasama]),
            [
                'status_persetujuan' => 'revisi',
                'catatan_persetujuan' => 'Perbaiki kesalahan pengetikan pada dokumen.',
            ]
        );

        $response->assertSessionHas('success');

        $kerjasama->refresh();
        $this->assertSame('revisi', $kerjasama->status_persetujuan?->value);
        $this->assertSame('Perbaiki kesalahan pengetikan pada dokumen.', $kerjasama->catatan_persetujuan);

        $this->assertDatabaseHas('status', ['jenis_status' => 'revisi']);
        $this->assertDatabaseHas('riwayat_status', [
            'id_kerjasama' => $kerjasama->id_kerjasama,
            'catatan' => 'Perbaiki kesalahan pengetikan pada dokumen.',
        ]);
        $this->assertTrue(
            RiwayatStatus::query()
                ->where('id_kerjasama', $kerjasama->id_kerjasama)
                ->whereHas('status', fn ($q) => $q->where('jenis_status', 'revisi'))
                ->exists()
        );
    }

    public function test_finalize_records_riwayat_and_marks_finalized(): void
    {
        $adminUser = $this->createAdminUser();
        $kerjasama = $this->createMitraKerjasama($adminUser);

        $response = $this->actingAs($adminUser)->put(
            route('admin.status-kontrak.finalize', ['id' => $kerjasama->id_kerjasama])
        );

        $response->assertSessionHas('success');

        $kerjasama->refresh();
        $this->assertTrue((bool) $kerjasama->is_finalized);
        $this->assertSame('disetujui', $kerjasama->status_persetujuan?->value);

        $this->assertTrue(
            RiwayatStatus::query()
                ->where('id_kerjasama', $kerjasama->id_kerjasama)
                ->whereHas('status', fn ($q) => $q->where('jenis_status', 'disetujui'))
                ->exists()
        );
    }

    private function createAdminUser(): User
    {
        $user = User::create([
            'email' => 'admin.status-kontrak@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Admin::create([
            'id_user' => $user->id_user,
            'nama' => 'Admin Status',
            'divisi' => 'Kerjasama',
        ]);

        return $user->fresh();
    }

    private function createMitraKerjasama(User $adminUser): Kerjasama
    {
        $mitraUser = User::create([
            'email' => 'mitra.status-kontrak@example.com',
            'password' => Hash::make('password'),
            'role' => 'mitra',
        ]);

        $mitra = Mitra::create([
            'id_user' => $mitraUser->id_user,
            'nama_perusahaan' => 'PT Mitra Status',
            'no_handphone' => '081222222222',
            'pic' => 'Budi',
            'alamat' => 'Boyolali',
        ]);

        $kategori = KategoriKerjasama::create([
            'nama_kategori' => 'Kesehatan',
            'deskripsi' => 'Kategori test',
            'file_template' => 'template.pdf',
        ]);

        return Kerjasama::create([
            'id_mitra' => $mitra->id_mitra,
            'id_admin' => $adminUser->admin->id_admin,
            'id_kategori' => $kategori->id_kategori,
            'judul' => 'Kerjasama Testing Status',
            'nomor_suratM' => 'M-TEST-001',
            'urusan' => 'Kesehatan',
            'daerah' => 'Boyolali',
            'status_aktif' => 'aktif',
            'pemrakarsa' => 'M',
            'tipe' => 'mitra',
            'jenis_kerjasama' => 'PKS',
            'jenis_dokumen' => 'PKS',
            'nama_pihak_luar' => 'PT Mitra Status',
            'is_finalized' => false,
            'status_negosiasi' => 'direview',
            'status_persetujuan' => null,
            'catatan_persetujuan' => null,
        ]);
    }
}
