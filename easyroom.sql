-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20251129.06e246bd2d
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 14, 2025 at 06:27 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `easyroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_admin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`) VALUES
('ADMTS2DXE', 'admin02', 'admin02', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id_dosen` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_dosen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id_dosen`, `nama_dosen`, `nip`, `email`, `password`) VALUES
('DSNK4OFSX', 'Hayyin', '109807856878', 'hayyin@gmail.com', 'hayyin123'),
('DSNKAY8BY', 'Faqih., S.Bio.', '23080960101', 'faqih@gmail.com', 'faqih123');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `kode_jadwal` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `kode_mk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_dosen` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_ruang` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kelas` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hari` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `kuota_peserta` int DEFAULT NULL,
  `kuota_masuk` int DEFAULT NULL,
  `semester` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`kode_jadwal`, `kode_mk`, `id_dosen`, `kode_ruang`, `kelas`, `hari`, `jam_mulai`, `jam_selesai`, `kuota_peserta`, `kuota_masuk`, `semester`) VALUES
('JDWMFCA1HK', 'TI098', 'DSNKAY8BY', 'RM583', 'TIF-5B', 'Selasa', '07:00:00', '08:40:00', 40, 0, '1');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_mahasiswa` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jurusan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prodi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `nim`, `email`, `password`, `nama_mahasiswa`, `jurusan`, `prodi`) VALUES
('MHSGGAD30', '23080960111', 'Aripin@gmail.com', 'Arif123', 'Arif Nanda', 'Ekonomi Islam', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `matakuliah`
--

CREATE TABLE `matakuliah` (
  `kode_mk` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_dosen` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_mk` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sks` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matakuliah`
--

INSERT INTO `matakuliah` (`kode_mk`, `id_dosen`, `nama_mk`, `sks`) VALUES
('TI098', NULL, 'Pemrograman Web', 2);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `kode_peminjaman` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `id_dosen` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_ruang` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_admin` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `keperluan` text COLLATE utf8mb4_general_ci,
  `file_pengajuan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') COLLATE utf8mb4_general_ci DEFAULT 'Menunggu',
  `catatan_admin` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`kode_peminjaman`, `id_dosen`, `kode_ruang`, `id_admin`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `keperluan`, `file_pengajuan`, `status`, `catatan_admin`) VALUES
('PMJ6ELQ8XB0', 'DSNKAY8BY', 'RM583', NULL, '2025-12-15', '07:00:00', '08:00:00', 'pinjem kelas', 'uploads/pengajuan/1765730984_693eeaa8c2732_1-s2.0-S0048969720307749-main.pdf', 'Ditolak', ''),
('PMJ7YC1SOYQ', 'DSNKAY8BY', 'RM583', NULL, '2025-12-16', '07:00:00', '08:40:00', 'kelas', 'uploads/pengajuan/1765731020_693eeacc6c4c3_1-s2.0-S0048969720307749-main.pdf', 'Disetujui', ''),
('PMJR56D7GI7', 'DSNKAY8BY', 'RM583', NULL, '2025-12-15', '08:00:00', '09:00:00', 'sosialisasi', 'uploads/pengajuan/1765736103_693efea7d023d_sustainability-12-00373-v2.pdf', 'Menunggu', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ruang`
--

CREATE TABLE `ruang` (
  `kode_ruang` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_ruang` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `kapasitas` int DEFAULT NULL,
  `fasilitas` text COLLATE utf8mb4_general_ci,
  `lokasi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Terpakai','Kosong') COLLATE utf8mb4_general_ci DEFAULT 'Terpakai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruang`
--

INSERT INTO `ruang` (`kode_ruang`, `nama_ruang`, `kapasitas`, `fasilitas`, `lokasi`, `status`) VALUES
('RM237', 'ISDB 4.5', 40, NULL, NULL, 'Terpakai'),
('RM583', 'ISDB 3.8', 40, NULL, NULL, 'Terpakai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id_dosen`),
  ADD UNIQUE KEY `nidn` (`nip`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`kode_jadwal`),
  ADD KEY `kode_mk` (`kode_mk`),
  ADD KEY `id_dosen` (`id_dosen`),
  ADD KEY `kode_ruang` (`kode_ruang`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`kode_mk`),
  ADD KEY `id_dosen` (`id_dosen`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`kode_peminjaman`),
  ADD KEY `id_dosen` (`id_dosen`),
  ADD KEY `kode_ruang` (`kode_ruang`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `ruang`
--
ALTER TABLE `ruang`
  ADD PRIMARY KEY (`kode_ruang`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`kode_mk`) REFERENCES `matakuliah` (`kode_mk`),
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`),
  ADD CONSTRAINT `jadwal_ibfk_3` FOREIGN KEY (`kode_ruang`) REFERENCES `ruang` (`kode_ruang`);

--
-- Constraints for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD CONSTRAINT `matakuliah_ibfk_1` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`kode_ruang`) REFERENCES `ruang` (`kode_ruang`),
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
