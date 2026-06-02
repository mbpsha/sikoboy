<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePotensiRequest;
use App\Http\Requests\Admin\UpdatePotensiRequest;
use App\Models\Potensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ManajemenPotensiController extends Controller
{
    /**
     * Tabs shown in the UI (can be extended by adding new categories in DB).
     *
     * @var list<string>
     */
    private const DEFAULT_KATEGORI = [
        'Pertanian',
        'Peternakan',
        'Wisata Alam',
        'UMKM',
        'Budaya',
    ];

    public function index(Request $request)
    {
        $dbKategori = Potensi::query()
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori')
            ->all();

        $kategoriList = array_values(array_unique(array_merge(self::DEFAULT_KATEGORI, $dbKategori)));

        $activeKategori = $request->query('kategori', $kategoriList[0] ?? null);
        if ($activeKategori === null) {
            return Inertia::render('Admin/ManajemenPotensi', [
                'kategori_list' => [],
                'active_kategori' => null,
                'potensi_list' => [],
            ]);
        }

        // Get all potensi for all categories
        $allPotensi = Potensi::query()
            ->with('poin')
            ->orderBy('kategori')
            ->orderBy('id_potensi')
            ->get()
            ->map(fn (Potensi $p) => [
                'id_potensi' => $p->id_potensi,
                'kategori' => $p->kategori,
                'judul' => $p->judul,
                'deskripsi' => $p->deskripsi,
                'gambar_url' => $p->gambar_path ? asset('storage/'.$p->gambar_path) : null,
                'status_tampil' => $p->status_tampil,
                'poin' => $p->poin->map(fn ($pt) => [
                    'id' => $pt->id_potensi_poin,
                    'isi' => $pt->isi,
                ])->values(),
            ])
            ->values();

        return Inertia::render('Admin/ManajemenPotensi', [
            'kategori_list' => $kategoriList,
            'active_kategori' => $activeKategori,
            'potensi_list' => $allPotensi,
        ]);
    }

    public function list()
    {
        $items = Potensi::query()
            ->with('poin')
            ->orderBy('kategori')
            ->orderBy('id_potensi')
            ->get()
            ->map(fn (Potensi $potensi) => $this->formatPotensi($potensi))
            ->values();

        return response()->json([
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $potensi = Potensi::create([
            'kategori' => $request->kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status_tampil' => true,
        ]);

        // Save image if provided
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('potensi', 'public');
            $potensi->update(['gambar_path' => $path]);
        }

        return back()->with('success', 'Potensi berhasil ditambahkan.');
    }
    public function update(int $id, UpdatePotensiRequest $request)
    {
        $potensi = Potensi::with('poin')->findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($potensi, $request, $validated) {
            $payload = array_filter([
                'kategori' => $validated['kategori'] ?? null,
                'judul' => $validated['judul'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'status_tampil' => $validated['status_tampil'] ?? null,
            ], fn ($v) => $v !== null);

            if (! empty($payload)) {
                $payload['updated_at'] = now();
                $potensi->update($payload);
            }

            if ($request->hasFile('gambar')) {
                $this->replaceGambar($potensi, $request->file('gambar'));
            } elseif (($validated['remove_gambar'] ?? false) && $potensi->gambar_path) {
                Storage::disk('public')->delete($potensi->gambar_path);
                $potensi->update([
                    'gambar_path' => null,
                    'updated_at' => now(),
                ]);
            }

            if (array_key_exists('poin', $validated)) {
                $this->syncPoin($potensi, $validated['poin'] ?? []);
            }
        });

        return back()->with('success', 'Potensi berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $potensi = Potensi::findOrFail($id);

        if ($potensi->gambar_path) {
            Storage::disk('public')->delete($potensi->gambar_path);
        }

        $potensi->delete();

        return back()->with('success', 'Potensi berhasil dihapus.');
    }

    private function formatPotensi(Potensi $potensi): array
    {
        return [
            'id_potensi' => $potensi->id_potensi,
            'kategori' => $potensi->kategori,
            'judul' => $potensi->judul,
            'deskripsi' => $potensi->deskripsi,
            'status_tampil' => (bool) $potensi->status_tampil,
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
    }

    private function syncPoin(Potensi $potensi, array $poinList): void
    {
        $clean = collect($poinList)
            ->filter(fn ($p) => is_string($p) && trim($p) !== '')
            ->map(fn ($p) => trim($p))
            ->values();

        $potensi->poin()->delete();

        foreach ($clean as $i => $isi) {
            $potensi->poin()->create([
                'isi' => $isi,
                'urutan' => $i + 1,
            ]);
        }
    }

    private function replaceGambar(Potensi $potensi, $file): void
    {
        $oldPath = $potensi->gambar_path;

        $path = $file->store('potensi', 'public');

        $potensi->update([
            'gambar_path' => $path,
            'updated_at' => now(),
        ]);

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
    }
}
