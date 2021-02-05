-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Feb 2021 pada 07.58
-- Versi server: 10.4.17-MariaDB
-- Versi PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `baratha_accounting_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `kode_barang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_awal` decimal(12,2) DEFAULT 0.00,
  `saldo_awal` decimal(13,2) DEFAULT 0.00,
  `stock` decimal(12,2) DEFAULT 0.00,
  `saldo` decimal(13,2) DEFAULT 0.00,
  `exp_date` date DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_penyimpanan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minimum_stock` decimal(12,2) DEFAULT 0.00,
  `id_kategori` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`kode_barang`, `nama`, `satuan`, `stock_awal`, `saldo_awal`, `stock`, `saldo`, `exp_date`, `keterangan`, `tempat_penyimpanan`, `minimum_stock`, `id_kategori`, `created_at`, `updated_at`) VALUES
('BR0001', 'Gas 3kg', 'pcs', NULL, NULL, '20.00', '300000.00', NULL, NULL, NULL, NULL, 1, '2021-01-25 16:42:18', '2021-02-02 06:37:37'),
('BR0002', 'Kompor', 'pcs', NULL, NULL, '5.00', '2500000.00', NULL, NULL, NULL, NULL, 1, '2021-01-25 16:42:36', '2021-02-05 06:42:00'),
('BR0003', 'Spatula', 'pcs', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, NULL, 1, '2021-01-25 17:48:07', '2021-02-02 06:26:09'),
('BR0004', 'Wajan', 'pcs', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, NULL, 2, '2021-01-25 17:48:27', '2021-02-02 06:42:53'),
('BR0005', 'Sampo Hotel', 'kardus', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, NULL, 3, '2021-01-25 17:57:40', '2021-01-25 18:12:28'),
('BR0006', 'Sabun Hotel', 'kardus', NULL, NULL, '0.00', '0.00', NULL, NULL, NULL, NULL, 3, '2021-01-25 17:57:55', '2021-01-25 18:09:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer`
--

CREATE TABLE `customer` (
  `kode_customer` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `piutang` decimal(13,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pemakaian_barang`
--

CREATE TABLE `detail_pemakaian_barang` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_pemakaian` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_barang` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `subtotal` decimal(13,2) NOT NULL,
  `kode_biaya` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pembelian_barang`
--

CREATE TABLE `detail_pembelian_barang` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_pembelian` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_barang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_satuan` decimal(13,2) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `subtotal` decimal(13,2) NOT NULL,
  `ppn` decimal(13,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_pembelian_barang`
--

INSERT INTO `detail_pembelian_barang` (`id`, `kode_pembelian`, `kode_barang`, `harga_satuan`, `qty`, `subtotal`, `ppn`, `created_at`, `updated_at`) VALUES
(9, 'PB0221-0001', 'BR0001', '15000.00', '20.00', '300000.00', '0.00', '2021-02-02 06:37:37', '2021-02-02 06:37:37'),
(11, 'PB0221-0002', 'BR0002', '500000.00', '5.00', '2500000.00', '0.00', '2021-02-05 06:42:00', '2021-02-05 06:42:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kartu_hutang`
--

CREATE TABLE `kartu_hutang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `kode_supplier` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_transaksi` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(13,2) NOT NULL,
  `tipe` enum('Pembelian','Pembayaran') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kartu_hutang`
--

INSERT INTO `kartu_hutang` (`id`, `tanggal`, `kode_supplier`, `kode_transaksi`, `nominal`, `tipe`, `created_at`, `updated_at`) VALUES
(4, '2021-02-02', 'SP0001', 'PB0221-0001', '300000.00', 'Pembelian', '2021-02-02 06:37:37', '2021-02-02 06:42:53'),
(5, '2021-02-05', 'SP0002', 'PB0221-0002', '2500000.00', 'Pembelian', '2021-02-05 06:41:59', '2021-02-05 06:41:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kartu_stock`
--

CREATE TABLE `kartu_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `kode_barang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_transaksi` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_detail` int(11) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `nominal` decimal(13,2) NOT NULL,
  `tipe` enum('Masuk','Keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kartu_stock`
--

INSERT INTO `kartu_stock` (`id`, `tanggal`, `kode_barang`, `kode_transaksi`, `id_detail`, `qty`, `nominal`, `tipe`, `created_at`, `updated_at`) VALUES
(8, '2021-02-02', 'BR0001', 'PB0221-0001', 9, '20.00', '300000.00', 'Masuk', '2021-02-02 06:37:37', '2021-02-02 06:37:37'),
(10, '2021-02-05', 'BR0002', 'PB0221-0002', 11, '5.00', '2500000.00', 'Masuk', '2021-02-05 06:42:00', '2021-02-05 06:42:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori_barang`
--

INSERT INTO `kategori_barang` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Alat Masaks', '2021-01-19 02:40:36', '2021-01-19 02:41:57'),
(2, 'Alat Dapur', '2021-01-19 02:40:46', '2021-01-19 02:40:46'),
(3, 'Perlengkapan Hotel', '2021-01-19 02:40:57', '2021-01-19 02:40:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kode_biaya`
--

CREATE TABLE `kode_biaya` (
  `kode_biaya` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_rekening` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kode_biaya`
--

INSERT INTO `kode_biaya` (`kode_biaya`, `nama`, `kode_rekening`, `created_at`, `updated_at`) VALUES
('OPRSNL', 'Biaya Operasional', '5110.1101', '2021-01-19 23:40:45', '2021-01-19 23:40:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kode_induk`
--

CREATE TABLE `kode_induk` (
  `kode_induk` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kode_induk`
--

INSERT INTO `kode_induk` (`kode_induk`, `nama`, `created_at`, `updated_at`) VALUES
('1110', 'Kas', '2021-01-18 10:48:59', '2021-01-18 10:52:10'),
('1120', 'Bank', '2021-01-18 23:22:22', '2021-01-18 23:22:22'),
('5110', 'Biaya Operasional', '2021-01-19 23:32:30', '2021-01-19 23:32:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kode_rekening`
--

CREATE TABLE `kode_rekening` (
  `kode_rekening` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Debet','Kredit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo_awal` decimal(13,2) DEFAULT 0.00,
  `kode_induk` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kode_rekening`
--

INSERT INTO `kode_rekening` (`kode_rekening`, `nama`, `tipe`, `saldo_awal`, `kode_induk`, `created_at`, `updated_at`) VALUES
('1110.1110', 'Kas Baratha', 'Debet', '0.00', '1110', '2021-01-18 23:23:18', '2021-01-18 23:39:30'),
('1120.1110', 'Bank BCA', 'Debet', NULL, '1120', '2021-01-18 23:24:49', '2021-01-18 23:24:49'),
('5110.1101', 'Biaya Produksi', 'Debet', '0.00', '5110', '2021-01-19 23:33:15', '2021-01-19 23:33:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kunci_transaksi`
--

CREATE TABLE `kunci_transaksi` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `jenis_transaksi` enum('Pembelian','Pemakaian','Penjualan','Kas','Bank','Memorial') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_kunci` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2021_01_18_150611_create_sessions_table', 1),
(7, '2021_01_18_172424_create_kode_induk_table', 2),
(9, '2021_01_18_181452_create_perusahaan_table', 3),
(11, '2021_01_18_184456_create_kode_rekening_table', 4),
(12, '2021_01_19_090936_create_kategori_barang_table', 5),
(14, '2021_01_19_094622_create_barang_table', 6),
(15, '2021_01_19_194740_create_supplier_table', 7),
(16, '2021_01_20_061925_create_kode_biaya_table', 8),
(17, '2021_01_20_210607_create_customer_table', 9),
(19, '2021_01_23_210710_create_pembelian_barang_table', 10),
(20, '2021_01_23_215709_create_detail_pembelian_barang_table', 11),
(21, '2021_01_23_230947_add_harga_satuan', 12),
(22, '2021_01_23_214626_create_kunci_transaksi_table', 13),
(23, '2021_01_24_044400_add_ppn_satuan', 13),
(24, '2021_01_26_141756_create_kartu_stock_table', 14),
(25, '2021_01_26_142421_create_kartu_hutang_table', 14),
(26, '2021_01_26_185129_customize_kartu_hutang_table', 15),
(28, '2021_02_02_144545_create_pemakaian_barang_table', 16),
(29, '2021_02_02_145107_create_detail_pemakaian_barang_table', 17);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemakaian_barang`
--

CREATE TABLE `pemakaian_barang` (
  `kode_pemakaian` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `total_qty` decimal(12,2) NOT NULL,
  `total_pemakaian` decimal(13,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembelian_barang`
--

CREATE TABLE `pembelian_barang` (
  `kode_pembelian` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_supplier` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `status_ppn` enum('Tanpa','Belum','Sudah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tanpa',
  `jatuh_tempo` date DEFAULT NULL,
  `total_qty` decimal(12,2) NOT NULL,
  `total` decimal(13,2) NOT NULL,
  `total_ppn` decimal(13,2) DEFAULT 0.00,
  `grandtotal` decimal(13,2) NOT NULL,
  `terbayar` decimal(13,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembelian_barang`
--

INSERT INTO `pembelian_barang` (`kode_pembelian`, `kode_supplier`, `tanggal`, `status_ppn`, `jatuh_tempo`, `total_qty`, `total`, `total_ppn`, `grandtotal`, `terbayar`, `created_at`, `updated_at`) VALUES
('PB0221-0001', 'SP0001', '2021-02-02', 'Tanpa', NULL, '20.00', '300000.00', '0.00', '300000.00', '0.00', '2021-02-02 06:37:36', '2021-02-02 06:42:53'),
('PB0221-0002', 'SP0002', '2021-02-05', 'Tanpa', NULL, '5.00', '2500000.00', '0.00', '2500000.00', '0.00', '2021-02-05 06:41:59', '2021-02-05 06:41:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perusahaan`
--

INSERT INTO `perusahaan` (`id`, `nama`, `alamat`, `kota`, `provinsi`, `telepon`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Baratha Hotel And Coffee', 'Jl. Saliwiryo Pranowo Gg. Taman No.11, Pattian, Kotakulon, Kec. Bondowoso', 'Bondowoso', 'Jawa Timur', '0332-', 'barathahotel@baratha.com', '2021-01-18 11:23:34', '2021-01-18 11:40:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('fWvLtee4tNZl2EEXMoWI5F0q0kBDQxpMVQAzR9js', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiUElqZTRDUVYwNks3QVBZNDFnUGJ6VWtOZk9JT3hFZjI5aTFzMlBKYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTAkcjFaSWlQOVdPMDBHMHNZeEticDIyZXVjRUE2Rm1TVTVGb2J1QzkwbXJGLkdwZGZXdDBKM0siO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEwJHIxWklpUDlXTzAwRzBzWXhLYnAyMmV1Y0VBNkZtU1U1Rm9idUM5MG1yRi5HcGRmV3QwSjNLIjt9', 1612445061),
('PFJOvvqvsWi2cZvJJ6iRo6LqjKjwIwmjlviXQLYH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiR291M0RmdHRZREVFc0ZWOUp6czR6TFRJT1RBUVRtTUNNQmVGMFl6ciI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcGVyc2VkaWFhbi9iYXJhbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTAkcjFaSWlQOVdPMDBHMHNZeEticDIyZXVjRUE2Rm1TVTVGb2J1QzkwbXJGLkdwZGZXdDBKM0siO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEwJHIxWklpUDlXTzAwRzBzWXhLYnAyMmV1Y0VBNkZtU1U1Rm9idUM5MG1yRi5HcGRmV3QwSjNLIjt9', 1612262110),
('U8vAzFMwrr7tgBIyPnTo7PeHOeMS0sdfeRppuVBh', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiOVpTdFo2RnhnbER2Vko4ejNkQlBieGYxUVo3TVV4WUZvc0pjS0ZkUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wZXJzZWRpYWFuL3BlbWFrYWlhbi1iYXJhbmcvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEwJHIxWklpUDlXTzAwRzBzWXhLYnAyMmV1Y0VBNkZtU1U1Rm9idUM5MG1yRi5HcGRmV3QwSjNLIjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMCRyMVpJaVA5V08wMEcwc1l4S2JwMjJldWNFQTZGbVNVNUZvYnVDOTBtckYuR3BkZld0MEozSyI7fQ==', 1612507337);

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `kode_supplier` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hutang` decimal(13,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`kode_supplier`, `nama`, `alamat`, `no_hp`, `hutang`, `created_at`, `updated_at`) VALUES
('SP0001', 'Lebron James', 'Los Angeles, California', '087757876543', '300000.00', '2021-01-23 15:53:03', '2021-02-02 06:42:53'),
('SP0002', 'Kevin Durant', 'Brooklyn, New York', '089987887878', '2500000.00', '2021-01-23 15:53:28', '2021-02-05 06:41:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `akses` enum('Akuntan','Persediaan','Pembelian','Super Admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `current_team_id`, `akses`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@baratha.com', '$2y$10$r1ZIiP9WO00G0sYxKbp22eucEA6FmSU5FobuC90mrF.GpdfWt0J3K', NULL, NULL, NULL, 'Super Admin', '2021-01-18 08:20:23', '2021-01-18 09:16:27'),
(2, 'inant', 'inant@baratha.com', '$2y$10$w1cKPvxwZvI7AYeDuDlLQ.dmSh6cvugb1BRXFQNT4wvXVc5U860cS', NULL, NULL, NULL, 'Super Admin', '2021-01-26 07:12:41', '2021-01-26 07:12:41');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`kode_barang`),
  ADD KEY `barang_id_kategori_foreign` (`id_kategori`);

--
-- Indeks untuk tabel `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`kode_customer`);

--
-- Indeks untuk tabel `detail_pemakaian_barang`
--
ALTER TABLE `detail_pemakaian_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pemakaian_barang_kode_pemakaian_foreign` (`kode_pemakaian`),
  ADD KEY `detail_pemakaian_barang_kode_barang_foreign` (`kode_barang`),
  ADD KEY `detail_pemakaian_barang_kode_biaya_foreign` (`kode_biaya`);

--
-- Indeks untuk tabel `detail_pembelian_barang`
--
ALTER TABLE `detail_pembelian_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pembelian_barang_kode_pembelian_foreign` (`kode_pembelian`),
  ADD KEY `detail_pembelian_barang_kode_barang_foreign` (`kode_barang`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kartu_hutang_kode_supplier_foreign` (`kode_supplier`);

--
-- Indeks untuk tabel `kartu_stock`
--
ALTER TABLE `kartu_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kartu_stock_kode_barang_foreign` (`kode_barang`);

--
-- Indeks untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kode_biaya`
--
ALTER TABLE `kode_biaya`
  ADD PRIMARY KEY (`kode_biaya`),
  ADD KEY `kode_biaya_kode_rekening_foreign` (`kode_rekening`);

--
-- Indeks untuk tabel `kode_induk`
--
ALTER TABLE `kode_induk`
  ADD PRIMARY KEY (`kode_induk`);

--
-- Indeks untuk tabel `kode_rekening`
--
ALTER TABLE `kode_rekening`
  ADD PRIMARY KEY (`kode_rekening`),
  ADD KEY `kode_rekening_kode_induk_foreign` (`kode_induk`);

--
-- Indeks untuk tabel `kunci_transaksi`
--
ALTER TABLE `kunci_transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `pemakaian_barang`
--
ALTER TABLE `pemakaian_barang`
  ADD PRIMARY KEY (`kode_pemakaian`);

--
-- Indeks untuk tabel `pembelian_barang`
--
ALTER TABLE `pembelian_barang`
  ADD PRIMARY KEY (`kode_pembelian`),
  ADD KEY `pembelian_barang_kode_supplier_foreign` (`kode_supplier`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`kode_supplier`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_pemakaian_barang`
--
ALTER TABLE `detail_pemakaian_barang`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_pembelian_barang`
--
ALTER TABLE `detail_pembelian_barang`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kartu_stock`
--
ALTER TABLE `kartu_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kunci_transaksi`
--
ALTER TABLE `kunci_transaksi`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_barang` (`id`);

--
-- Ketidakleluasaan untuk tabel `detail_pemakaian_barang`
--
ALTER TABLE `detail_pemakaian_barang`
  ADD CONSTRAINT `detail_pemakaian_barang_kode_barang_foreign` FOREIGN KEY (`kode_barang`) REFERENCES `barang` (`kode_barang`),
  ADD CONSTRAINT `detail_pemakaian_barang_kode_biaya_foreign` FOREIGN KEY (`kode_biaya`) REFERENCES `kode_biaya` (`kode_biaya`),
  ADD CONSTRAINT `detail_pemakaian_barang_kode_pemakaian_foreign` FOREIGN KEY (`kode_pemakaian`) REFERENCES `pemakaian_barang` (`kode_pemakaian`);

--
-- Ketidakleluasaan untuk tabel `detail_pembelian_barang`
--
ALTER TABLE `detail_pembelian_barang`
  ADD CONSTRAINT `detail_pembelian_barang_kode_barang_foreign` FOREIGN KEY (`kode_barang`) REFERENCES `barang` (`kode_barang`),
  ADD CONSTRAINT `detail_pembelian_barang_kode_pembelian_foreign` FOREIGN KEY (`kode_pembelian`) REFERENCES `pembelian_barang` (`kode_pembelian`);

--
-- Ketidakleluasaan untuk tabel `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  ADD CONSTRAINT `kartu_hutang_kode_supplier_foreign` FOREIGN KEY (`kode_supplier`) REFERENCES `supplier` (`kode_supplier`);

--
-- Ketidakleluasaan untuk tabel `kartu_stock`
--
ALTER TABLE `kartu_stock`
  ADD CONSTRAINT `kartu_stock_kode_barang_foreign` FOREIGN KEY (`kode_barang`) REFERENCES `barang` (`kode_barang`);

--
-- Ketidakleluasaan untuk tabel `kode_biaya`
--
ALTER TABLE `kode_biaya`
  ADD CONSTRAINT `kode_biaya_kode_rekening_foreign` FOREIGN KEY (`kode_rekening`) REFERENCES `kode_rekening` (`kode_rekening`);

--
-- Ketidakleluasaan untuk tabel `kode_rekening`
--
ALTER TABLE `kode_rekening`
  ADD CONSTRAINT `kode_rekening_kode_induk_foreign` FOREIGN KEY (`kode_induk`) REFERENCES `kode_induk` (`kode_induk`);

--
-- Ketidakleluasaan untuk tabel `pembelian_barang`
--
ALTER TABLE `pembelian_barang`
  ADD CONSTRAINT `pembelian_barang_kode_supplier_foreign` FOREIGN KEY (`kode_supplier`) REFERENCES `supplier` (`kode_supplier`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
