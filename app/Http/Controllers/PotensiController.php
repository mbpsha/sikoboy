<?php

namespace App\Http\Controllers;

use App\Models\Potensi;
use Illuminate\Http\Request;

class PotensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Potensi::query()
            ->with('poin')
            ->where('status_tampil', true);

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->string('kategori')->toString());
        }

        $items = $query
            ->orderBy('kategori')
            ->orderBy('id_potensi')
            ->get()
            ->map(function (Potensi $potensi) {
                return [
                    'id_potensi' => $potensi->id_potensi,
                    'kategori' => $potensi->kategori,
                    'judul' => $potensi->judul,
                    'deskripsi' => $potensi->deskripsi,
                    'gambar_url' => $potensi->gambar_path
                        ? asset('storage/'.$potensi->gambar_path)
                        : null,
                    'poin' => $potensi->poin
                        ->map(fn ($p) => [
                            'id' => $p->id_potensi_poin,
                            'isi' => $p->isi,
                        ])
                        ->values(),
                ];
            })
            ->values();

        return response()->json([
            'data' => $items,
        ]);
    }
}
