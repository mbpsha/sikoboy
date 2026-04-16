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
        $this->assertTrue(Route::has('admin.manajemen-potensi.store'));
        $this->assertTrue(Route::has('admin.manajemen-potensi.update'));
        $this->assertTrue(Route::has('admin.manajemen-potensi.destroy'));
        $this->assertTrue(Route::has('admin.manajemen-dokumen.index'));
        $this->assertTrue(Route::has('admin.manajemen-dokumen.store'));
        $this->assertTrue(Route::has('admin.manajemen-dokumen.download'));
        $this->assertTrue(Route::has('admin.manajemen-dokumen.destroy'));
    }

    public function test_template_dokumen_public_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('template-dokumen.index'));
        $this->assertTrue(Route::has('template-dokumen.download'));
    }

    public function test_admin_manajemen_dokumen_routes_require_auth_and_admin_role(): void
    {
        $route = Route::getRoutes()->getByName('admin.manajemen-dokumen.store');
        $this->assertNotNull($route);

        $middleware = $route->gatherMiddleware();
        $this->assertContains('auth', $middleware);
        $this->assertContains('role:admin', $middleware);
    }
}
