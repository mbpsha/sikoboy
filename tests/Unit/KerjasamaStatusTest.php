<?php

namespace Tests\Unit;

use App\Enums\StatusPersetujuan;
use App\Models\Kerjasama;
use App\Models\PeriodeKerjasama;
use Tests\TestCase;

class KerjasamaStatusTest extends TestCase
{
    public function test_status_label_is_null_for_unapproved_mitra_kerjasama(): void
    {
        $kerjasama = new Kerjasama([
            'pemrakarsa' => 'M',
            'status_persetujuan' => null,
        ]);

        $kerjasama->setRelation('latestPeriode', new PeriodeKerjasama([
            'tanggal_mulai' => now()->subMonth()->toDateString(),
            'tanggal_berakhir' => now()->addMonth()->toDateString(),
        ]));

        $this->assertNull($kerjasama->status_label);
    }

    public function test_status_label_is_segera_berakhir_when_end_date_is_within_three_months(): void
    {
        $kerjasama = new Kerjasama([
            'pemrakarsa' => 'P',
            'status_persetujuan' => StatusPersetujuan::Disetujui->value,
        ]);

        $kerjasama->setRelation('latestPeriode', new PeriodeKerjasama([
            'tanggal_mulai' => now()->subMonth()->toDateString(),
            'tanggal_berakhir' => now()->addMonths(2)->toDateString(),
        ]));

        $this->assertSame('segera berakhir', $kerjasama->status_label);
    }

    public function test_nomor_surat_accessor_reads_value_based_on_pemrakarsa(): void
    {
        $mitraKerjasama = new Kerjasama([
            'pemrakarsa' => 'M',
            'nomor_suratM' => 'M-001',
            'nomor_suratP' => 'P-001',
        ]);

        $pemdaKerjasama = new Kerjasama([
            'pemrakarsa' => 'P',
            'nomor_suratM' => 'M-002',
            'nomor_suratP' => 'P-002',
        ]);

        $this->assertSame('M-001', $mitraKerjasama->nomor_surat);
        $this->assertSame('P-002', $pemdaKerjasama->nomor_surat);
    }
}
