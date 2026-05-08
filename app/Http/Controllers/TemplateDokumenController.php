<?php

namespace App\Http\Controllers;

use App\Models\TemplateDokumen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TemplateDokumenController extends Controller
{
    private const PRIMARY_STORAGE_DISK = 'local';

    private const FALLBACK_STORAGE_DISK = 'public';

    public function index(Request $request): JsonResponse
    {
        $templates = TemplateDokumen::query()
            ->with('kategori:id_kategori,nama_kategori')
            ->where('is_active', true)
            ->when(
                $request->filled('id_kategori'),
                fn ($query) => $query->where('id_kategori', (int) $request->integer('id_kategori'))
            )
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (TemplateDokumen $template) => [
                'id_template_dokumen' => $template->id_template_dokumen,
                'id_kategori' => $template->id_kategori,
                'nama_kategori' => $template->kategori?->nama_kategori,
                'nama_file' => $template->nama_file,
                'jenis_dokumen' => $template->jenis_dokumen,
                'created_at' => $template->created_at,
                'download_url' => route('template-dokumen.download', $template->id_template_dokumen),
                'preview_url' => route('template-dokumen.preview', $template->id_template_dokumen),
            ])
            ->values();

        return response()->json([
            'data' => $templates,
        ]);
    }

    public function download(int $id): Response
    {
        $template = TemplateDokumen::query()
            ->where('is_active', true)
            ->findOrFail($id);

        [$disk, $path] = $this->resolveStorageLocation($template->lokasi_file);
        abort_if($disk === null || $path === null, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk($disk);

        return $fs->download($path, $template->nama_file);
    }

    public function preview(int $id): Response
    {
        $template = TemplateDokumen::query()
            ->where('is_active', true)
            ->findOrFail($id);

        [$disk, $path] = $this->resolveStorageLocation($template->lokasi_file);
        abort_if($disk === null || $path === null, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk($disk);
        $absolutePath = $fs->path($path);

        abort_if(! is_file($absolutePath), 404);

        $mime = $fs->mimeType($path) ?? 'application/octet-stream';
        $filename = str_replace(["\r", "\n", '"'], ['', '', "'"], basename($template->nama_file));

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function resolveStorageLocation(string $rawPath): array
    {
        $normalizedPath = $this->normalizePath($rawPath);

        if ($normalizedPath === null) {
            return [null, null];
        }

        if (Storage::disk(self::PRIMARY_STORAGE_DISK)->exists($normalizedPath)) {
            return [self::PRIMARY_STORAGE_DISK, $normalizedPath];
        }

        if (Storage::disk(self::FALLBACK_STORAGE_DISK)->exists($normalizedPath)) {
            return [self::FALLBACK_STORAGE_DISK, $normalizedPath];
        }

        return [null, null];
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
}
