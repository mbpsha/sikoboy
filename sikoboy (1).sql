-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jun 2026 pada 18.06
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sikoboy`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `adendum`
--

CREATE TABLE `adendum` (
  `id_adendum` bigint(20) UNSIGNED NOT NULL,
  `id_kerjasama` bigint(20) UNSIGNED NOT NULL,
  `mitra` varchar(255) DEFAULT NULL,
  `tahun` varchar(4) DEFAULT NULL,
  `judul_adendum` varchar(255) NOT NULL,
  `nomor_surat_mitra_baru` varchar(255) DEFAULT NULL,
  `nomor_surat_pemerintah_baru` varchar(255) DEFAULT NULL,
  `nomor_surat_mitra_lama` varchar(255) DEFAULT NULL,
  `nomor_surat_pemerintah_lama` varchar(255) DEFAULT NULL,
  `urusan` varchar(255) DEFAULT NULL,
  `jangka_waktu` varchar(255) DEFAULT NULL,
  `jenis_kerjasama` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `pembiayaan` text DEFAULT NULL,
  `keterangan_adendum` text DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `lokasi_file` varchar(255) NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id_admin` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `divisi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id_admin`, `id_user`, `nama`, `divisi`) VALUES
(1, 1, 'Admin SIKOBOY', 'Administrator'),
(2, 7, 'Admin Utama', 'Admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('sikoboy-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:13;', 1782489894),
('sikoboy-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1782489894;', 1782489894);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen`
--

CREATE TABLE `dokumen` (
  `id_dokumen` bigint(20) UNSIGNED NOT NULL,
  `id_kerjasama` bigint(20) UNSIGNED NOT NULL,
  `id_riwayat` bigint(20) UNSIGNED DEFAULT NULL,
  `jenis_dokumen` enum('KSB','Nota Kesepakatan','Perjanjian Teknis','PKS','Rencana Kerja','MOU','RKT','LOI') NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `lokasi_file` varchar(255) NOT NULL,
  `versi_dokumen` int(11) NOT NULL,
  `tipe_dokumen` enum('admin','mitra') NOT NULL DEFAULT 'admin',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_kerjasama`
--

CREATE TABLE `kategori_kerjasama` (
  `id_kategori` bigint(20) UNSIGNED NOT NULL,
  `nama_kategori` enum('KSDD','KSDPK','NK/RK','PERTEK','KSDPL','KSDLL') NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `file_template` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori_kerjasama`
--

INSERT INTO `kategori_kerjasama` (`id_kategori`, `nama_kategori`, `deskripsi`, `file_template`) VALUES
(1, 'KSDD', 'Kerjasama daerah antar daerah', '-'),
(2, 'KSDPK', 'Kerjasama dengan pihak ketiga', '-'),
(3, 'NK/RK', 'Sinergi dengan pemerintah pusat atau lembaga', '-'),
(4, 'PERTEK', 'Perjanjian teknis', '-'),
(5, 'KSDPL', 'Kerjasama daerah dengan pemerintah daerah di luar negeri', '-'),
(6, 'KSDLL', 'Kerjasama daerah dengan lembaga di luar negeri', '-');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kerjasama`
--

CREATE TABLE `kerjasama` (
  `id_kerjasama` bigint(20) UNSIGNED NOT NULL,
  `id_mitra` bigint(20) UNSIGNED DEFAULT NULL,
  `id_admin` bigint(20) UNSIGNED NOT NULL,
  `id_kategori` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `nomor_suratM` varchar(255) DEFAULT NULL,
  `nomor_suratP` varchar(255) DEFAULT NULL,
  `urusan` enum('SEMUA URUSAN','PENDIDIKAN','KESEHATAN','PEKERJAAN UMUM DAN PENATAAN RUANG','PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN','KETENTRAMAN, KETERTIBAN UMUM DAN PERLINDUNGAN MASYARAKAT','SOSIAL','TENAGA KERJA','PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK','PANGAN','PERTANAHAN') NOT NULL,
  `jenis_kerjasama` varchar(255) DEFAULT NULL,
  `jenis_dokumen` varchar(255) DEFAULT NULL,
  `tipe` enum('mitra','pemerintah') NOT NULL DEFAULT 'mitra',
  `nama_pihak_luar` varchar(255) DEFAULT NULL,
  `pemrakarsa` enum('M','P') NOT NULL DEFAULT 'M',
  `daerah` varchar(255) NOT NULL,
  `status_aktif` varchar(255) NOT NULL,
  `jangka_waktu` int(10) UNSIGNED DEFAULT NULL,
  `is_finalized` tinyint(1) NOT NULL DEFAULT 0,
  `status_negosiasi` text DEFAULT NULL,
  `status_persetujuan` enum('disetujui','revisi','dibatalkan') DEFAULT NULL,
  `has_adendum_badge` tinyint(1) NOT NULL DEFAULT 0,
  `catatan_persetujuan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pembiayaan` enum('APBN','APBD','PIHAK KETIGA','PARA PIHAK','SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_mitras_table', 1),
(5, '0001_01_01_000004_create_admins_table', 1),
(6, '0001_01_01_000005_create_status_table', 1),
(7, '0001_01_01_000006_create_kategori_kerjasama_table', 1),
(8, '0001_01_01_000007_create_kerjasama_table', 1),
(9, '0001_01_01_000008_create_periode_kerjasama_table', 1),
(10, '0001_01_01_000009_create_dokumen_table', 1),
(11, '0001_01_01_000010_create_riwayat_status_table', 1),
(12, '2024_01_01_000011_update_kerjasama_for_admin_archive', 1),
(13, '2026_04_16_120000_remove_npwp_from_mitras_table', 1),
(14, '2026_04_16_120050_ensure_alamat_exists_on_mitras_table', 1),
(15, '2026_04_16_120100_update_nomor_surat_and_pemrakarsa_on_kerjasama_table', 1),
(16, '2026_04_16_120200_create_template_dokumen_table', 1),
(17, '2026_04_16_131550_create_potensi_table', 1),
(18, '2026_04_16_140200_extend_potensi_for_categories_and_points', 1),
(19, '2026_04_23_100000_add_status_verifikasi_to_users_table', 1),
(20, '2026_04_25_100000_add_kategori_and_status_to_template_dokumen_table', 1),
(21, '2026_04_29_031410_create_peraturans_table', 1),
(22, '2026_05_04_220000_update_status_persetujuan_values_on_kerjasama_table', 1),
(23, '2026_05_04_232500_add_penanggung_jawab_to_riwayat_status_table', 1),
(24, '2026_05_05_152336_create_adendum_table', 1),
(25, '2026_05_09_000000_make_mitra_fields_nullable', 1),
(26, '2026_05_10_120000_add_is_active_to_users_table', 1),
(27, '2026_05_10_142500_add_jangka_waktu_to_kerjasama_table', 1),
(28, '2026_05_11_000000_fix_status_aktif_column', 1),
(29, '2026_05_13_000002_add_pembiayaan_to_kerjasama_table_again', 1),
(30, '2026_05_13_040000_expand_adendum_fields', 1),
(31, '2026_05_15_000000_add_judul_to_riwayat_status_table', 1),
(32, '2026_05_16_000000_add_file_to_riwayat_status_table', 1),
(33, '2026_05_16_154259_add_tipe_to_dokumens_table', 1),
(34, '2026_05_16_160000_add_tipe_dokumen_to_dokumen_table', 1),
(35, '2026_05_24_000000_add_judul_and_deskripsi_to_template_dokumen_table', 1),
(36, '2026_05_25_111613_add_nama_file_to_peraturans_table', 2),
(37, '2026_06_12_134544_add_id_riwayat_to_dokumen_table', 3),
(38, '2026_06_01_000000_add_missing_judul_and_deskripsi_to_template_dokumen_table', 4),
(39, '2026_06_04_050000_add_has_adendum_badge_to_kerjasama_table', 4),
(40, '2026_06_24_000000_add_dibatalkan_to_status_table', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mitras`
--

CREATE TABLE `mitras` (
  `id_mitra` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `no_handphone` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mitras`
--

INSERT INTO `mitras` (`id_mitra`, `id_user`, `nama_perusahaan`, `no_handphone`, `pic`, `alamat`) VALUES
(1, 2, 'PT Contoh Mitra Indonesia', '081234567891', 'Budi Santoso', 'Jl. Contoh No. 123, Boyolali'),
(2, 3, 'CV Mitra Sejahtera', '081234567892', 'Siti Nurhaliza', 'Jl. Merdeka No. 45, Boyolali'),
(3, 4, 'PT Belum Verifikasi', '081234567893', 'Ahmad Dahlan', 'Jl. Pemuda No. 78, Boyolali'),
(4, 5, 'tempe', '08912345678', 'muha', 'joho'),
(5, 6, 'Las Lus Enisilus', '08912345678', 'Adam', 'Jalan King Nasir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `peraturans`
--

CREATE TABLE `peraturans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `periode_kerjasama`
--

CREATE TABLE `periode_kerjasama` (
  `id_periode` bigint(20) UNSIGNED NOT NULL,
  `id_kerjasama` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `potensi`
--

CREATE TABLE `potensi` (
  `id_potensi` bigint(20) UNSIGNED NOT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar_path` varchar(255) DEFAULT NULL,
  `status_tampil` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `potensi_poin`
--

CREATE TABLE `potensi_poin` (
  `id_potensi_poin` bigint(20) UNSIGNED NOT NULL,
  `id_potensi` bigint(20) UNSIGNED NOT NULL,
  `isi` text NOT NULL,
  `urutan` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_status`
--

CREATE TABLE `riwayat_status` (
  `id_riwayat` bigint(20) UNSIGNED NOT NULL,
  `id_kerjasama` bigint(20) UNSIGNED NOT NULL,
  `id_status` bigint(20) UNSIGNED NOT NULL,
  `id_admin` bigint(20) UNSIGNED NOT NULL,
  `catatan` varchar(255) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `penanggung_jawab` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0m108j35eL9Yv8su4Lbj8dTBDhIK3uHy8pgq5Sgv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbmxJUXpCeE5VbzROa1J1U1hOSWIyeEZRbXB1TTFFOVBTSXNJblpoYkhWbElqb2lVVnBKT1dOQ04wUjJiak00WVV4MlFsUjRSbmhoZEdFdk5UQmFPVk5YVnpnMEsySnVkRUU1S3pGbWMwTjZSV28xV1V4WlZsSjNPR3A0UjI1b1IydFFiRTFaYlROR1ZFeFpaa0pDYmsxVVYzcFlTME5NV1RodFlqRmxiWEpJYURkU2VHUktXa1ZHTDBRcmJDdGpNbTFOTUhWcmNYcHNaRGxUUjBwUFVIUklVbmh1WVZKQ1YzQTBObnBWV0ZsbmVFUnpUQzk2WmpZMWEzVklPSFZrYVd0a2MxTm9URzVUYldNek5uZEhlbkl2V2s1U1dqWXJTMVoxZVVrMWNXVkdPRVpLTTFZd2FYRlNZM2x6WmtGcWQwdFdVbFJPU0VGRVMydERUSHB1ZFVkVldYSlVUMFExVGxWNFNscHJPVTh4T1d0a1RFZHJURkZKUkRkMlJHbFJkbXhJV0ZseVZuTTViR3ROYkdZdmVtUkNjRFJ4VVVJMmVHTkZMM1o0YlU5V1MwbHdTVTkwYlZwQlQzSkNWbXM5SWl3aWJXRmpJam9pWlRaak9UWTBPR1V3WVdZek1EazBZMlpqTVRabVlXRXhaalZsTVdFd05UUmlNek0wTjJSalltUTRNVGM1TnpRMFlUUTRZamRrTmprd1lUQXpPV1UzWWlJc0luUmhaeUk2SWlKOQ==', 1782488835),
('buMODlPLWr71c7Mdeh83koQsVl86ZYARAbycruee', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbUV6ZDAxNEszRkRiR3RYUWpKSk1ISjRObEZtZFZFOVBTSXNJblpoYkhWbElqb2lRMUUxU1RkVlNXWkViV3BZWkV3NU16a3hiRmxCWWpWdFYwTXJTRVlyTW00MWVtVm9WSGhQYWpSV00yVjVkR3A2VDFack1ubHJiM2d4YjBWNVJVVlpVVVJ2T0VsRFIzSlZkRWNyVUdsUldtRjNhR3BHU3pOSWFGcDZTRmhaTm1reVZUSlFZVTlMZUZGd1ZXOVdSSFp1VDBsRVV6TklTRXgxVm5OelQxVmhXR2RsVDFvekwwWlRZVkZPZEdNMk0wZGxUVmR3UjNBM04yeDZORmwyYTBOQlJVc3ZjMDVHVm5wa05EZHJOMHd5ZURjd2NHNUpZMWxIZURKS1Z5OUZPVE5pU2tsbGRrRklWMDlPSzNocWFXMDJZMGhFV1ZJMWEwbHdVSGR6WW5SQ1lXTk1ZbkZCT0hkMVEzcFBNbFpaYkdKWFZVeG5VbEJJY3pCR09HdElhVGN5VGt0Tlp6ZzRWM28zZWtFclZXNUhlRGRTZFZJeFRYQTJZWHBDWW5OQ1RGcDFNbk00Y2pCaGQwUnlka2htVlRWcVZuTnRiMVpKU0hoa2RWa3JaV1pUUzNkMmR6ZEVVa3RvV1RWbVN5dHBibFl5YjFoM1dVOUhkMHRaZDBsbFNGSlRaVGt4T1U1eU5UUlVNVmxwT0hobGRrbFViVUpNWTJsa1FXTnBUMWhGWlRscWMyOVZValJEYXpWVFVHdEJXbEp5TmpjdlNqVnplRzFtWm1KTWNVTXhPWGxPTjFoM0szVnFNRkl6YnowaUxDSnRZV01pT2lJMU5UazNaRGd6TjJZNE4yRTFZV1prTkdWallqazBZelUwWWpCaU1EVmpNV015Wm1WbVpEYzNNekpoWVdOalpqSmpaamd4WldZek5URXlaR1kyTVRBd0lpd2lkR0ZuSWpvaUluMD0=', 1782456063),
('KJL7CgX3sfpR449cD6pdTFPQTBFI9dqw8E10LKfz', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbGd2Y2psaVlscElXVGQzVjFVeFNHVTJaSE5aUW1jOVBTSXNJblpoYkhWbElqb2laemhMWW1NMGVuQnhWVEJqVjNaeGNsUm5OWEZ3ZUN0VVJuTlNPVXd6VjAxb1UwRTFUVmhWWW5CYWFsZFRPV2hyTlVsNlJYQjNWSFV2VFdkdmJtUlVhVU5EUVRWSGVFcFBWV1pDTjJOTlVWbENSalEwTjA5a2NraE5kazVMVW1kcmJHWjFRMWd2UWpCclJIYzFXV3QxZG5ndmJGVnBOeXRaV0ZoMFVHRlpMMHRFWkRSSVJqSTFaR1pFU1hkaGRHaFVORFp5Y2pBMmJGZG9TMDVGTVhkQmNUSkllRVJDWnl0NVVTOUxWbVJZY1UxYVNFWTJaR1ZzY1ROM1ZqUXJTakJ3VFVnd1EzQnpXRVpNWVU5SmNtTktXVU41T0VkVVZWcHZaaTl0VUhOc09USTRTbTVoUVdWYWJXcE1WVVJxYWxodGJXMTZkMEpvYld3MmRVbHVZV3hOZDNGMU5GZFJWbTVKVFcxUU9UQnlkR0ZWU2xZcmRIbHVTMXBqUVM4M1lXczJMM0JxY2pWWVFVRmhWVzE2VEZrelZXNXpOVTAzWm1jNUwwOTNNMWswZDNGM1RtWlFZbTF5ZFU0d01UY3hlbWRsYkdOU1ltdElWbEJYTWpKSVZGZDNURk00ZEdwU1FsVTVXbFV3T1hoeWNreDNWVXRqTmpSYVJEa3pVMFJVTVRaV2JIcHpMMHR2Y1ZsMFZUVkRWVU52YzBkT2N6RlRLMGhMTm1KUFpYaEphbEYxTVhoelYyZ3JRblp0UlhSeFR6WkVkWHBCZDFKc1NYZDBiR3N6T0VsT1UzTnZjRmhLTWpaRmVsUjRVM2s1UkU1WmJHVm5Za0ZYYVVGTGNUbEVlVkpWVTJaQmVsQnNhREl2U0VFOUlpd2liV0ZqSWpvaVl6azJaRE5sTm1KaE5UUmpOemN4TjJJM05UYzRNbUZrWkdNeE16SXhZV1kyTkRkaVpXWmhNemRrTWpJMlpqVTFZemN6TUdJeU1qZzJaV015WTJZeE55SXNJblJoWnlJNklpSjk=', 1782489879),
('MDguzS5m7r7zLdgfDH8EdN3elm968sHGzHhxqK1O', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbEpDYTJJemRESnhSRkJVUjBGM1N6SkRZalIyZFhjOVBTSXNJblpoYkhWbElqb2lNVkU1YzFwcGJtSTFhVVZTTkZGTVRtUkVUMEoyV0dSUk1YRmxSRkZuVm1kbFZHMXNXbFZ4U0dkbFYxTTVNVzB2Um5GQlQyOTVjRzQxUVdOTmJVeG1OR3BYYkRBM1ZHazVlSGQwUVZGWGVuVnFXSEZYTWxnMlpYTXhhWFY2WWpsNmIycExVRkZLUVhSdVNuUm5jV3hqVm5SR00wRXpVVmsyT0VoemIyOXpZM1JrZVhrNFRFRjBlRVJrZVZoRGFHOWljMjVSVW1sbk9YSmtRelZZTmtodkwyVmthV2M0WkRWM1RWcFNVRkJLYTNkc2FsaDFORUYwZVROamRtZFRVbTV5WWxaS05tVnhZek40TmxSV1NsVm5ZbFYyWWpCWFVqWTBNVkJCTXpVdldYTkRjemxxVWxwRFVWZEhTM0JWYjJreGVISm5WbXdyVVRWNU1ISkRkblIzVTBrNFRWZFdSVWhxTkV4b1NuVjZhRmR1TkhocFFWQmFkRFJ0WVZoM2RtdDZhVXRXY1RWMk1IWjBUazVHTjNObU9VOWpWRVZFV2pnMlFqWldOMkZzT1daemNHWjRTRkZtWmpCbk9HRm9iRGQ2Um1ObmEybE5PSFF2VUM5RVMwSkJRV3BOVjNadmNWUjRkbGswWjB0R1dYRjViVGxCV1M5RU5tTk1lRTVSZUVwU1ExaGtOelZwWjBsVFVUSkNXSFJHZWpsQ1ZHSkVkSEoxYzFkNFlWbHhOMVE1V2prNGNVcGtaazlTWkhwTFUybHhVVU50TXpnNFV6VmtZVFEyV25sSWVrbFFjRXRUSzBSS1JuZFhRa01yVGt0TU5UWXJSV1o0TVRWbVFsVkVSMFp6VGxwRVIycDNiVm80ZWtaNU1ESXpVamxRWm14SlprbHFVMmhtTUN0SFMwaEZkVlo0VlVOalJWbEJSM3B0TUhKamMyWTNWM05uUFQwaUxDSnRZV01pT2lKaU56ZzNNV1F3TXpabU0yWTVZVE14TVRjeFpEa3lZVGxqTTJKalpUZzVOR1k1WkdRMFpUUmxOMlZpWkRJek1XTTNOVFU0WlRZME9XTmlOalJoTlRFeklpd2lkR0ZuSWpvaUluMD0=', 1782456438),
('T9KBAWnR7ccMq7gAHk3kTSSBF2nqLGL3ZejHvgl2', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJakZRT1hoS2QySmtTRXc1UjFWbVJIaHVTa3d2Wm5jOVBTSXNJblpoYkhWbElqb2lUM2hIUVdZME56TkViQ3MzYkVKU0sySjVSMUpHUVcxRVUwcG9VR3BJYkROc1RrOTVPVFUwT0ZWRWNscE5hVGhUTW1KcmRVNUhNMFZYTlVvcmJtUmlVRWhEVG5vNWRtNUVNMUZqVFdGamFuUmFXa2xQU201WVJ6aGtRMVptTW1kQmJuaDJiR2x2U2xoR09HVkNUVU5pV1RBeksxRTBiVGxCTms5dEwxZDBhRkphTTNSbloxbFJSaXRCWTFVM09GSTBPWGxvYWpCTE16Y3lTRGxCZUVKbVJYaFBUVWx5YkRaVkswTXlaa1F6VVhKc2FrSjBUa1pVVjNWa2NWWnRSMnhWTTNsM01XNTZVa1pUZW5Oa00wcDBPVm96Ym1OWWVrRlJUVmg0ZG5ZelVUZHhTbUZxZUdNeFVuWnFaelZVVW1neVZHRnFVRTFKVWxKUVpEZ3lUeXR2TjB0VGNUZHlPVmxNYlV0SmQxRnpTa3BZVVU1dGJWbEdWMFJwUTBjeWFXaEVZMmNyZG1Zd2FGTkZRWEk1WlZCcWRXMTNlRm8xVEZOcFNISjNlRGREZEN0NlpETXJSMmN2WXk4dmIwSmhWV28wV0ROeFZtSkpkRVJsTDJKSWJVeHlNbkpMWTJkR2VYY3lSWEF2TkdwdVZWVnhPWFkzVUdneWVYRlFaalIwYUhaNUlpd2liV0ZqSWpvaU5XTTNNRFk1TlRFMU9UazJNVFF6TVRkaFpHTXlNbUl6WVRVd01HUXdNekF6WmpjMlptSXlaR1JrTjJFNE1EVXlaamc1TmpObU1XUTBOREEwWWpRd01pSXNJblJoWnlJNklpSjk=', 1782489722);

-- --------------------------------------------------------

--
-- Struktur dari tabel `status`
--

CREATE TABLE `status` (
  `id_status` bigint(20) UNSIGNED NOT NULL,
  `jenis_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `status`
--

INSERT INTO `status` (`id_status`, `jenis_status`) VALUES
(1, 'Aktif'),
(2, 'Segera Berakhir'),
(3, 'Berakhir'),
(4, 'diajukan'),
(5, 'disetujui'),
(6, 'revisi'),
(7, 'proses'),
(8, 'Dibatalkan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `template_dokumen`
--

CREATE TABLE `template_dokumen` (
  `id_template_dokumen` bigint(20) UNSIGNED NOT NULL,
  `id_admin` bigint(20) UNSIGNED NOT NULL,
  `id_kategori` bigint(20) UNSIGNED DEFAULT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `jenis_dokumen` varchar(255) DEFAULT NULL,
  `lokasi_file` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mitra') NOT NULL DEFAULT 'mitra',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status_verifikasi` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'disetujui',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `email`, `password`, `role`, `is_active`, `status_verifikasi`, `created_at`, `updated_at`) VALUES
(1, 'AdminSikoboy123@admin.com', '$2y$12$Stk3DG7K/0ORUb0LO.opueXKdcZSK4Sa6fWNFJrl/VbDj/WcKkVw2', 'admin', 1, 'disetujui', '2026-05-24 21:13:38', '2026-05-24 21:13:38'),
(2, 'mitra@example.com', '$2y$12$vtGlEOtwIAn3QftvPDuyu.m5ElHxJAMjfB79SnzoLLe5/irwePJm2', 'mitra', 1, 'disetujui', '2026-05-24 21:13:38', '2026-05-24 21:13:38'),
(3, 'partner2@example.com', '$2y$12$1A6kFBmod6SFkCjkQa8epOw8T6hLaevV24p.EulVYb7fJkayDvwye', 'mitra', 1, 'disetujui', '2026-05-24 21:13:39', '2026-05-24 21:13:39'),
(4, 'unverified@example.com', '$2y$12$UNmHTSwhHaZAFuAP8tkN3O/MezU.tj6Wg1sjA0DpSnsfBEeHOfLjS', 'mitra', 1, 'pending', '2026-05-24 21:13:39', '2026-05-24 21:13:39'),
(5, 'bagasukocok123@gmail.com', '$2y$12$SZhbIvhYZlYzIMayGeBx9.fYyN861NcJrnUzh7/91fwMiIh40uPaO', 'mitra', 1, 'disetujui', '2026-05-24 21:20:08', '2026-05-24 21:20:48'),
(6, 'nhilmya@gmail.com', '$2y$12$RDVpXB9myS4LdNrR/xr7UeUA/npFKnbPGiTMUaLEoZPpsCnKB79Hm', 'mitra', 1, 'disetujui', '2026-06-26 04:43:19', '2026-06-26 04:43:19'),
(7, 'AdminSikarsaBYL@admin.com', '$2y$12$kFaWogRyHotjwjdMDR3ziOsimT30Hn6zTsrKBy3YyHfLduLSqNTCW', 'admin', 1, 'disetujui', '2026-06-26 16:03:54', '2026-06-26 16:03:54');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `adendum`
--
ALTER TABLE `adendum`
  ADD PRIMARY KEY (`id_adendum`),
  ADD KEY `adendum_id_kerjasama_index` (`id_kerjasama`);

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `admins_id_user_unique` (`id_user`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id_dokumen`),
  ADD KEY `dokumen_id_kerjasama_index` (`id_kerjasama`),
  ADD KEY `dokumen_id_riwayat_foreign` (`id_riwayat`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori_kerjasama`
--
ALTER TABLE `kategori_kerjasama`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `kerjasama`
--
ALTER TABLE `kerjasama`
  ADD PRIMARY KEY (`id_kerjasama`),
  ADD KEY `kerjasama_id_admin_foreign` (`id_admin`),
  ADD KEY `kerjasama_id_mitra_id_admin_index` (`id_mitra`,`id_admin`),
  ADD KEY `kerjasama_id_kategori_index` (`id_kategori`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mitras`
--
ALTER TABLE `mitras`
  ADD PRIMARY KEY (`id_mitra`),
  ADD UNIQUE KEY `mitras_id_user_unique` (`id_user`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`),
  ADD KEY `password_reset_tokens_token_index` (`token`);

--
-- Indeks untuk tabel `peraturans`
--
ALTER TABLE `peraturans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `periode_kerjasama`
--
ALTER TABLE `periode_kerjasama`
  ADD PRIMARY KEY (`id_periode`),
  ADD KEY `periode_kerjasama_id_kerjasama_index` (`id_kerjasama`);

--
-- Indeks untuk tabel `potensi`
--
ALTER TABLE `potensi`
  ADD PRIMARY KEY (`id_potensi`),
  ADD KEY `potensi_kategori_index` (`kategori`);

--
-- Indeks untuk tabel `potensi_poin`
--
ALTER TABLE `potensi_poin`
  ADD PRIMARY KEY (`id_potensi_poin`),
  ADD KEY `potensi_poin_id_potensi_urutan_index` (`id_potensi`,`urutan`);

--
-- Indeks untuk tabel `riwayat_status`
--
ALTER TABLE `riwayat_status`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `riwayat_status_id_status_foreign` (`id_status`),
  ADD KEY `riwayat_status_id_admin_foreign` (`id_admin`),
  ADD KEY `riwayat_status_id_kerjasama_id_status_index` (`id_kerjasama`,`id_status`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id_status`);

--
-- Indeks untuk tabel `template_dokumen`
--
ALTER TABLE `template_dokumen`
  ADD PRIMARY KEY (`id_template_dokumen`),
  ADD KEY `template_dokumen_id_admin_foreign` (`id_admin`),
  ADD KEY `template_dokumen_kategori_active_idx` (`id_kategori`,`is_active`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_email_index` (`email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_status_verifikasi_index` (`status_verifikasi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `adendum`
--
ALTER TABLE `adendum`
  MODIFY `id_adendum` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id_dokumen` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori_kerjasama`
--
ALTER TABLE `kategori_kerjasama`
  MODIFY `id_kategori` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kerjasama`
--
ALTER TABLE `kerjasama`
  MODIFY `id_kerjasama` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `mitras`
--
ALTER TABLE `mitras`
  MODIFY `id_mitra` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `peraturans`
--
ALTER TABLE `peraturans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `periode_kerjasama`
--
ALTER TABLE `periode_kerjasama`
  MODIFY `id_periode` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `potensi`
--
ALTER TABLE `potensi`
  MODIFY `id_potensi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `potensi_poin`
--
ALTER TABLE `potensi_poin`
  MODIFY `id_potensi_poin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `riwayat_status`
--
ALTER TABLE `riwayat_status`
  MODIFY `id_riwayat` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `status`
--
ALTER TABLE `status`
  MODIFY `id_status` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `template_dokumen`
--
ALTER TABLE `template_dokumen`
  MODIFY `id_template_dokumen` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `adendum`
--
ALTER TABLE `adendum`
  ADD CONSTRAINT `adendum_id_kerjasama_foreign` FOREIGN KEY (`id_kerjasama`) REFERENCES `kerjasama` (`id_kerjasama`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_id_kerjasama_foreign` FOREIGN KEY (`id_kerjasama`) REFERENCES `kerjasama` (`id_kerjasama`) ON DELETE CASCADE,
  ADD CONSTRAINT `dokumen_id_riwayat_foreign` FOREIGN KEY (`id_riwayat`) REFERENCES `riwayat_status` (`id_riwayat`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kerjasama`
--
ALTER TABLE `kerjasama`
  ADD CONSTRAINT `kerjasama_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `kerjasama_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_kerjasama` (`id_kategori`),
  ADD CONSTRAINT `kerjasama_id_mitra_foreign` FOREIGN KEY (`id_mitra`) REFERENCES `mitras` (`id_mitra`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `periode_kerjasama`
--
ALTER TABLE `periode_kerjasama`
  ADD CONSTRAINT `periode_kerjasama_id_kerjasama_foreign` FOREIGN KEY (`id_kerjasama`) REFERENCES `kerjasama` (`id_kerjasama`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `potensi_poin`
--
ALTER TABLE `potensi_poin`
  ADD CONSTRAINT `potensi_poin_id_potensi_foreign` FOREIGN KEY (`id_potensi`) REFERENCES `potensi` (`id_potensi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat_status`
--
ALTER TABLE `riwayat_status`
  ADD CONSTRAINT `riwayat_status_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `riwayat_status_id_kerjasama_foreign` FOREIGN KEY (`id_kerjasama`) REFERENCES `kerjasama` (`id_kerjasama`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_status_id_status_foreign` FOREIGN KEY (`id_status`) REFERENCES `status` (`id_status`);

--
-- Ketidakleluasaan untuk tabel `template_dokumen`
--
ALTER TABLE `template_dokumen`
  ADD CONSTRAINT `template_dokumen_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `template_dokumen_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_kerjasama` (`id_kategori`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
