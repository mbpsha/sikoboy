<?php

namespace App\Http\Controllers\Mitra;

use App\Enums\StatusPersetujuan;
use App\Http\Controllers\Controller;
use App\Models\Kerjasama;
use App\Support\NotificationFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show the mitra profile page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $mitra = $user?->mitra;

        $kerjasamaItems = $mitra
            ? $mitra->kerjasama()
                ->with([
                    'latestPeriode',
                    'kategori',
                    'dokumen',
                    'riwayatStatus.status',
                    'riwayatStatus.admin',
                ])
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $kerjasamaList = $kerjasamaItems->map(function (Kerjasama $kerjasama) {
            $latestPeriode = $kerjasama->latestPeriode;
            $latestRiwayat = $kerjasama->riwayatStatus->sortByDesc('tanggal')->first();
            $latestStatus = $latestRiwayat?->status?->jenis_status;
            $statusPersetujuan = $kerjasama->status_persetujuan?->value;
            $status = 'pending';

            if ($statusPersetujuan === StatusPersetujuan::Disetujui->value) {
                $status = 'disetujui';
            } elseif (in_array($latestStatus, ['ditolak', 'dibatalkan'], true) || in_array($statusPersetujuan, ['ditolak', 'dibatalkan'], true)) {
                $status = 'ditolak';
            } elseif (in_array($latestStatus, ['revisi', 'diajukan', 'diproses'], true) || in_array($statusPersetujuan, ['revisi', 'diajukan', 'diproses'], true)) {
                $status = 'dalam_proses';
            }

            $proses = $kerjasama->riwayatStatus
                ->sortBy('tanggal')
                ->values()
                ->map(function ($riwayat) use ($kerjasama) {
                    $statusName = $riwayat->status?->jenis_status ?: 'proses';

                    // Find mitra dokumen linked specifically to this riwayat
                    $mitraDokumen = collect($kerjasama->dokumen ?? [])
                        ->where('tipe_dokumen', 'mitra')
                        ->where('id_riwayat', $riwayat->id_riwayat)
                        ->sortByDesc('versi_dokumen')
                        ->first();

                    return [
                        'id' => $riwayat->id_riwayat,
                        'title' => ucfirst(str_replace('_', ' ', $statusName)),
                        'tanggal' => $riwayat->tanggal ? Carbon::parse($riwayat->tanggal)->format('d/m/Y H:i') : '-',
                        'catatan' => $riwayat->catatan,
                        'file' => $riwayat->file,
                        'file_mitra' => $mitraDokumen?->lokasi_file,
                        'file_mitra_name' => $mitraDokumen?->nama_file,
                        'file_mitra_created_at' => $mitraDokumen?->created_at
                            ? Carbon::parse($mitraDokumen->created_at)->format('d/m/Y H:i')
                            : null,
                        'penanggung' => $riwayat->penanggung_jawab,
                        'pegawai' => $riwayat->penanggung_jawab,
                    ];
                })
                ->values();

            return [
                'id_kerjasama' => $kerjasama->id_kerjasama,
                'judul' => $kerjasama->judul,
                'kategori' => $kerjasama->kategori?->nama_kategori ?: $kerjasama->jenis_kerjasama,
                'urusan' => $kerjasama->urusan,
                'status' => $status,
                'tanggal_daftar' => $kerjasama->created_at ? Carbon::parse($kerjasama->created_at)->format('d/m/Y') : '-',
                'periode' => $latestPeriode
                    ? Carbon::parse($latestPeriode->tanggal_mulai)->format('d/m/Y').' - '.Carbon::parse($latestPeriode->tanggal_berakhir)->format('d/m/Y')
                    : '-',
                'proses' => $proses,
            ];
        })->values();

        $totalPengajuan = $kerjasamaItems->count();
        $disetujui = $kerjasamaItems->filter(fn (Kerjasama $kerjasama) => $kerjasama->status_persetujuan?->value === StatusPersetujuan::Disetujui->value)->count();
        $pending = $kerjasamaItems->filter(function (Kerjasama $kerjasama) {
            $status = $kerjasama->status_persetujuan?->value;

            return $status === null || in_array($status, ['diajukan', 'diproses', 'pending'], true);
        })->count();
        $dalamProses = max($totalPengajuan - $disetujui - $pending, 0);

        return Inertia::render('Mitra/Profile/Profile', [
            'mitra' => $mitra,
            'stats' => [
                'total_pengajuan' => $totalPengajuan,
                'disetujui' => $disetujui,
                'dalam_proses' => $dalamProses,
                'pending' => $pending,
            ],
            'kerjasama_list' => $kerjasamaList,
            'notifications' => NotificationFeed::forMitra($user, 50),
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

    /**
     * Show the mitra notifications page.
     */
    public function notifications(Request $request)
    {
        return Inertia::render('Mitra/Profile/ListNotif', [
            'allNotifications' => NotificationFeed::forMitra($request->user(), 50),
        ]);
    }

    /**
     * Mark a notification as read (store in session).
     */
    public function markNotificationAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $key = 'read_notifications_'.$user->id_user;
        
        // Get current read notifications from session
        $readNotifications = session($key, []);
        
        // Add this notification ID if not already present
        if (!in_array($id, $readNotifications)) {
            $readNotifications[] = $id;
            session([$key => $readNotifications]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca',
        ]);
    }
}
