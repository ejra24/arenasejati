-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Sep 2026 pada 19.22
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
-- Database: `arenasejati`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(2, 'admin', 'ejrajago');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `kelas_lomba` varchar(100) NOT NULL,
  `nama_peserta` varchar(100) NOT NULL,
  `nama_burung` varchar(100) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status_pembayaran` varchar(15) DEFAULT 'pending',
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `jumlah_gantangan` int(11) NOT NULL,
  `nomor_gantangan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `total_harga` int(11) NOT NULL,
  `waktu_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktu_expired` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `kelas_lomba`, `nama_peserta`, `nama_burung`, `alamat`, `metode_pembayaran`, `status_pembayaran`, `bukti_transfer`, `jumlah_gantangan`, `nomor_gantangan`, `deskripsi`, `total_harga`, `waktu_pesan`, `waktu_expired`) VALUES
(85, 'Sesi 3 - Cucak Ijo Tilas', 'ejra', 'suki', 'raudah 6', 'QRIS Duitku', 'batal', NULL, 2, '3, 7', 'Ref: DS3519926R0XDUGKE2PKECRZ', 110000, '2026-09-10 03:49:11', '2026-09-10 11:59:11'),
(86, 'Sesi 3 - Cucak Ijo Tilas', 'ejra', 'suki', 'raudah 6', 'QRIS Duitku', 'batal', NULL, 1, '2', 'Ref: DS3519926R8S8SH61YDDNXQ3', 55000, '2026-09-10 04:42:21', '2026-09-10 12:52:21'),
(87, 'Sesi 1 - Murai Borneo Tilas', 'cacil', 'suki', 'raudah 6', 'QRIS Duitku', 'batal', NULL, 1, '2', 'Ref: DS35199264RS1O6C1SG17K4U', 55000, '2026-09-10 19:43:13', '2026-09-11 03:53:13'),
(88, 'Sesi 4 - Cendet Tilas', 'ejra', 'suki', 'pmnnor', 'QRIS Duitku', 'batal', NULL, 1, '7', 'Ref: DS3519926N0RZ0Y8GYV5VWPE', 55000, '2026-09-10 19:55:04', '2026-09-11 04:05:04'),
(89, 'Sesi 1 - Murai Borneo Tilas', 'ejra', 'nomz', 'pmnnor', 'Cash/Transfer', 'Lunas', NULL, 2, '4, 7', 'Lunas di Kasir', 110000, '2026-09-10 19:55:32', '2026-09-11 03:55:32'),
(90, 'Sesi 3 - Cucak Ijo Tilas', 'ejra', 'suki', 'pmnnor', 'QRIS Duitku', 'batal', NULL, 2, '3, 8', 'Ref: DS3519926US8TI0WBG2TVJ4T', 110000, '2026-09-11 07:06:16', '2026-09-11 15:16:16'),
(91, 'Sesi 3 - Cucak Ijo Tilas', 'ejra', 'suki', 'pmnnor', 'Cash/Transfer', 'Lunas', NULL, 2, '3, 4', 'Lunas di Kasir', 110000, '2026-09-11 07:06:52', '2026-09-11 15:06:52'),
(92, 'Sesi 14 - Cucak Ijo Sultan', 'Faiz Aprianda', 'nomz', 'suryanata', 'Cash/Transfer', 'Lunas', NULL, 1, '1', 'Lunas di Kasir', 220000, '2026-09-11 07:07:02', '2026-09-11 15:07:02'),
(93, 'Sesi 2 - Kacer Tilas', 'ejra', 'nomz', 'pmnnor', 'Cash/Transfer', 'Lunas', NULL, 2, '2, 3', 'Lunas di Kasir', 110000, '2026-09-11 07:07:48', '2026-09-11 15:07:48'),
(94, 'Sesi 8 - Kacer Marwah', 'ejra', 'suki', 'suryanata', 'Cash/Transfer', 'Lunas', NULL, 1, '24', 'Lunas di Kasir', 125000, '2026-09-11 07:07:59', '2026-09-11 15:07:59'),
(95, 'Sesi 6 - Konin Tilas', 'ejra', 'bruno', 'kehewanan gang 1', 'Cash/Transfer', 'Lunas', NULL, 2, '2, 3', 'Lunas di Kasir', 110000, '2026-09-11 07:13:10', '2026-09-11 15:13:10');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
