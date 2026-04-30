<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peraturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PeraturanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ManajemenPeraturan', [
            'peraturans' => Peraturan::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'file' => 'required|file|max:20240',
                'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048'
            ]);

            if (!$request->hasFile('file')) {
                return back()->withErrors(['file' => 'File dokumen harus dipilih']);
            }

            if (!$request->file('file')->isValid()) {
                return back()->withErrors(['file' => 'File tidak valid atau gagal terupload']);
            }

            $filePath = $request->file('file')->store('peraturan', 'public');

            if (!$filePath) {
                return back()->withErrors(['file' => 'Gagal menyimpan file ke storage']);
            }

            $thumbPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('peraturan', 'public');
            }

            $peraturan = Peraturan::create([
                'judul' => $request->judul,
                'file' => $filePath,
                'thumbnail' => $thumbPath
            ]);

            if (!$peraturan) {
                Storage::disk('public')->delete($filePath);
                if ($thumbPath) {
                    Storage::disk('public')->delete($thumbPath);
                }
                return back()->withErrors(['error' => 'Gagal menyimpan data ke database']);
            }

            return back()->with('success', 'Peraturan berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Peraturan Store Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Peraturan $peraturan)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'file' => 'nullable|file|max:20240',
                'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048'
            ]);

            $oldFile = $peraturan->file;
            $oldThumb = $peraturan->thumbnail;

            if ($request->file('file')) {
                if (!$request->file('file')->isValid()) {
                    return back()->withErrors(['file' => 'File tidak valid atau gagal terupload']);
                }

                $newFile = $request->file('file')->store('peraturan', 'public');
                if (!$newFile) {
                    return back()->withErrors(['file' => 'Gagal menyimpan file ke storage']);
                }

                $peraturan->file = $newFile;
            }

            if ($request->file('thumbnail')) {
                $newThumb = $request->file('thumbnail')->store('peraturan', 'public');
                if (!$newThumb) {
                    return back()->withErrors(['thumbnail' => 'Gagal menyimpan thumbnail ke storage']);
                }
                $peraturan->thumbnail = $newThumb;
            }

            $peraturan->judul = $request->judul;
            $updated = $peraturan->save();

            if (!$updated) {
                return back()->withErrors(['error' => 'Gagal memperbarui data ke database']);
            }

            // Delete old files only after successful update
            if ($request->file('file') && $oldFile) {
                Storage::disk('public')->delete($oldFile);
            }
            if ($request->file('thumbnail') && $oldThumb) {
                Storage::disk('public')->delete($oldThumb);
            }

            return back()->with('success', 'Peraturan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Peraturan Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Peraturan $peraturan)
    {
        try {
            $file = $peraturan->file;
            $thumb = $peraturan->thumbnail;

            $deleted = $peraturan->delete();

            if (!$deleted) {
                return back()->withErrors(['error' => 'Gagal menghapus peraturan']);
            }

            // Delete files only after successful deletion
            if ($file) {
                Storage::disk('public')->delete($file);
            }
            if ($thumb) {
                Storage::disk('public')->delete($thumb);
            }

            return back()->with('success', 'Peraturan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Peraturan Delete Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}