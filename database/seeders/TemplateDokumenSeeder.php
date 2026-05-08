<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\KategoriKerjasama;
use App\Models\TemplateDokumen;
use Illuminate\Database\Seeder;

class TemplateDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultAdminId = Admin::query()->orderBy('id_admin')->value('id_admin');

        if ($defaultAdminId === null) {
            return;
        }

        KategoriKerjasama::query()
            ->whereNotNull('file_template')
            ->where('file_template', '!=', '')
            ->where('file_template', '!=', '-')
            ->get()
            ->each(function (KategoriKerjasama $kategori) use ($defaultAdminId): void {
                $normalizedPath = $this->normalizePath((string) $kategori->file_template);

                if ($normalizedPath === null) {
                    return;
                }

                $namaFile = basename($normalizedPath);

                if ($namaFile === '') {
                    return;
                }

                TemplateDokumen::query()->updateOrCreate(
                    [
                        'id_kategori' => $kategori->id_kategori,
                        'lokasi_file' => $normalizedPath,
                    ],
                    [
                        'id_admin' => $defaultAdminId,
                        'nama_file' => $namaFile,
                        'jenis_dokumen' => $this->inferJenisDokumen($kategori->nama_kategori, $namaFile),
                        'is_active' => true,
                    ]
                );
            });
    }

    private function normalizePath(string $rawPath): ?string
    {
        $path = trim(str_replace('\\', '/', $rawPath));

        if ($path === '' || $path === '-') {
            return null;
        }

        if (str_starts_with($path, '/storage/')) {
            return ltrim(substr($path, strlen('/storage/')), '/');
        }

        if (str_starts_with($path, 'storage/')) {
            return ltrim(substr($path, strlen('storage/')), '/');
        }

        if (str_starts_with($path, '/public/')) {
            return ltrim(substr($path, strlen('/public/')), '/');
        }

        if (str_starts_with($path, 'public/')) {
            return ltrim(substr($path, strlen('public/')), '/');
        }

        return ltrim($path, '/');
    }

    private function inferJenisDokumen(string $namaKategori, string $namaFile): ?string
    {
        $source = strtolower($namaKategori.' '.$namaFile);

        if (str_contains($source, 'pks')) {
            return 'PKS';
        }

        if (str_contains($source, 'kesepakatan bersama')) {
            return 'KSB';
        }

        if (str_contains($source, 'nota kesepakatan')) {
            return 'Nota Kesepakatan';
        }

        return null;
    }
}
