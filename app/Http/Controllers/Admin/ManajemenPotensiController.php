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
                'potensi' => null,
            ]);
        }

        $potensi = Potensi::query()
            ->where('kategori', $activeKategori)
            ->with('poin')
            ->first();

        return Inertia::render('Admin/ManajemenPotensi', [
            'kategori_list' => $kategoriList,
            'active_kategori' => $activeKategori,
            'potensi' => $potensi ? $this->formatPotensi($potensi) : null,
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'kategori' => 'required|string',
        'potensi' => 'required|array',
    ]);

    DB::transaction(function () use ($request) {

        foreach ($request->potensi as $item) {

            $potensi = Potensi::create([
                'kategori' => $request->kategori,
                'judul' => $item['judul'],
                'deskripsi' => $item['deskripsi'],
                'status_tampil' => true
            ]);

            // poin
            if (isset($item['poin'])) {
                foreach ($item['poin'] as $i => $p) {
                    $potensi->poin()->create([
                        'isi' => $p,
                        'urutan' => $i + 1
                    ]);
                }
            }

            // gambar
            if (isset($item['gambar'])) {
                $path = $item['gambar']->store('potensi', 'public');

                $potensi->update([
                    'gambar_path' => $path
                ]);
            }
        }

    });

    return back()->with('success', 'Berhasil simpan banyak potensi');
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
