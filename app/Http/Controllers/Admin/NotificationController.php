<?php

namespace App\Http\Controllers\Admin;

use App\Models\Dokumen;
use App\Models\Kerjasama;
use App\Models\RiwayatStatus;
use App\Http\Controllers\Controller;
use App\Support\NotificationFeed;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/NotifAdmin', [
            'notifications' => NotificationFeed::forAdmin(250)->values()->all(),
        ]);
    }

    public function show(string $id)
    {
        // ========================================
        // NOTIF UPLOAD / REVISI DOKUMEN
        // ========================================
        if (str_starts_with($id, 'admin-upload-')) {

            $dokumenId = (int) str_replace('admin-upload-', '', $id);

            $dokumen = Dokumen::with([
                'kerjasama.mitra',
                'kerjasama.latestPeriode',
            ])->findOrFail($dokumenId);

            $kerjasama = $dokumen->kerjasama;

            return Inertia::render('Admin/DetailNotifAdmin', [
                'notificationType' => 'upload',

                'notification' => [
                    'title' => $dokumen->versi_dokumen > 1
                        ? 'Mitra Mengunggah Revisi Dokumen'
                        : 'Mitra Mengunggah Dokumen Pengajuan',

                    'nama_file' => $dokumen->nama_file,
                    'versi_dokumen' => $dokumen->versi_dokumen,

                    'mitra' => $kerjasama->mitra?->nama_perusahaan,

                    'kerjasama' => [
                        'judul' => $kerjasama->judul,
                        'nomor' => $kerjasama->nomor_suratM
                            ?: $kerjasama->nomor_suratP,
                    ],

                    'created_at' => $dokumen->created_at,
                ],
            ]);
        }

        // ========================================
        // NOTIF STATUS KONTRAK
        // ========================================
        if (str_starts_with($id, 'admin-status-')) {

            $kerjasamaId = (int) str_replace('admin-status-', '', $id);

            $kerjasama = Kerjasama::with([
                'mitra',
                'latestPeriode',
            ])->findOrFail($kerjasamaId);

            $periode = $kerjasama->latestPeriode;

            $tanggalBerakhir = Carbon::parse(
                $periode->tanggal_berakhir
            );

            $daysLeft = max(
                now()->diffInDays($tanggalBerakhir, false),
                0
            );

            return Inertia::render('Admin/DetailNotifAdmin', [
                'notificationType' => 'status',

                'notification' => [
                    'judul' => $kerjasama->judul,

                    'nomor' => $kerjasama->nomor_suratM
                        ?: $kerjasama->nomor_suratP,

                    'tanggalMulai' => $periode->tanggal_mulai,

                    'tanggalBerakhir' => $periode->tanggal_berakhir,

                    'daysLeft' => $daysLeft,

                    'status' => strtolower(
                        $kerjasama->status_aktif
                    ) === 'berakhir'
                        ? 'expired'
                        : 'warning',

                    'type' => $kerjasama->pemrakarsa === 'M'
                        ? 'MITRA'
                        : 'SETDA',

                    'durasi' => $kerjasama->jangka_waktu
                        ? $kerjasama->jangka_waktu . ' Tahun'
                        : '-',

                    'pihak1' => [
                        'nama_instansi' =>
                            'Sekretariat Daerah Kabupaten Boyolali',

                        'alamat' =>
                            'Boyolali',
                    ],

                    'pihak2' => [
                        'nama_instansi' =>
                            $kerjasama->mitra?->nama_perusahaan,

                        'alamat' =>
                            $kerjasama->mitra?->alamat,
                    ],
                ],
            ]);
        }

        // ========================================
        // NOTIF KERJASAMA DIBATALKAN
        // ========================================
        if (str_starts_with($id, 'admin-dibatalkan-')) {

            $riwayatId = (int) str_replace('admin-dibatalkan-', '', $id);

            $riwayat = RiwayatStatus::with([
                'kerjasama.mitra',
                'kerjasama.latestPeriode',
                'admin',
            ])->findOrFail($riwayatId);

            $kerjasama = $riwayat->kerjasama;
            $periode = $kerjasama?->latestPeriode;

            return Inertia::render('Admin/DetailNotifAdmin', [
                'notificationType' => 'dibatalkan',

                'notification' => [
                    'judul' => $kerjasama?->judul,
                    'nomor' => $kerjasama?->nomor_suratM ?: $kerjasama?->nomor_suratP,
                    'mitra' => $kerjasama?->mitra?->nama_perusahaan,
                    'admin' => $riwayat->admin?->nama,
                    'catatan' => $riwayat->catatan,
                    'tanggalMulai' => $periode?->tanggal_mulai,
                    'tanggalBerakhir' => $periode?->tanggal_berakhir,
                    'created_at' => $riwayat->tanggal,
                ],
            ]);
        }

        abort(404);
    }
}
