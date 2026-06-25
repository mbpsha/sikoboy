<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Menyimpan file dengan nama ASLI (bukan hash) sambil tetap aman:
 *  - karakter berbahaya di-sanitize
 *  - bila nama sudah dipakai, otomatis diberi suffix (1), (2), ...
 */
class FileUpload
{
    /**
     * Simpan file dengan nama asli ke disk yang ditentukan.
     *
     * @return array{nama_file: string, lokasi_file: string}
     */
    public static function storeAsOriginal(
        UploadedFile $file,
        string $directory,
        string $disk = 'public'
    ): array {
        $originalName = $file->getClientOriginalName();
        $safeName     = self::sanitizeFileName($originalName);
        $safeName     = self::ensureUniqueName($safeName, $directory, $disk);

        $path = $file->storeAs($directory, $safeName, $disk);

        return [
            'nama_file'   => $safeName,
            'lokasi_file' => $path,
        ];
    }

    /**
     * Buang karakter yang berbahaya untuk nama file di filesystem,
     * tetapi pertahankan huruf, angka, spasi, titik, tanda hubung & garis bawah.
     */
    public static function sanitizeFileName(string $name): string
    {
        // Pisahkan nama dan ekstensi
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $basename  = $extension !== ''
            ? substr($name, 0, -(strlen($extension) + 1))
            : $name;

        // Trim & buang kontrol karakter + karakter terlarang OS (\ / : * ? " < > |)
        $basename  = preg_replace('#[\x00-\x1F\x7F\\\\/:*?"<>|]#u', '', trim($basename));
        $extension = preg_replace('#[\x00-\x1F\x7F\\\\/:*?"<>|]#u', '', trim($extension));

        // Cegah nama kosong
        if ($basename === '' || $basename === null) {
            $basename = 'file';
        }

        // Batasi panjang nama (sisakan ruang untuk suffix anti-collision + ekstensi)
        $maxBase = 180;
        if (mb_strlen($basename) > $maxBase) {
            $basename = mb_substr($basename, 0, $maxBase);
        }

        return $extension !== '' ? $basename . '.' . $extension : $basename;
    }

    /**
     * Bila nama sudah dipakai di folder tujuan, tambahkan suffix " (n)".
     */
    public static function ensureUniqueName(string $name, string $directory, string $disk): string
    {
        $fs = Storage::disk($disk);

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $basename  = $extension !== ''
            ? substr($name, 0, -(strlen($extension) + 1))
            : $name;

        $candidate = $name;
        $counter   = 1;

        while ($fs->exists(rtrim($directory, '/') . '/' . $candidate)) {
            $candidate = $extension !== ''
                ? sprintf('%s (%d).%s', $basename, $counter, $extension)
                : sprintf('%s (%d)', $basename, $counter);
            $counter++;
        }

        return $candidate;
    }
}
