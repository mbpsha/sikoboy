<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Support\NotificationFeed;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        $mitra = $user->mitra;
        
        // Get kerjasama statistics (include in-progress and newly created ones)
        $kerjasama = $mitra->kerjasama()->get();
        $stats = [
            'total_pengajuan' => $kerjasama->count(),
            'disetujui' => $kerjasama->where('status_persetujuan', 'disetujui')->count(),
            'dalam_proses' => $kerjasama->where('status_persetujuan', 'dalam_proses')->count() + $kerjasama->where('is_finalized', false)->count(),
            'pending' => $kerjasama->where('status_persetujuan', null)->count(),
        ];

        // Get latest kerjasama with periode and riwayat/proses info (include non-finalized)
        $latestKerjasama = $mitra->kerjasama()
            ->with('kategori', 'periodes', 'riwayatStatus')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($k) {
                $lastPeriode = collect($k->periodes)->sortByDesc('tanggal_berakhir')->first();
                $periodeText = '-';

                if ($lastPeriode) {
                    $start = Carbon::parse($lastPeriode->tanggal_mulai);
                    $end = Carbon::parse($lastPeriode->tanggal_berakhir);

                    // Use DateInterval to get years, months, days precisely
                    $diff = $start->diff($end);
                    $years = $diff->y;
                    $months = $diff->m;
                    $days = $diff->d;

                    $parts = [];
                    if ($years > 0) $parts[] = $years . ' Tahun';
                    if ($months > 0) $parts[] = $months . ' Bulan';
                    // always show days (if zero and no other parts, show '0 Hari')
                    if ($days > 0 || empty($parts)) $parts[] = $days . ' Hari';

                    $periodeText = implode(' ', $parts);
                }

                $riwayatList = collect($k->riwayatStatus)->sortBy('id_riwayat')->values();

                $prosesList = $riwayatList->map(function ($r, $idx) use ($k, $riwayatList) {
                    $label      = $r->judul ?: ($r->catatan ?: ($r->status?->jenis_status ?? null));
                    $penanggung = $r->admin?->divisi ?? $r->penanggung_jawab ?? null;

                    // Riwayat berikutnya
                    $nextRiwayat = $riwayatList->get($idx + 1);

                    // Cari dokumen mitra berdasarkan id_dokumen range
                    // yaitu id_dokumen > dokumen admin di riwayat ini, tapi < dokumen admin di riwayat berikutnya
                    $adminDokumen = $k->dokumen()
                        ->where('tipe_dokumen', 'admin')
                        ->where('lokasi_file', $r->file)
                        ->first();

                    $nextAdminDokumen = $nextRiwayat?->file
                        ? $k->dokumen()
                            ->where('tipe_dokumen', 'admin')
                            ->where('lokasi_file', $nextRiwayat->file)
                            ->first()
                        : null;

                    $fileMitraQuery = $k->dokumen()->where('tipe_dokumen', 'mitra');

                    if ($adminDokumen) {
                        $fileMitraQuery->where('id_dokumen', '>', $adminDokumen->id_dokumen);
                    }

                    if ($nextAdminDokumen) {
                        $fileMitraQuery->where('id_dokumen', '<', $nextAdminDokumen->id_dokumen);
                    }

                    $fileMitra = $fileMitraQuery
                        ->orderBy('id_dokumen', 'desc')
                        ->first()
                        ?->lokasi_file ?? null;

                    return [
                        'id'         => $r->id_riwayat,
                        'title'      => $label,
                        'label'      => $label,
                        'catatan'    => $r->catatan,
                        'penanggung' => $penanggung,
                        'tanggal'    => $r->tanggal,
                        'file'       => $r->file ?? null,
                        'file_mitra' => $fileMitra,
                    ];
                })->values()->all();

                $riwayatCount = count($prosesList);

                if ($k->is_finalized) {
                    $statusLabel = 'Selesai';
                } else {
                    $statusLabel = $riwayatCount > 0 ? 'Proses ' . $riwayatCount : ($k->status_persetujuan ?? 'Pending');
                }

                return [
                    'id_kerjasama' => $k->id_kerjasama,
                    'judul' => $k->judul,
                    'status' => $k->status_persetujuan ?? 'pending',
                    'kategori' => $k->kategori?->nama_kategori ?? '-',
                    'urusan' => $k->urusan ?? '-',
                    'tanggal_daftar' => $k->created_at?->format('d M Y') ?? '-',
                    'periode' => $periodeText,
                    'is_finalized' => $k->is_finalized,
                    'proses' => $prosesList,
                    'status_label' => $statusLabel,
                    'dokumen_mitra' => $k->dokumen()->where('tipe_dokumen', 'mitra')->orderBy('versi_dokumen', 'desc')->first()?->lokasi_file ?? null,
                ];
            });

        return Inertia::render('Mitra/Profile/Profile', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $mitra,
            'stats' => $stats,
            'kerjasama_list' => $latestKerjasama,
        ]);
    }

    /**
     * Show the notifications list page.
     */
    public function notifications(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        return Inertia::render('Mitra/Profile/ListNotif', [
            'allNotifications' => NotificationFeed::forMitra($user, 250)->values()->all(),
        ]);
    }

    /**
     * Show profile completion form for mitra without profile.
     */
    public function completeProfile(Request $request)
    {
        if ($request->user()->mitra) {
            return redirect()->route('mitra.profile.edit');
        }

        return Inertia::render('Mitra/Profile/Edit', [
            'user' => [
                'email' => $request->user()->email,
            ],
            'mitra' => null,
            'mode' => 'complete',
        ]);
    }

    /**
     * Store profile for first-time mitra completion.
     */
    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'no_handphone' => 'required|string|max:50',
            'alamat' => 'required|string',
        ]);

        $request->user()->mitra()->updateOrCreate(
            ['id_user' => $request->user()->id_user],
            $validated
        );

        return redirect()->route('mitra.profile.edit')
            ->with('success', 'Profil berhasil dilengkapi.');
    }

    /**
     * Show the profile edit form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        if (! $user->mitra) {
            return redirect()->route('mitra.profile.complete');
        }

        return Inertia::render('Mitra/Profile/Edit', [
            'user' => [
                'email' => $user->email,
            ],
            'mitra' => $user->mitra,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the partner profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'no_handphone' => 'required|string|max:50',
            'alamat' => 'required|string',
        ]);

        $request->user()->mitra()->updateOrCreate(
            ['id_user' => $request->user()->id_user],
            $validated
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
