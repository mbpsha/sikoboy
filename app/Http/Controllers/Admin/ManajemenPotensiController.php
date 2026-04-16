<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePotensiRequest;
use App\Http\Requests\Admin\UpdatePotensiRequest;
use App\Models\Potensi;
use Inertia\Inertia;

class ManajemenPotensiController extends Controller
{
    public function index()
    {
        $potensi = Potensi::query()
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/ManajemenPotensi/Index', [
            'potensi' => $potensi,
        ]);
    }

    public function store(StorePotensiRequest $request)
    {
        Potensi::create($request->validated());

        return back()->with('success', 'Konten potensi berhasil ditambahkan.');
    }

    public function update(int $id, UpdatePotensiRequest $request)
    {
        $potensi = Potensi::findOrFail($id);
        $potensi->update($request->validated());

        return back()->with('success', 'Konten potensi berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $potensi = Potensi::findOrFail($id);
        $potensi->delete();

        return back()->with('success', 'Konten potensi berhasil dihapus.');
    }
}
