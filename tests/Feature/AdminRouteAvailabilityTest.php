<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRouteAvailabilityTest extends TestCase
{
    public function test_admin_panel_section_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('admin.beranda.index'));
        $this->assertTrue(Route::has('admin.pengguna.index'));
        $this->assertTrue(Route::has('admin.data-kerjasama.index'));
        $this->assertTrue(Route::has('admin.riwayat-kerjasama.mitra'));
        $this->assertTrue(Route::has('admin.manajemen-potensi.index'));
        $this->assertTrue(Route::has('admin.manajemen-dokumen.index'));
    }

    public function test_template_dokumen_public_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('template-dokumen.index'));
        $this->assertTrue(Route::has('template-dokumen.download'));
    }
}
