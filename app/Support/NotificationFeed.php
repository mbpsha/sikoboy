<?php

namespace App\Support;

use App\Models\Kerjasama;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationFeed
{
    public static function forAdmin(int $limit = 50)
    {
        $uploadNotifications = DB::table('dokumen as d')
            ->join('users as u', 'u.id_user', '=', 'd.created_by')
            ->join('kerjasama as k', 'k.id_kerjasama', '=', 'd.id_kerjasama')
            ->leftJoin('mitras as m', 'm.id_mitra', '=', 'k.id_mitra')
            ->where('u.role', 'mitra')
            ->orderByDesc('d.created_at')
            ->limit($limit)
            ->get([
                'd.id_dokumen',
                'd.created_at',
                'd.versi_dokumen',
                'd.nama_file',
                'k.id_kerjasama',
                'k.judul',
                'k.pemrakarsa',
                'k.nomor_suratM',
                'k.nomor_suratP',
                'm.nama_perusahaan',
            ])
            ->map(function ($row) {
                $nomor = $row->nomor_suratM ?: $row->nomor_suratP;
                $versiLabel = (int) $row->versi_dokumen > 1 ? 'revisi dokumen' : 'dokumen pengajuan';
                $namaMitra = $row->nama_perusahaan ?: 'Mitra';
                $kind = (int) $row->versi_dokumen > 1 ? 'revisi_mitra' : 'pengajuan_kerjasama';
                $title = $namaMitra.' mengunggah '.$versiLabel;

                return [
                    'id' => 'admin-upload-'.$row->id_dokumen,
                    'type' => 'MITRA',
                    'kind' => $kind,
                    'title' => $title,
                    'description' => 'File '.$row->nama_file.' untuk kerjasama '.$row->judul.' telah diunggah oleh mitra.',
                    'nomor' => $nomor,
                    'tanggalBerakhir' => null,
                    'status' => 'info',
                    'status_group' => null,
                    'daysLeft' => null,
                    'countdown' => 'Baru',
                    'created_at' => Carbon::parse($row->created_at)->toISOString(),
                ];
            });

        $statusNotifications = self::adminStatusNotifications();

        return $uploadNotifications
            ->concat($statusNotifications)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();
    }

    public static function forMitra(User $user, int $limit = 50)
    {
        $mitra = $user->mitra;
        if (! $mitra) {
            return collect();
        }

        $adminUploadNotifications = DB::table('dokumen as d')
            ->join('users as u', 'u.id_user', '=', 'd.created_by')
            ->join('kerjasama as k', 'k.id_kerjasama', '=', 'd.id_kerjasama')
            ->leftJoin('periode_kerjasama as p', function ($join) {
                $join->on('p.id_kerjasama', '=', 'k.id_kerjasama')
                    ->whereRaw('p.tanggal_berakhir = (
                        SELECT MAX(p2.tanggal_berakhir)
                        FROM periode_kerjasama p2
                        WHERE p2.id_kerjasama = k.id_kerjasama
                    )');
            })
            ->where('u.role', 'admin')
            ->where('k.id_mitra', $mitra->id_mitra)
            ->orderByDesc('d.created_at')
            ->limit($limit)
            ->get([
                'd.id_dokumen',
                'd.created_at',
                'd.versi_dokumen',
                'd.nama_file',
                'k.id_kerjasama',
                'k.judul',
                'k.nomor_suratM',
                'k.nomor_suratP',
                'k.status_aktif',
                'p.tanggal_mulai',
                'p.tanggal_berakhir',
            ])
            ->map(function ($row) {
                $versiLabel = (int) $row->versi_dokumen > 1 ? 'revisi dokumen' : 'dokumen';
                $nomor = $row->nomor_suratM ?: $row->nomor_suratP;

                return [
                    'id' => 'mitra-upload-'.$row->id_dokumen,
                    'type' => 'document_update',
                    'status_type' => 'pending',
                    'title' => 'Admin mengunggah '.$versiLabel.' kerjasama',
                    'message' => 'Pemerintah Boyolali mengunggah file '.$row->nama_file.' untuk kerjasama '.$row->judul.'.',
                    'days_left' => null,
                    'kerjasama_id' => (int) $row->id_kerjasama,
                    'kerjasama_judul' => $row->judul,
                    'nomor_kerjasama' => $nomor,
                    'tanggal_mulai' => $row->tanggal_mulai,
                    'tanggal_berakhir' => $row->tanggal_berakhir,
                    'status' => $row->status_aktif ?: 'Aktif',
                    'created_at' => Carbon::parse($row->created_at)->toISOString(),
                ];
            });

        $processNotifications = self::mitraProcessNotifications((int) $mitra->id_mitra);
        $statusNotifications = self::mitraStatusNotifications((int) $mitra->id_mitra);

        return $adminUploadNotifications
            ->concat($processNotifications)
            ->concat($statusNotifications)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();
    }

    private static function adminStatusNotifications()
    {
        return Kerjasama::query()
            ->finalized()
            ->with(['latestPeriode', 'mitra'])
            ->whereIn(DB::raw('LOWER(status_aktif)'), ['segera berakhir', 'berakhir'])
            ->get()
            ->map(function (Kerjasama $kerjasama) {
                $periode = $kerjasama->latestPeriode;
                if (! $periode?->tanggal_berakhir) {
                    return null;
                }

                $statusAktif = Str::lower((string) $kerjasama->status_aktif);
                $isExpired = $statusAktif === 'berakhir';
                $tanggalBerakhir = Carbon::parse($periode->tanggal_berakhir);
                $daysLeft = Carbon::today()->diffInDays($tanggalBerakhir, false);
                $daysLeft = max($daysLeft, 0);
                $isMitraType = $kerjasama->pemrakarsa === 'M';
                $type = $isMitraType ? 'MITRA' : 'SETDA';
                $nomor = $kerjasama->nomor_suratM ?: $kerjasama->nomor_suratP;
                $prefix = $isMitraType ? 'Kerjasama' : 'Arsip dokumen';

                return [
                    'id' => 'admin-status-'.$kerjasama->id_kerjasama,
                    'type' => $type,
                    'title' => $isExpired ? $prefix.' telah berakhir' : $prefix.' akan berakhir dalam '.$daysLeft.' hari',
                    'description' => 'Masa kerjasama '.$kerjasama->judul.' berakhir pada '.$tanggalBerakhir->translatedFormat('d F Y').'.',
                    'nomor' => $nomor,
                    'tanggalBerakhir' => $tanggalBerakhir->toDateString(),
                    'status' => $isExpired ? 'expired' : 'warning',
                    'status_group' => $isExpired ? 'sudah_berakhir' : 'akan_berakhir',
                    'daysLeft' => $isExpired ? 0 : $daysLeft,
                    'countdown' => $isExpired ? 'Telah berakhir' : $daysLeft.' hari lagi',
                    'created_at' => $kerjasama->created_at?->toISOString() ?? now()->toISOString(),
                ];
            })
            ->filter()
            ->values();
    }

    private static function mitraProcessNotifications(int $mitraId)
    {
        return DB::table('riwayat_status as rs')
            ->join('kerjasama as k', 'k.id_kerjasama', '=', 'rs.id_kerjasama')
            ->join('admins as a', 'a.id_admin', '=', 'rs.id_admin')
            ->where('k.id_mitra', $mitraId)
            ->where('rs.judul', '!=', null)
            // Hanya tampilkan notifikasi untuk status penting (id_status: 2=revisi, 3=disetujui, 4=ditolak, 5=dibatalkan)
            ->whereIn('rs.id_status', [2, 3, 4, 5])
            ->orderByDesc('rs.tanggal')
            ->limit(50)
            ->get([
                'rs.id_riwayat',
                'rs.tanggal',
                'rs.judul',
                'rs.id_status',
                'rs.catatan',
                'k.id_kerjasama',
                'k.judul as kerjasama_judul',
                'k.nomor_suratM',
                'k.nomor_suratP',
                'k.status_aktif',
                'a.nama',
            ])
            ->map(function ($row) {
                $nomor = $row->nomor_suratM ?: $row->nomor_suratP;
                $judul = $row->judul ?: $row->kerjasama_judul;
                
                // Map id_status ke nama
                $statusName = match($row->id_status) {
                    2 => 'revisi',
                    3 => 'disetujui',
                    4 => 'ditolak',
                    5 => 'dibatalkan',
                    default => 'proses',
                };
                
                // Format status untuk display
                $statusLabel = match($statusName) {
                    'revisi' => 'meminta revisi',
                    'disetujui' => 'menyetujui',
                    default => 'telah menambahkan',
                };

                return [
                    'id' => 'mitra-process-'.$row->id_riwayat,
                    'type' => 'process_update',
                    'status_type' => $statusName,
                    'title' => 'Admin '.$statusLabel.' proses kerjasama',
                    'message' => 'Admin ('.$row->nama.') '.$statusLabel.' proses "'.$judul.'" untuk kerjasama '.$row->kerjasama_judul.'. '
                        .($row->catatan ? 'Catatan: '.$row->catatan : ''),
                    'days_left' => null,
                    'kerjasama_id' => (int) $row->id_kerjasama,
                    'kerjasama_judul' => $row->kerjasama_judul,
                    'nomor_kerjasama' => $nomor,
                    'proses_judul' => $judul,
                    'jenis_status' => $statusName,
                    'status' => $row->status_aktif ?: 'Aktif',
                    'created_at' => Carbon::parse($row->tanggal)->toISOString(),
                ];
            })
            ->values();
    }

    private static function mitraStatusNotifications(int $mitraId)
    {
        return Kerjasama::query()
            ->finalized()
            ->where('id_mitra', $mitraId)
            ->with(['latestPeriode'])
            ->whereIn(DB::raw('LOWER(status_aktif)'), ['segera berakhir', 'berakhir'])
            ->get()
            ->map(function (Kerjasama $kerjasama) {
                $periode = $kerjasama->latestPeriode;
                if (! $periode?->tanggal_berakhir) {
                    return null;
                }

                $statusAktif = Str::lower((string) $kerjasama->status_aktif);
                $isExpired = $statusAktif === 'berakhir';
                $tanggalMulai = $periode->tanggal_mulai;
                $tanggalBerakhir = $periode->tanggal_berakhir;
                $endDate = Carbon::parse($tanggalBerakhir);
                $daysLeft = max(Carbon::today()->diffInDays($endDate, false), 0);
                $nomor = $kerjasama->nomor_suratM ?: $kerjasama->nomor_suratP;

                return [
                    'id' => 'mitra-status-'.$kerjasama->id_kerjasama,
                    'type' => $isExpired ? 'expired' : 'expiring_soon',
                    'status_type' => $isExpired ? 'expired' : 'expiring_soon',
                    'title' => $isExpired
                        ? 'Kerjasama Anda telah berakhir'
                        : 'Kerjasama Anda akan berakhir dalam '.$daysLeft.' hari',
                    'message' => $isExpired
                        ? 'Masa kerjasama dengan SETDA Boyolali telah berakhir pada tanggal '.$endDate->translatedFormat('d F Y').'.'
                        : 'Masa kerjasama dengan SETDA Boyolali akan berakhir pada tanggal '.$endDate->translatedFormat('d F Y').'.',
                    'days_left' => $isExpired ? null : $daysLeft,
                    'kerjasama_id' => (int) $kerjasama->id_kerjasama,
                    'kerjasama_judul' => $kerjasama->judul,
                    'nomor_kerjasama' => $nomor,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'status' => $isExpired ? 'Expired' : 'Aktif',
                    'created_at' => $kerjasama->created_at?->toISOString() ?? now()->toISOString(),
                ];
            })
            ->filter()
            ->values();
    }
}
