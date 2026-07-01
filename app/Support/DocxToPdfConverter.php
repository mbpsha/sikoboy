<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Exception\InvalidImageException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use RuntimeException;
use Throwable;
use ZipArchive;

class DocxToPdfConverter
{
    private static bool $rendererConfigured = false;

    /**
     * Convert a DOCX file to PDF using PHPWord + DomPDF (default) or TCPDF.
     *
     * @return string Absolute path to the generated PDF file
     */
    public static function convert(string $inputPath, string $outputDirectory): string
    {
        if (! is_file($inputPath)) {
            throw new RuntimeException('File DOCX tidak ditemukan.');
        }

        if (! is_dir($outputDirectory) || ! is_writable($outputDirectory)) {
            throw new RuntimeException('Folder output konversi tidak dapat ditulis.');
        }

        $expectedPdf = $outputDirectory.DIRECTORY_SEPARATOR.pathinfo($inputPath, PATHINFO_FILENAME).'.pdf';
        $workingPath = self::createNormalizedCopy($inputPath);
        $cleanupCopy = $workingPath !== $inputPath;

        self::ensureTempDirectories();
        self::configurePdfRenderer();

        try {
            $phpWord = self::loadPhpWord($workingPath);
            $writer = IOFactory::createWriter($phpWord, 'PDF');
            $writer->save($expectedPdf);
        } catch (Throwable $e) {
            Log::error('PHPWord DOCX to PDF conversion failed', [
                'input' => $inputPath,
                'working' => $workingPath,
                'renderer' => config('services.docx_pdf.renderer', 'dompdf'),
                'message' => $e->getMessage(),
            ]);

            throw new RuntimeException('Konversi DOCX ke PDF gagal: '.$e->getMessage());
        } finally {
            if ($cleanupCopy && is_file($workingPath)) {
                @unlink($workingPath);
            }
        }

        if (! is_file($expectedPdf)) {
            throw new RuntimeException('Konversi DOCX ke PDF gagal: file PDF tidak dihasilkan.');
        }

        return $expectedPdf;
    }

    /**
     * Beberapa DOCX (mis. hasil export Word) memakai Target="/media/..." pada rels.
     * PHPWord menambahkan prefix "word/" sehingga path menjadi "word//media/..." dan gagal.
     */
    private static function createNormalizedCopy(string $inputPath): string
    {
        $tempDir = storage_path('app/temp/docx-convert');
        if (! is_dir($tempDir) && ! mkdir($tempDir, 0755, true) && ! is_dir($tempDir)) {
            return $inputPath;
        }

        $tempPath = $tempDir.DIRECTORY_SEPARATOR.uniqid('docx_', true).'.docx';
        if (! copy($inputPath, $tempPath)) {
            return $inputPath;
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath) !== true) {
            @unlink($tempPath);

            return $inputPath;
        }

        $changed = false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (! is_string($name) || ! str_ends_with($name, '.xml.rels')) {
                continue;
            }

            $content = $zip->getFromName($name);
            if (! is_string($content) || ! str_contains($content, 'Target="/')) {
                continue;
            }

            $fixed = preg_replace('/Target="\/([^"]+)"/', 'Target="../$1"', $content);
            if (! is_string($fixed) || $fixed === $content) {
                continue;
            }

            $zip->deleteName($name);
            $zip->addFromString($name, $fixed);
            $changed = true;
        }

        $zip->close();

        if (! $changed) {
            @unlink($tempPath);

            return $inputPath;
        }

        return $tempPath;
    }

    private static function loadPhpWord(string $path): \PhpOffice\PhpWord\PhpWord
    {
        try {
            return IOFactory::load($path);
        } catch (InvalidImageException $e) {
            Log::warning('DOCX load failed on embedded image, retrying without images', [
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            $reader = IOFactory::createReader('Word2007');
            $reader->setImageLoading(false);

            return $reader->load($path);
        }
    }

    private static function ensureTempDirectories(): void
    {
        $phpWordTemp = storage_path('app/temp/phpword');
        if (! is_dir($phpWordTemp)) {
            mkdir($phpWordTemp, 0755, true);
        }

        Settings::setTempDir($phpWordTemp);
    }

    private static function configurePdfRenderer(): void
    {
        if (self::$rendererConfigured) {
            return;
        }

        $renderer = strtolower((string) config('services.docx_pdf.renderer', 'dompdf'));

        if ($renderer === 'tcpdf') {
            self::configureTcpdf();
        } else {
            self::configureDompdf();
        }

        self::$rendererConfigured = true;
    }

    private static function configureDompdf(): void
    {
        $path = base_path('vendor/dompdf/dompdf');

        if (! is_dir($path)) {
            throw new RuntimeException(
                'Library DomPDF belum terpasang. Jalankan: composer require dompdf/dompdf'
            );
        }

        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
        Settings::setPdfRendererPath($path);
    }

    private static function configureTcpdf(): void
    {
        $path = base_path('vendor/tecnickcom/tcpdf');

        if (! is_dir($path) || ! is_file($path.'/tcpdf.php')) {
            throw new RuntimeException(
                'Library TCPDF belum terpasang. Jalankan: composer require tecnickcom/tcpdf'
            );
        }

        Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
        Settings::setPdfRendererPath($path);
    }
}
