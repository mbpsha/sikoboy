<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ManajemenDokumenController extends Controller
{
    private const STORAGE_DIRECTORY = 'template-dokumen';

    private const PRIMARY_STORAGE_DISK = 'local';

    private const FALLBACK_STORAGE_DISK = 'public';

    private const JENIS_DOKUMEN = [
        'KSB',
        'Nota Kesepakatan',
        'Perjanjian Teknis',
        'PKS',
        'Rencana Kerja',
        'MOU',
        'RKT',
        'LOI',
    ];

    public function index()
    {
        return Inertia::render('Admin/ManajemenDokumen', [
            'templates' => $this->templateList(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'id_kategori' => ['nullable', 'exists:kategori_kerjasama,id_kategori'],
            'jenis_dokumen' => ['nullable', 'string', Rule::in(self::JENIS_DOKUMEN)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $admin = $request->user()?->admin;
        abort_if($admin === null, 403, 'Akses ditolak.');

        $file = $validated['template_file'];
        $path = $file->store(self::STORAGE_DIRECTORY, self::PRIMARY_STORAGE_DISK);

        TemplateDokumen::create([
            'id_admin' => $admin->id_admin,
            'id_kategori' => $validated['id_kategori'] ?? null,
            'nama_file' => $file->getClientOriginalName(),
            'jenis_dokumen' => $validated['jenis_dokumen'] ?? null,
            'lokasi_file' => $path,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return back()->with('success', 'Template dokumen berhasil diunggah.');
    }

    public function update(int $id, Request $request)
    {
        $validated = $request->validate([
            'template_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'id_kategori' => ['nullable', 'exists:kategori_kerjasama,id_kategori'],
            'jenis_dokumen' => ['nullable', 'string', Rule::in(self::JENIS_DOKUMEN)],
            'is_active' => ['sometimes', 'boolean'],
            'nama_file' => ['nullable', 'string', 'max:255'],
        ]);

        $template = TemplateDokumen::findOrFail($id);

        $payload = [
            'id_kategori' => $validated['id_kategori'] ?? $template->id_kategori,
            'jenis_dokumen' => $validated['jenis_dokumen'] ?? $template->jenis_dokumen,
        ];

        if (array_key_exists('is_active', $validated)) {
            $payload['is_active'] = (bool) $validated['is_active'];
        }

        if (array_key_exists('nama_file', $validated) && $validated['nama_file'] !== null) {
            $payload['nama_file'] = $validated['nama_file'];
        }

        $oldPath = $template->lokasi_file;

        if (! empty($validated['template_file'])) {
            $file = $validated['template_file'];
            $payload['lokasi_file'] = $file->store(self::STORAGE_DIRECTORY, self::PRIMARY_STORAGE_DISK);
            $payload['nama_file'] = $payload['nama_file'] ?? $file->getClientOriginalName();
        }

        $template->update($payload);

        if (! empty($validated['template_file'])) {
            foreach ([self::PRIMARY_STORAGE_DISK, self::FALLBACK_STORAGE_DISK] as $disk) {
                if (Storage::disk($disk)->exists($oldPath)) {
                    Storage::disk($disk)->delete($oldPath);
                }
            }
        }

        return back()->with('success', 'Template dokumen berhasil diperbarui.');
    }

    public function listPublic()
    {
        return response()->json([
            'data' => $this->templateList(),
        ]);
    }

    public function list()
    {
        return response()->json([
            'data' => $this->templateList(),
        ]);
    }

    public function download(int $id)
    {
        $template = TemplateDokumen::findOrFail($id);

        $disk = $this->resolveStorageDisk($template->lokasi_file);
        abort_if($disk === null, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk($disk);

        return $fs->download($template->lokasi_file, $template->nama_file);
    }

    /**
     * Display the template inline (PDF preview in browser).
     */
    public function preview(int $id)
    {
        $template = TemplateDokumen::findOrFail($id);

        $disk = $this->resolveStorageDisk($template->lokasi_file);
        abort_if($disk === null, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk($disk);

        $mime = $fs->mimeType($template->lokasi_file) ?? '';
        abort_if(! str_starts_with($mime, 'application/pdf'), 415, 'Hanya PDF yang bisa ditampilkan.');

        $path = $fs->path($template->lokasi_file);
        abort_if(! is_file($path), 404);

        $filename = str_replace(["\r", "\n", '"'], ['', '', "'"], basename($template->nama_file));

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function destroy(int $id)
    {
        $template = TemplateDokumen::findOrFail($id);

        foreach ([self::PRIMARY_STORAGE_DISK, self::FALLBACK_STORAGE_DISK] as $disk) {
            if (Storage::disk($disk)->exists($template->lokasi_file)) {
                Storage::disk($disk)->delete($template->lokasi_file);
            }
        }

        $template->delete();

        return back()->with('success', 'Template dokumen berhasil dihapus.');
    }

    private function templateList()
    {
        return TemplateDokumen::query()
            ->with('kategori:id_kategori,nama_kategori')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (TemplateDokumen $template) => [
                'id_template_dokumen' => $template->id_template_dokumen,
                'id_kategori' => $template->id_kategori,
                'nama_kategori' => $template->kategori?->nama_kategori,
                'nama_file' => $template->nama_file,
                'jenis_dokumen' => $template->jenis_dokumen,
                'is_active' => (bool) $template->is_active,
                'created_at' => $template->created_at,
                'download_url' => route('template-dokumen.download', $template->id_template_dokumen),
                'preview_url' => route('template-dokumen.preview', $template->id_template_dokumen),
            ])
            ->values();
    }

    private function resolveStorageDisk(string $path): ?string
    {
        if (Storage::disk(self::PRIMARY_STORAGE_DISK)->exists($path)) {
            return self::PRIMARY_STORAGE_DISK;
        }

        if (Storage::disk(self::FALLBACK_STORAGE_DISK)->exists($path)) {
            return self::FALLBACK_STORAGE_DISK;
        }

        return null;
    }
}
