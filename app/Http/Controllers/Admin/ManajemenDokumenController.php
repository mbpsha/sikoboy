<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ManajemenDokumenController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ManajemenDokumen/Index', [
            'templates' => $this->templateList(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $validated['template_file'];
        $path = $file->store('template-dokumen', 'public');

        TemplateDokumen::create([
            'id_admin' => $request->user()->admin->id_admin,
            'nama_file' => $file->getClientOriginalName(),
            'lokasi_file' => $path,
        ]);

        return back()->with('success', 'Template dokumen berhasil diunggah.');
    }

    public function listPublic()
    {
        return response()->json([
            'data' => $this->templateList(),
        ]);
    }

    public function download(int $id)
    {
        $template = TemplateDokumen::findOrFail($id);
        abort_unless(Storage::disk('public')->exists($template->lokasi_file), 404);

        return Storage::disk('public')->download($template->lokasi_file, $template->nama_file);
    }

    private function templateList()
    {
        return TemplateDokumen::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (TemplateDokumen $template) => [
                'id_template_dokumen' => $template->id_template_dokumen,
                'nama_file' => $template->nama_file,
                'created_at' => $template->created_at,
                'download_url' => route('template-dokumen.download', $template->id_template_dokumen),
            ])
            ->values();
    }
}
