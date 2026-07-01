<?php

namespace Database\Seeders;

use App\Models\Kerjasama;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecalculateKerjasamaStatusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Hitung dan isi status_aktif berdasarkan tanggal berakhir untuk semua kerjasama
     */
    public function run(): void
    {
        $today = Carbon::today();

        Kerjasama::with('latestPeriode')->each(function ($kerjasama) use ($today) {
            $status = $kerjasama->status_aktif;
            if (strtolower($status ?? '') === 'dibatalkan' || $kerjasama->status_persetujuan === \App\Enums\StatusPersetujuan::Dibatalkan) {
                $kerjasama->update(['status_aktif' => 'Dibatalkan']);
                return;
            }

            $periode = $kerjasama->latestPeriode;

            if (! $periode || ! $periode->tanggal_berakhir) {
                $kerjasama->update(['status_aktif' => 'Aktif']);
                return;
            }

            $berakhir = Carbon::parse($periode->tanggal_berakhir);

            // Tentukan status berdasarkan tanggal
            if ($today->gte($berakhir)) {
                // Sudah lewat tanggal berakhir
                $status = 'Berakhir';
            } elseif ($today->diffInDays($berakhir, false) <= 90) {
                // Kurang dari atau sama dengan 90 hari
                $status = 'Segera Berakhir';
            } else {
                // Masih lama
                $status = 'Aktif';
            }

            $kerjasama->update(['status_aktif' => $status]);

            echo "ID: {$kerjasama->id_kerjasama}, Status: {$status}, Berakhir: " . $berakhir->format('Y-m-d') . ", Sisa: " . $today->diffInDays($berakhir, false) . " hari\n";
        });

        echo "\n✅ Status kerjasama sudah diperbaharui berdasarkan tanggal berakhir!\n";
    }
}