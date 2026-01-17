-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 17, 2026 at 09:46 PM
-- Server version: 9.1.0
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rms`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
CREATE TABLE IF NOT EXISTS `access` (
  `id_access` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access_group` int DEFAULT NULL,
  `access_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `access_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `access_client` tinyint(1) NOT NULL COMMENT 'If true, the account is a client.',
  `access_active` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_access`),
  KEY `id_access_group` (`id_access_group`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`id_access`, `id_access_group`, `access_name`, `access_email`, `access_contact`, `access_password`, `access_foto`, `access_client`, `access_active`) VALUES
(1, 1, 'Solihul Hadi', 'dhiforester@gmail.com', '089601154726', '$2y$10$KnOYcmK1U3iE8ta.PnDefOTr1h5Cz1LaGHfyM5wBqg1vuqqg1i5le', 'ca6526b10323e5ffc519def7f71e10.jpg', 0, 1),
(2, 8, 'Dewi Widiastuti', 'dewiwidiastuti@gmail.com', '08975657467', '$2y$10$YW/wCElX7HYlfipjFo80eO89RkvlUZ9iIOwZk4lK.Cf/BR8ypeygm', '4522beb0ae8aabe337284b439dcc79.png', 0, 1),
(8, 1, 'Bayu Anugrah', 'bayu88aaa@gmail.com', '085693168595', '$2y$10$gNbRZTnQ8lPJtrg5TGCyoe0N2k7EcFKI1znNWu8XI/UkuCJA4S8Ae', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `access_feature`
--

DROP TABLE IF EXISTS `access_feature`;
CREATE TABLE IF NOT EXISTS `access_feature` (
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `feature_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_category` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `datetime_creat` timestamp NOT NULL,
  PRIMARY KEY (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_feature`
--

INSERT INTO `access_feature` (`id_access_feature`, `feature_name`, `feature_category`, `feature_description`, `datetime_creat`) VALUES
('36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y', 'Koneksi SIMRS', 'Koneksi', 'Pengaturan parameter koneksi dengan SIMRS', '2025-12-16 20:07:41'),
('5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E', 'Koneksi Satu Sehat', 'Koneksi', 'Pengaturan parameter koneksi ke Satu Sehat Platform', '2025-12-17 18:47:14'),
('D3f1WdUaRaPRZAoyVb317LsXAu1TA983ehbZ', 'Tarif Layanan', 'Referensi', 'Halaman yang digunakan untuk mengelola tarif layanan pmeriksaan radiologi', '2026-01-13 14:26:39'),
('Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA', 'Bantuan', 'Lainnya', 'Halaman untuk mengelola konten bantuan atau dokumentasi', '2025-09-06 14:36:36'),
('DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD', 'Pemeriksaan', 'Master', 'Halaman untuk mengelola pemeriksaan radiologi', '2025-12-19 20:02:10'),
('FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX', 'Kode Pemeriksaan', 'Referensi', 'Halaman untuk mengelola referensi pemeriksaan', '2025-12-21 01:27:29'),
('FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7', 'Kode Klinis', 'Referensi', 'Halaman untuk mengelola kode klinis pasien', '2025-12-20 20:28:14'),
('KX9gf0vhmDPh6ewWEZhJBkfzzJSP381lGM8e', 'Dicom Router', 'Lainnya', 'Download Dicom Router Dari Satu Sehat', '2026-01-04 21:55:27'),
('Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH', 'Pengaturan Umum', 'Pengaturan', 'Halaman yang berfungsi untuk mengatur aplikasi secara umum', '2025-09-01 19:27:07'),
('aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY', 'Email Gateway', 'Pengaturan', 'Halaman yang berguna untuk menyimpan pengaturan email gateway', '2025-09-01 19:32:54'),
('fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw', 'Daftar Pertanyaan', 'Referensi', 'Halaman untuk mengelola daftar pertanyaan dalam assesment radiologi', '2025-12-30 20:58:40'),
('jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw', 'Akses Pengguna', 'Akses', 'Halaman untuk mengelola akun akses pengguna', '2025-08-31 20:23:54'),
('lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD', 'Entitas Akses Pengguna', 'Akses', 'Halaman untuk mengelola entitas/group/level pengguna', '2025-08-31 20:23:01'),
('mx0HdJRPFScVla7nCyFTIhAfbdGLYfwDpblB', 'Koneksi PACS', 'Koneksi', 'Pengaturan parameter koneksi ke PACS', '2025-12-17 21:30:17'),
('nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv', 'Fitur Aplikasi', 'Akses', 'Halaman untuk mengelola fitur aplikasi', '2025-08-31 20:21:48'),
('nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee', 'API Key', 'Koneksi', 'Halaman untuk mengelola data API key untuk aplikasi lain agar terhubung Ke Redix', '2025-12-19 16:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `access_group`
--

DROP TABLE IF EXISTS `access_group`;
CREATE TABLE IF NOT EXISTS `access_group` (
  `id_access_group` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_access_group`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_group`
--

INSERT INTO `access_group` (`id_access_group`, `group_name`, `group_description`) VALUES
(1, 'Admin', 'Pihak yang berwenang melakukan akses ke semua fitur'),
(3, 'Sekretaris', 'Pihak yang melakukan verifikasi pembayaran'),
(8, 'Bendahara', 'Pihak yang berhak menyimpan keuangan');

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
CREATE TABLE IF NOT EXISTS `access_log` (
  `id_access_log` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `log_datetime` datetime NOT NULL,
  `log_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_access_log`),
  KEY `access_log_id_access_index` (`id_access`)
) ENGINE=InnoDB AUTO_INCREMENT=403 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_log`
--

INSERT INTO `access_log` (`id_access_log`, `id_access`, `log_datetime`, `log_category`, `log_description`) VALUES
(1, 1, '2025-09-12 11:38:46', 'Entitas Akses', 'Edit Entitas Akses'),
(2, 1, '2025-09-12 11:41:08', 'Bantuan', 'Tambah Konten Bantuan'),
(3, 1, '2025-09-12 13:08:04', 'Login', 'Login Berhasil'),
(4, 1, '2025-09-12 13:20:16', 'Bantuan', 'Hapus Konten Bantuan'),
(5, 1, '2025-09-12 13:20:24', 'Bantuan', 'Edit Konten Bantuan'),
(6, 1, '2025-09-13 07:18:55', 'Login', 'Login Berhasil'),
(7, 2, '2025-09-13 07:31:01', 'Login', 'Login Berhasil'),
(8, 2, '2025-09-13 08:49:27', 'Login', 'Login Berhasil'),
(9, 1, '2025-09-14 13:27:21', 'Login', 'Login Berhasil'),
(10, 1, '2025-09-14 17:35:33', 'Login', 'Login Berhasil'),
(11, 1, '2025-09-14 18:39:49', 'Kelas', 'Input Kelas Berhasil'),
(12, 1, '2025-09-14 19:02:10', 'Login', 'Login Berhasil'),
(13, 1, '2025-09-14 19:34:30', 'Login', 'Login Berhasil'),
(14, 1, '2025-09-15 01:16:03', 'Login', 'Login Berhasil'),
(15, 1, '2025-09-17 21:07:10', 'Login', 'Login Berhasil'),
(16, 1, '2025-09-18 03:42:08', 'Login', 'Login Berhasil'),
(17, 1, '2025-09-18 12:59:24', 'Login', 'Login Berhasil'),
(18, 1, '2025-09-18 16:19:50', 'Login', 'Login Berhasil'),
(19, 1, '2025-09-18 18:05:00', 'Login', 'Login Berhasil'),
(20, 1, '2025-09-18 21:38:06', 'Login', 'Login Berhasil'),
(21, 1, '2025-09-19 18:55:59', 'Login', 'Login Berhasil'),
(22, 1, '2025-09-19 21:29:03', 'Login', 'Login Berhasil'),
(23, 1, '2025-09-20 17:14:16', 'Login', 'Login Berhasil'),
(24, 1, '2025-09-20 19:49:57', 'Login', 'Login Berhasil'),
(25, 1, '2025-09-21 00:13:59', 'Login', 'Login Berhasil'),
(26, 1, '2025-09-23 00:12:26', 'Login', 'Login Berhasil'),
(27, 1, '2025-09-23 01:31:31', 'Login', 'Login Berhasil'),
(28, 1, '2025-09-23 16:04:17', 'Login', 'Login Berhasil'),
(29, 1, '2025-09-23 18:11:19', 'Login', 'Login Berhasil'),
(30, 1, '2025-09-24 22:51:52', 'Login', 'Login Berhasil'),
(31, 1, '2025-09-25 03:16:42', 'Login', 'Login Berhasil'),
(32, 1, '2025-09-25 14:49:50', 'Login', 'Login Berhasil'),
(33, 1, '2025-09-25 17:10:02', 'Login', 'Login Berhasil'),
(34, 1, '2025-09-26 00:15:18', 'Login', 'Login Berhasil'),
(35, 1, '2025-10-14 13:02:47', 'Login', 'Login Berhasil'),
(36, 1, '2025-10-14 13:04:26', 'Login', 'Login Berhasil'),
(37, 1, '2025-10-19 12:58:26', 'Login', 'Login Berhasil'),
(38, 1, '2025-10-21 03:37:45', 'Login', 'Login Berhasil'),
(39, 1, '2025-10-21 04:05:41', 'Periode Akademik', 'Input Periode Akademik Akses'),
(40, 1, '2025-10-21 18:55:35', 'Login', 'Login Berhasil'),
(41, 1, '2025-10-21 23:33:54', 'Login', 'Login Berhasil'),
(42, 1, '2025-10-21 23:34:48', 'Login', 'Login Berhasil'),
(43, 1, '2025-10-22 00:50:16', 'Login', 'Login Berhasil'),
(44, 1, '2025-10-22 13:58:24', 'Login', 'Login Berhasil'),
(45, 1, '2025-10-23 00:13:45', 'Login', 'Login Berhasil'),
(46, 1, '2025-10-24 17:59:21', 'Login', 'Login Berhasil'),
(47, 1, '2025-10-24 20:59:47', 'Login', 'Login Berhasil'),
(48, 1, '2025-10-25 00:37:08', 'Login', 'Login Berhasil'),
(49, 1, '2025-10-25 10:52:13', 'Login', 'Login Berhasil'),
(50, 1, '2025-10-25 16:35:46', 'Login', 'Login Berhasil'),
(51, 1, '2025-10-25 16:46:02', 'Akses', 'Input Fitur Akses'),
(52, 1, '2025-10-25 16:46:22', 'Entitas Akses', 'Edit Entitas Akses'),
(53, 1, '2025-10-25 18:32:09', 'Login', 'Login Berhasil'),
(54, 1, '2025-10-25 21:54:05', 'Login', 'Login Berhasil'),
(55, 1, '2025-10-26 11:39:30', 'Login', 'Login Berhasil'),
(56, 1, '2025-10-26 19:24:26', 'Login', 'Login Berhasil'),
(57, 1, '2025-10-27 15:56:35', 'Login', 'Login Berhasil'),
(58, 1, '2025-10-27 19:06:53', 'Login', 'Login Berhasil'),
(59, 1, '2025-10-27 22:02:31', 'Login', 'Login Berhasil'),
(60, 1, '2025-10-27 23:11:15', 'Login', 'Login Berhasil'),
(61, 1, '2025-10-28 01:34:30', 'Login', 'Login Berhasil'),
(62, 1, '2025-10-28 01:52:48', 'Pembayaran', 'Input Pembayaran Berhasil'),
(63, 1, '2025-10-28 22:38:22', 'Login', 'Login Berhasil'),
(64, 1, '2025-10-29 00:02:42', 'Pembayaran', 'Input Pembayaran Berhasil'),
(65, 1, '2025-10-29 00:02:50', 'Pembayaran', 'Input Pembayaran Berhasil'),
(66, 1, '2025-10-29 00:02:59', 'Pembayaran', 'Input Pembayaran Berhasil'),
(67, 1, '2025-10-29 00:03:04', 'Pembayaran', 'Input Pembayaran Berhasil'),
(68, 1, '2025-10-29 22:17:23', 'Login', 'Login Berhasil'),
(69, 1, '2025-10-30 00:09:55', 'Login', 'Login Berhasil'),
(70, 1, '2025-10-30 01:46:55', 'Login', 'Login Berhasil'),
(71, 1, '2025-10-30 14:01:24', 'Login', 'Login Berhasil'),
(72, 1, '2025-10-30 17:28:11', 'Login', 'Login Berhasil'),
(73, 1, '2025-10-31 19:40:25', 'Login', 'Login Berhasil'),
(74, 1, '2025-10-31 22:00:34', 'Login', 'Login Berhasil'),
(75, 1, '2025-11-01 00:44:29', 'Login', 'Login Berhasil'),
(76, 1, '2025-11-01 02:39:31', 'Login', 'Login Berhasil'),
(77, 1, '2025-11-01 19:34:03', 'Login', 'Login Berhasil'),
(78, 1, '2025-11-01 23:19:42', 'Login', 'Login Berhasil'),
(79, 1, '2025-11-02 19:41:33', 'Login', 'Login Berhasil'),
(80, 1, '2025-11-03 00:31:51', 'Login', 'Login Berhasil'),
(81, 1, '2025-11-05 01:49:08', 'Login', 'Login Berhasil'),
(82, 1, '2025-11-05 21:19:23', 'Login', 'Login Berhasil'),
(83, 1, '2025-11-06 01:51:46', 'Login', 'Login Berhasil'),
(84, 1, '2025-11-06 02:07:37', 'Siswa', 'Edit Siswa Berhasil'),
(85, 1, '2025-11-06 02:08:21', 'Siswa', 'Edit Siswa Berhasil'),
(86, 1, '2025-11-06 02:08:31', 'Siswa', 'Edit Siswa Berhasil'),
(87, 1, '2025-11-06 02:14:00', 'Siswa', 'Edit Siswa Berhasil'),
(88, 1, '2025-11-06 02:14:08', 'Siswa', 'Edit Siswa Berhasil'),
(89, 1, '2025-11-06 02:41:17', 'Siswa', 'Edit Siswa Berhasil'),
(90, 1, '2025-11-06 02:41:25', 'Siswa', 'Edit Siswa Berhasil'),
(91, 1, '2025-11-06 02:41:34', 'Siswa', 'Edit Siswa Berhasil'),
(92, 1, '2025-11-06 02:41:40', 'Siswa', 'Edit Siswa Berhasil'),
(93, 1, '2025-11-06 02:41:45', 'Siswa', 'Edit Siswa Berhasil'),
(94, 1, '2025-11-06 12:27:02', 'Login', 'Login Berhasil'),
(95, 1, '2025-11-06 16:08:40', 'Login', 'Login Berhasil'),
(96, 1, '2025-11-06 18:58:23', 'Login', 'Login Berhasil'),
(97, 1, '2025-11-07 22:10:23', 'Login', 'Login Berhasil'),
(98, 1, '2025-11-08 19:47:18', 'Login', 'Login Berhasil'),
(99, 1, '2025-11-09 00:03:44', 'Login', 'Login Berhasil'),
(100, 1, '2025-11-09 01:31:22', 'Login', 'Login Berhasil'),
(101, 1, '2025-11-09 04:31:09', 'Pembayaran', 'Input Pembayaran Berhasil'),
(102, 1, '2025-11-09 05:04:47', 'Pembayaran', 'Input Pembayaran Berhasil'),
(103, 1, '2025-11-09 17:23:44', 'Login', 'Login Berhasil'),
(104, 1, '2025-11-09 18:52:33', 'Pembayaran', 'Input Pembayaran Berhasil'),
(105, 1, '2025-11-09 19:04:01', 'Pembayaran', 'Input Pembayaran Berhasil'),
(106, 1, '2025-11-09 22:05:41', 'Login', 'Login Berhasil'),
(107, 1, '2025-11-10 15:36:49', 'Login', 'Login Berhasil'),
(108, 1, '2025-11-10 17:21:36', 'Login', 'Login Berhasil'),
(109, 1, '2025-11-11 15:41:24', 'Login', 'Login Berhasil'),
(110, 1, '2025-11-11 15:42:08', 'Pembayaran', 'Input Pembayaran Berhasil'),
(111, 1, '2025-11-11 15:54:29', 'Login', 'Login Berhasil'),
(112, 1, '2025-11-11 19:03:18', 'Login', 'Login Berhasil'),
(113, 1, '2025-11-11 20:32:07', 'Login', 'Login Berhasil'),
(114, 1, '2025-11-11 21:55:55', 'Login', 'Login Berhasil'),
(115, 1, '2025-11-11 22:57:28', 'Login', 'Login Berhasil'),
(116, 1, '2025-11-11 23:58:19', 'Login', 'Login Berhasil'),
(117, 1, '2025-11-13 04:44:46', 'Login', 'Login Berhasil'),
(118, 1, '2025-11-13 12:52:38', 'Login', 'Login Berhasil'),
(119, 1, '2025-11-14 00:16:43', 'Login', 'Login Berhasil'),
(120, 1, '2025-11-14 17:06:58', 'Login', 'Login Berhasil'),
(121, 1, '2025-11-14 20:01:55', 'Login', 'Login Berhasil'),
(122, 1, '2025-11-14 20:02:49', 'Login', 'Login Berhasil'),
(123, 1, '2025-11-15 00:53:50', 'Login', 'Login Berhasil'),
(124, 1, '2025-11-15 00:54:12', 'Pembayaran', 'Input Pembayaran Berhasil'),
(125, 1, '2025-11-15 00:54:26', 'Pembayaran', 'Input Pembayaran Berhasil'),
(126, 1, '2025-11-15 01:47:03', 'Pembayaran', 'Input Pembayaran Berhasil'),
(127, 1, '2025-11-15 01:48:06', 'Pembayaran', 'Input Pembayaran Berhasil'),
(128, 1, '2025-11-15 01:48:24', 'Pembayaran', 'Input Pembayaran Berhasil'),
(129, 1, '2025-11-15 03:18:09', 'Komponen Biaya', 'Input Komponen Biaya Berhasil'),
(130, 1, '2025-11-15 03:18:50', 'Komponen Biaya', 'Input Komponen Biaya Berhasil'),
(131, 1, '2025-11-15 03:19:16', 'Komponen Biaya', 'Input Komponen Biaya Berhasil'),
(132, 1, '2025-11-15 03:23:28', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(133, 1, '2025-11-15 03:24:42', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(134, 1, '2025-11-15 03:25:18', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(135, 1, '2025-11-15 03:43:06', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(136, 1, '2025-11-15 18:18:57', 'Login', 'Login Berhasil'),
(137, 1, '2025-11-15 19:53:55', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(138, 1, '2025-11-15 20:01:49', 'Komponen Biaya', 'Hapus Komponen Biaya'),
(139, 1, '2025-11-15 21:42:52', 'Login', 'Login Berhasil'),
(140, 1, '2025-11-16 00:08:35', 'Login', 'Login Berhasil'),
(141, 1, '2025-11-16 00:54:04', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(142, 1, '2025-11-16 10:36:54', 'Login', 'Login Berhasil'),
(143, 1, '2025-11-17 01:03:32', 'Login', 'Login Berhasil'),
(144, 1, '2025-11-17 21:37:56', 'Login', 'Login Berhasil'),
(145, 1, '2025-11-18 01:06:31', 'Login', 'Login Berhasil'),
(146, 1, '2025-11-18 02:37:56', 'Login', 'Login Berhasil'),
(147, 1, '2025-11-18 02:54:45', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(148, 1, '2025-11-18 02:56:00', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(149, 1, '2025-11-18 02:56:04', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(150, 1, '2025-11-18 02:57:15', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(151, 1, '2025-11-18 02:57:19', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(152, 1, '2025-11-18 02:57:25', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(153, 1, '2025-11-18 02:57:32', 'Komponen Biaya', 'Update Komponen Biaya Berhasil'),
(154, 1, '2025-11-18 18:06:55', 'Login', 'Login Berhasil'),
(155, 1, '2025-11-18 19:48:48', 'Login', 'Login Berhasil'),
(156, 1, '2025-11-19 01:50:17', 'Login', 'Login Berhasil'),
(157, 1, '2025-11-19 15:09:21', 'Login', 'Login Berhasil'),
(158, 1, '2025-11-19 16:05:59', 'Pembayaran', 'Input Pembayaran Berhasil'),
(159, 1, '2025-11-19 16:59:42', 'Pembayaran', 'Input Pembayaran Berhasil'),
(160, 1, '2025-11-19 16:59:50', 'Pembayaran', 'Input Pembayaran Berhasil'),
(161, 1, '2025-11-19 17:07:29', 'Pembayaran', 'Input Pembayaran Berhasil'),
(162, 1, '2025-11-19 17:07:41', 'Pembayaran', 'Input Pembayaran Berhasil'),
(163, 1, '2025-11-19 18:24:40', 'Login', 'Login Berhasil'),
(164, 1, '2025-11-20 01:40:02', 'Login', 'Login Berhasil'),
(165, 1, '2025-11-20 14:02:25', 'Login', 'Login Berhasil'),
(166, 1, '2025-11-20 23:54:13', 'Login', 'Login Berhasil'),
(167, 1, '2025-11-22 22:15:01', 'Login', 'Login Berhasil'),
(168, 1, '2025-11-23 01:13:07', 'Login', 'Login Berhasil'),
(169, 1, '2025-11-23 14:11:17', 'Login', 'Login Berhasil'),
(170, 1, '2025-11-24 00:53:29', 'Login', 'Login Berhasil'),
(171, 1, '2025-11-25 00:41:26', 'Login', 'Login Berhasil'),
(172, 1, '2025-11-25 02:16:42', 'Pembayaran', 'Input Pembayaran Berhasil'),
(173, 1, '2025-11-25 02:17:25', 'Pembayaran', 'Input Pembayaran Berhasil'),
(174, 1, '2025-11-25 04:28:16', 'Login', 'Login Berhasil'),
(175, 1, '2025-11-26 00:35:18', 'Login', 'Login Berhasil'),
(176, 1, '2025-11-26 01:27:51', 'Pembayaran', 'Input Pembayaran Berhasil'),
(177, 1, '2025-11-28 01:07:11', 'Login', 'Login Berhasil'),
(178, 1, '2025-11-28 03:28:18', 'Pembayaran', 'Input Pembayaran Berhasil'),
(179, 1, '2025-11-28 03:36:08', 'Pembayaran', 'Input Pembayaran Berhasil'),
(180, 1, '2025-11-28 03:39:45', 'Pembayaran', 'Input Pembayaran Berhasil'),
(181, 1, '2025-11-28 03:40:54', 'Pembayaran', 'Input Pembayaran Berhasil'),
(182, 1, '2025-11-28 03:42:31', 'Pembayaran', 'Input Pembayaran Berhasil'),
(183, 1, '2025-11-28 14:45:10', 'Login', 'Login Berhasil'),
(184, 1, '2025-11-28 20:20:00', 'Login', 'Login Berhasil'),
(185, 1, '2025-11-28 20:53:50', 'Pembayaran', 'Input Pembayaran Berhasil'),
(186, 1, '2025-11-29 14:32:11', 'Login', 'Login Berhasil'),
(187, 1, '2025-11-29 15:05:25', 'Pembayaran', 'Input Pembayaran Berhasil'),
(188, 1, '2025-11-29 16:45:43', 'Login', 'Login Berhasil'),
(189, 1, '2025-11-29 18:23:07', 'Pembayaran', 'Input Pembayaran Berhasil'),
(190, 1, '2025-11-29 18:45:34', 'Pembayaran', 'Input Pembayaran Berhasil'),
(191, 1, '2025-11-29 18:45:55', 'Pembayaran', 'Input Pembayaran Berhasil'),
(192, 1, '2025-11-29 18:48:41', 'Pembayaran', 'Input Pembayaran Berhasil'),
(193, 1, '2025-11-29 18:50:13', 'Pembayaran', 'Hapus Pembayaran Berhasil'),
(194, 1, '2025-11-29 18:51:34', 'Pembayaran', 'Input Pembayaran Berhasil'),
(195, 1, '2025-11-29 18:58:14', 'Tagihan', 'Hapus Tagihan Berhasil'),
(196, 1, '2025-11-29 19:00:40', 'Tagihan', 'Hapus Tagihan Berhasil'),
(197, 1, '2025-11-29 19:01:40', 'Tagihan', 'Hapus Tagihan Berhasil'),
(198, 1, '2025-11-29 20:33:40', 'Login', 'Login Berhasil'),
(199, 1, '2025-11-29 21:15:17', 'Pembayaran', 'Input Pembayaran Berhasil'),
(200, 1, '2025-11-29 22:12:51', 'Pembayaran', 'Input Pembayaran Berhasil'),
(201, 1, '2025-11-29 22:32:01', 'Pembayaran', 'Input Pembayaran Berhasil'),
(202, 1, '2025-11-29 22:34:09', 'Pembayaran', 'Input Pembayaran Berhasil'),
(203, 1, '2025-11-29 22:34:23', 'Pembayaran', 'Input Pembayaran Berhasil'),
(204, 1, '2025-11-29 22:34:31', 'Pembayaran', 'Input Pembayaran Berhasil'),
(205, 1, '2025-11-30 00:40:36', 'Login', 'Login Berhasil'),
(206, 1, '2025-11-30 03:55:18', 'Login', 'Login Berhasil'),
(207, 1, '2025-11-30 05:05:53', 'Login', 'Login Berhasil'),
(208, 1, '2025-11-30 19:09:11', 'Login', 'Login Berhasil'),
(209, 1, '2025-11-30 21:03:09', 'Periode Akademik', 'Input Periode Akademik Akses'),
(210, 1, '2025-11-30 21:06:24', 'Tahun Akademik', 'Hapus Tahun Akademik Berhasil'),
(211, 1, '2025-11-30 21:14:48', 'Periode Akademik', 'Update Periode Akademik ID 4'),
(212, 1, '2025-11-30 21:15:02', 'Periode Akademik', 'Update Periode Akademik ID 4'),
(213, 1, '2025-11-30 22:09:22', 'Periode Akademik', 'Update Periode Akademik ID 4'),
(214, 1, '2025-12-02 03:10:38', 'Login', 'Login Berhasil'),
(215, 1, '2025-12-02 03:52:47', 'Login', 'Login Berhasil'),
(216, 1, '2025-12-02 12:32:33', 'Login', 'Login Berhasil'),
(217, 1, '2025-12-02 16:18:25', 'Login', 'Login Berhasil'),
(218, 1, '2025-12-02 21:00:38', 'Login', 'Login Berhasil'),
(219, 1, '2025-12-03 09:13:27', 'Login', 'Login Berhasil'),
(220, 1, '2025-12-03 14:34:57', 'Login', 'Login Berhasil'),
(221, 1, '2025-12-03 15:55:54', 'Login', 'Login Berhasil'),
(222, 1, '2025-12-03 21:25:39', 'Login', 'Login Berhasil'),
(223, 1, '2025-12-04 00:35:27', 'Login', 'Login Berhasil'),
(224, 1, '2025-12-04 13:45:10', 'Login', 'Login Berhasil'),
(225, 1, '2025-12-05 01:04:09', 'Login', 'Login Berhasil'),
(226, 1, '2025-12-05 19:20:45', 'Login', 'Login Berhasil'),
(227, 1, '2025-12-05 21:41:07', 'Login', 'Login Berhasil'),
(228, 1, '2025-12-06 01:02:23', 'Login', 'Login Berhasil'),
(229, 1, '2025-12-06 16:24:01', 'Login', 'Login Berhasil'),
(230, 1, '2025-12-07 08:03:21', 'Login', 'Login Berhasil'),
(231, 1, '2025-12-07 22:41:32', 'Login', 'Login Berhasil'),
(232, 1, '2025-12-08 01:04:42', 'Login', 'Login Berhasil'),
(233, 1, '2025-12-08 18:00:43', 'Login', 'Login Berhasil'),
(234, 1, '2025-12-08 18:32:11', 'Fitur Akses', 'Hapus Fitur Akses'),
(235, 1, '2025-12-08 18:33:33', 'Akses', 'Input Fitur Akses'),
(236, 1, '2025-12-13 21:02:42', 'Login', 'Login Berhasil'),
(237, 1, '2025-12-14 02:12:40', 'Login', 'Login Berhasil'),
(238, 1, '2025-12-14 21:19:42', 'Login', 'Login Berhasil'),
(239, 1, '2025-12-15 03:08:36', 'Login', 'Login Berhasil'),
(240, 1, '2025-12-17 00:05:51', 'Login', 'Login Berhasil'),
(241, 1, '2025-12-17 01:15:29', 'Fitur Akses', 'Hapus Fitur Akses'),
(242, 1, '2025-12-17 01:15:38', 'Fitur Akses', 'Hapus Fitur Akses'),
(243, 1, '2025-12-17 01:15:42', 'Fitur Akses', 'Hapus Fitur Akses'),
(244, 1, '2025-12-17 01:15:46', 'Fitur Akses', 'Hapus Fitur Akses'),
(245, 1, '2025-12-17 01:15:53', 'Fitur Akses', 'Hapus Fitur Akses'),
(246, 1, '2025-12-17 01:16:00', 'Fitur Akses', 'Hapus Fitur Akses'),
(247, 1, '2025-12-17 01:16:05', 'Fitur Akses', 'Hapus Fitur Akses'),
(248, 1, '2025-12-17 03:07:41', 'Akses', 'Input Fitur Akses'),
(249, 1, '2025-12-17 03:07:56', 'Entitas Akses', 'Edit Entitas Akses'),
(250, 1, '2025-12-17 05:26:34', 'Login', 'Login Berhasil'),
(251, 1, '2025-12-17 16:17:16', 'Login', 'Login Berhasil'),
(252, 1, '2025-12-17 20:05:17', 'Login', 'Login Berhasil'),
(253, 1, '2025-12-17 21:10:02', 'Login', 'Login Berhasil'),
(254, 1, '2025-12-17 23:17:00', 'Login', 'Login Berhasil'),
(255, 1, '2025-12-18 01:10:49', 'Login', 'Login Berhasil'),
(256, 1, '2025-12-18 01:47:14', 'Akses', 'Input Fitur Akses'),
(257, 1, '2025-12-18 01:47:23', 'Entitas Akses', 'Edit Entitas Akses'),
(258, 1, '2025-12-18 02:55:19', 'Login', 'Login Berhasil'),
(259, 1, '2025-12-18 04:30:17', 'Akses', 'Input Fitur Akses'),
(260, 1, '2025-12-18 04:32:00', 'Entitas Akses', 'Hapus Entitas Akses'),
(261, 1, '2025-12-18 04:32:30', 'Entitas Akses', 'Edit Entitas Akses'),
(262, 1, '2025-12-19 16:35:46', 'Login', 'Login Berhasil'),
(263, 1, '2025-12-19 18:11:53', 'Login', 'Login Berhasil'),
(264, 1, '2025-12-19 23:22:57', 'Login', 'Login Berhasil'),
(265, 1, '2025-12-19 23:28:20', 'Akses', 'Input Fitur Akses'),
(266, 1, '2025-12-19 23:31:22', 'Entitas Akses', 'Edit Entitas Akses'),
(267, 1, '2025-12-20 02:17:09', 'Login', 'Login Berhasil'),
(268, 1, '2025-12-20 03:02:10', 'Akses', 'Input Fitur Akses'),
(269, 1, '2025-12-20 03:02:18', 'Entitas Akses', 'Edit Entitas Akses'),
(270, 1, '2025-12-20 22:05:27', 'Login', 'Login Berhasil'),
(271, 1, '2025-12-20 23:50:18', 'Login', 'Login Berhasil'),
(272, 1, '2025-12-21 03:28:14', 'Akses', 'Input Fitur Akses'),
(273, 1, '2025-12-21 03:30:49', 'Entitas Akses', 'Edit Entitas Akses'),
(274, 1, '2025-12-21 08:25:08', 'Login', 'Login Berhasil'),
(275, 1, '2025-12-21 08:27:29', 'Akses', 'Input Fitur Akses'),
(276, 1, '2025-12-21 11:18:43', 'Login', 'Login Berhasil'),
(277, 1, '2025-12-21 11:26:35', 'Entitas Akses', 'Edit Entitas Akses'),
(278, 1, '2025-12-21 18:29:50', 'Login', 'Login Berhasil'),
(279, 1, '2025-12-21 20:33:36', 'Login', 'Login Berhasil'),
(280, 1, '2025-12-22 04:57:27', 'Login', 'Login Berhasil'),
(281, 1, '2025-12-22 15:55:24', 'Login', 'Login Berhasil'),
(282, 1, '2025-12-22 21:11:27', 'Login', 'Login Berhasil'),
(283, 1, '2025-12-23 10:33:12', 'Login', 'Login Berhasil'),
(284, 1, '2025-12-23 14:15:35', 'Login', 'Login Berhasil'),
(285, 1, '2025-12-24 00:34:52', 'Login', 'Login Berhasil'),
(286, 1, '2025-12-24 05:33:52', 'Login', 'Login Berhasil'),
(287, 1, '2025-12-24 09:56:32', 'Login', 'Login Berhasil'),
(288, 8, '2025-12-24 10:13:53', 'Login', 'Login Berhasil'),
(289, 8, '2025-12-24 11:37:20', 'Login', 'Login Berhasil'),
(290, 8, '2025-12-24 11:38:04', 'Login', 'Login Berhasil'),
(291, 1, '2025-12-24 18:50:16', 'Login', 'Login Berhasil'),
(292, 1, '2025-12-25 03:06:28', 'Login', 'Login Berhasil'),
(293, 1, '2025-12-25 09:31:59', 'Login', 'Login Berhasil'),
(294, 1, '2025-12-25 14:47:50', 'Login', 'Login Berhasil'),
(295, 1, '2025-12-25 23:50:36', 'Login', 'Login Berhasil'),
(296, 1, '2025-12-26 13:44:17', 'Login', 'Login Berhasil'),
(297, 1, '2025-12-27 01:30:10', 'Login', 'Login Berhasil'),
(298, 1, '2025-12-27 08:09:39', 'Login', 'Login Berhasil'),
(299, 8, '2025-12-27 09:12:23', 'Login', 'Login Berhasil'),
(300, 8, '2025-12-27 10:54:42', 'Login', 'Login Berhasil'),
(301, 1, '2025-12-27 11:03:38', 'Login', 'Login Berhasil'),
(302, 8, '2025-12-27 13:04:43', 'Login', 'Login Berhasil'),
(303, 1, '2025-12-27 15:22:22', 'Login', 'Login Berhasil'),
(304, 1, '2025-12-27 18:57:48', 'Login', 'Login Berhasil'),
(305, 1, '2025-12-28 03:11:36', 'Login', 'Login Berhasil'),
(306, 1, '2025-12-28 08:27:38', 'Login', 'Login Berhasil'),
(307, 1, '2025-12-28 11:49:27', 'Login', 'Login Berhasil'),
(308, 1, '2025-12-28 15:37:22', 'Login', 'Login Berhasil'),
(309, 1, '2025-12-29 00:26:16', 'Login', 'Login Berhasil'),
(310, 1, '2025-12-29 01:51:49', 'Login', 'Login Berhasil'),
(311, 8, '2025-12-29 08:32:01', 'Login', 'Login Berhasil'),
(312, 1, '2025-12-29 09:00:57', 'Login', 'Login Berhasil'),
(313, 1, '2025-12-29 11:41:22', 'Login', 'Login Berhasil'),
(314, 1, '2025-12-29 11:58:06', 'Login', 'Login Berhasil'),
(315, 8, '2025-12-29 12:20:12', 'Login', 'Login Berhasil'),
(316, 1, '2025-12-29 12:30:43', 'Login', 'Login Berhasil'),
(317, 1, '2025-12-29 13:07:03', 'Login', 'Login Berhasil'),
(318, 1, '2025-12-29 15:35:14', 'Login', 'Login Berhasil'),
(319, 1, '2025-12-29 17:51:46', 'Login', 'Login Berhasil'),
(320, 1, '2025-12-30 05:07:06', 'Login', 'Login Berhasil'),
(321, 8, '2025-12-30 08:45:56', 'Login', 'Login Berhasil'),
(322, 8, '2025-12-30 13:38:01', 'Login', 'Login Berhasil'),
(323, 1, '2025-12-30 19:12:31', 'Login', 'Login Berhasil'),
(324, 1, '2025-12-30 20:26:02', 'Login', 'Login Berhasil'),
(325, 1, '2025-12-31 03:12:37', 'Login', 'Login Berhasil'),
(326, 1, '2025-12-31 03:58:40', 'Akses', 'Input Fitur Akses'),
(327, 1, '2025-12-31 04:01:02', 'Entitas Akses', 'Edit Entitas Akses'),
(328, 8, '2025-12-31 08:33:58', 'Login', 'Login Berhasil'),
(329, 1, '2025-12-31 14:34:50', 'Login', 'Login Berhasil'),
(330, 1, '2025-12-31 18:21:22', 'Login', 'Login Berhasil'),
(331, 1, '2026-01-01 06:27:29', 'Login', 'Login Berhasil'),
(332, 1, '2026-01-01 10:42:55', 'Login', 'Login Berhasil'),
(333, 1, '2026-01-01 12:53:29', 'Login', 'Login Berhasil'),
(334, 1, '2026-01-01 16:18:34', 'Login', 'Login Berhasil'),
(335, 1, '2026-01-01 17:35:06', 'Login', 'Login Berhasil'),
(336, 1, '2026-01-01 20:20:18', 'Login', 'Login Berhasil'),
(337, 1, '2026-01-01 22:10:07', 'Login', 'Login Berhasil'),
(338, 1, '2026-01-02 02:16:47', 'Login', 'Login Berhasil'),
(339, 1, '2026-01-02 16:47:54', 'Login', 'Login Berhasil'),
(340, 1, '2026-01-02 19:02:34', 'Login', 'Login Berhasil'),
(341, 1, '2026-01-02 20:03:57', 'Login', 'Login Berhasil'),
(342, 8, '2026-01-03 09:55:34', 'Login', 'Login Berhasil'),
(343, 1, '2026-01-03 16:55:05', 'Login', 'Login Berhasil'),
(344, 1, '2026-01-03 22:25:50', 'Login', 'Login Berhasil'),
(345, 1, '2026-01-04 08:16:52', 'Login', 'Login Berhasil'),
(346, 1, '2026-01-04 09:30:52', 'Login', 'Login Berhasil'),
(347, 1, '2026-01-04 14:12:53', 'Login', 'Login Berhasil'),
(348, 1, '2026-01-04 22:34:48', 'Login', 'Login Berhasil'),
(349, 1, '2026-01-05 02:19:43', 'Login', 'Login Berhasil'),
(350, 1, '2026-01-05 02:24:53', 'Login', 'Login Berhasil'),
(351, 1, '2026-01-05 04:55:27', 'Akses', 'Input Fitur Akses'),
(352, 1, '2026-01-05 04:56:48', 'Entitas Akses', 'Edit Entitas Akses'),
(353, 1, '2026-01-05 16:44:06', 'Login', 'Login Berhasil'),
(354, 1, '2026-01-06 05:47:27', 'Login', 'Login Berhasil'),
(355, 8, '2026-01-06 11:00:59', 'Login', 'Login Berhasil'),
(356, 8, '2026-01-06 13:08:30', 'Login', 'Login Berhasil'),
(357, 1, '2026-01-06 17:56:36', 'Login', 'Login Berhasil'),
(358, 1, '2026-01-06 22:20:55', 'Login', 'Login Berhasil'),
(359, 1, '2026-01-07 00:19:50', 'Login', 'Login Berhasil'),
(360, 1, '2026-01-07 01:32:34', 'Login', 'Login Berhasil'),
(361, 1, '2026-01-07 09:15:24', 'Login', 'Login Berhasil'),
(362, 1, '2026-01-07 10:23:51', 'Login', 'Login Berhasil'),
(363, 1, '2026-01-07 10:58:13', 'Login', 'Login Berhasil'),
(364, 1, '2026-01-07 11:06:07', 'Login', 'Login Berhasil'),
(365, 8, '2026-01-07 11:23:23', 'Login', 'Login Berhasil'),
(366, 1, '2026-01-07 12:35:43', 'Login', 'Login Berhasil'),
(367, 1, '2026-01-07 13:13:48', 'Login', 'Login Berhasil'),
(368, 1, '2026-01-07 14:43:57', 'Login', 'Login Berhasil'),
(369, 1, '2026-01-07 14:58:42', 'Login', 'Login Berhasil'),
(370, 1, '2026-01-07 16:18:00', 'Login', 'Login Berhasil'),
(371, 1, '2026-01-07 16:29:13', 'Login', 'Login Berhasil'),
(372, 8, '2026-01-08 11:35:27', 'Login', 'Login Berhasil'),
(373, 1, '2026-01-08 17:43:41', 'Login', 'Login Berhasil'),
(374, 1, '2026-01-08 20:10:22', 'Login', 'Login Berhasil'),
(375, 1, '2026-01-10 16:13:37', 'Login', 'Login Berhasil'),
(376, 1, '2026-01-12 19:16:56', 'Login', 'Login Berhasil'),
(377, 1, '2026-01-12 23:02:36', 'Login', 'Login Berhasil'),
(378, 1, '2026-01-13 19:55:45', 'Login', 'Login Berhasil'),
(379, 1, '2026-01-13 20:51:21', 'Login', 'Login Berhasil'),
(380, 1, '2026-01-13 21:26:39', 'Akses', 'Input Fitur Akses'),
(381, 1, '2026-01-13 21:47:43', 'Entitas Akses', 'Edit Entitas Akses'),
(382, 1, '2026-01-13 23:19:15', 'Login', 'Login Berhasil'),
(383, 1, '2026-01-14 04:38:50', 'Login', 'Login Berhasil'),
(384, 1, '2026-01-14 19:28:03', 'Login', 'Login Berhasil'),
(385, 1, '2026-01-14 22:21:42', 'Login', 'Login Berhasil'),
(386, 1, '2026-01-15 01:04:04', 'Login', 'Login Berhasil'),
(387, 1, '2026-01-15 02:35:28', 'Login', 'Login Berhasil'),
(388, 1, '2026-01-15 02:38:03', 'Login', 'Login Berhasil'),
(389, 1, '2026-01-15 03:07:35', 'Login', 'Login Berhasil'),
(390, 1, '2026-01-15 03:32:07', 'Login', 'Login Berhasil'),
(391, 1, '2026-01-15 12:57:26', 'Login', 'Login Berhasil'),
(392, 1, '2026-01-15 13:10:17', 'Login', 'Login Berhasil'),
(393, 1, '2026-01-15 14:22:54', 'Login', 'Login Berhasil'),
(394, 1, '2026-01-15 15:47:44', 'Login', 'Login Berhasil'),
(395, 1, '2026-01-16 05:35:52', 'Login', 'Login Berhasil'),
(396, 1, '2026-01-16 21:00:48', 'Login', 'Login Berhasil'),
(397, 1, '2026-01-17 00:48:00', 'Login', 'Login Berhasil'),
(398, 1, '2026-01-17 03:59:27', 'Login', 'Login Berhasil'),
(399, 1, '2026-01-17 15:27:39', 'Login', 'Login Berhasil'),
(400, 1, '2026-01-17 16:23:19', 'Login', 'Login Berhasil'),
(401, 1, '2026-01-17 22:37:52', 'Login', 'Login Berhasil'),
(402, 1, '2026-01-18 00:41:05', 'Login', 'Login Berhasil');

-- --------------------------------------------------------

--
-- Table structure for table `access_login`
--

DROP TABLE IF EXISTS `access_login`;
CREATE TABLE IF NOT EXISTS `access_login` (
  `id_access_login` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  PRIMARY KEY (`id_access_login`),
  KEY `access_login_id_access_index` (`id_access`)
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_login`
--

INSERT INTO `access_login` (`id_access_login`, `id_access`, `token`, `datetime_creat`, `datetime_expired`) VALUES
(51, 2, 'D4hbO8ZH3g4UZWJXy6ZhcWt1qzu8DEX2ILFx', '2025-09-13 08:49:27', '2025-09-13 10:33:46'),
(306, 8, '1GNwTfgziYVhHj8QPubz96G0LJlocAGfSaz4', '2026-01-08 11:35:26', '2026-01-08 12:36:36'),
(334, 1, 'jOZ77JDShxQ2SuLhoEoadm3ZKxsiSNeVgbLW', '2026-01-18 00:41:05', '2026-01-18 05:44:16');

-- --------------------------------------------------------

--
-- Table structure for table `access_permission`
--

DROP TABLE IF EXISTS `access_permission`;
CREATE TABLE IF NOT EXISTS `access_permission` (
  `id_permission` int NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_permission`),
  KEY `id_access` (`id_access`),
  KEY `id_access_feature` (`id_access_feature`)
) ENGINE=InnoDB AUTO_INCREMENT=297 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_permission`
--

INSERT INTO `access_permission` (`id_permission`, `id_access`, `id_access_feature`) VALUES
(31, 7, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(32, 7, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(33, 7, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(34, 7, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(35, 7, 'TleUu0waFsTCePkXuIqJuA1DDJ2hY3FGvzYX'),
(36, 7, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(37, 7, 'a99AXGc0fRtw8wPbfCq16dmAfETaN5jZQc8R'),
(38, 7, 'mOFQURHvlxqXre9cyx7FMjFtzqc1zWb0x2RD'),
(108, 2, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(109, 2, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(110, 2, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(111, 2, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(112, 2, 'G2LxhMdVkih0ZJ4xPz8YGxVVCMLNmH0OnQvF'),
(113, 2, 'TIVPffUE3kqw288OB1R0CJ09daM9l2TLdGVv'),
(114, 2, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(115, 2, 'TleUu0waFsTCePkXuIqJuA1DDJ2hY3FGvzYX'),
(116, 2, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(117, 2, 'JbBByqDggzgIC8y6IH4JnbyynMUvHd0iFx5G'),
(118, 2, 'a99AXGc0fRtw8wPbfCq16dmAfETaN5jZQc8R'),
(119, 2, 'mOFQURHvlxqXre9cyx7FMjFtzqc1zWb0x2RD'),
(239, 8, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(240, 8, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(241, 8, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(242, 8, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(243, 8, 'mx0HdJRPFScVla7nCyFTIhAfbdGLYfwDpblB'),
(244, 8, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(245, 8, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(246, 8, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(247, 8, 'DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD'),
(248, 8, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(249, 8, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(250, 8, 'FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7'),
(251, 8, 'FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX'),
(281, 1, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(282, 1, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(283, 1, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(284, 1, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(285, 1, 'mx0HdJRPFScVla7nCyFTIhAfbdGLYfwDpblB'),
(286, 1, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(287, 1, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(288, 1, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(289, 1, 'KX9gf0vhmDPh6ewWEZhJBkfzzJSP381lGM8e'),
(290, 1, 'DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD'),
(291, 1, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(292, 1, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(293, 1, 'fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw'),
(294, 1, 'FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7'),
(295, 1, 'FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX'),
(296, 1, 'D3f1WdUaRaPRZAoyVb317LsXAu1TA983ehbZ');

-- --------------------------------------------------------

--
-- Table structure for table `access_reference`
--

DROP TABLE IF EXISTS `access_reference`;
CREATE TABLE IF NOT EXISTS `access_reference` (
  `id_access_reference` int NOT NULL AUTO_INCREMENT,
  `id_access_group` int NOT NULL,
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_access_reference`),
  KEY `id_access_group` (`id_access_group`),
  KEY `id_access_fitures` (`id_access_feature`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_reference`
--

INSERT INTO `access_reference` (`id_access_reference`, `id_access_group`, `id_access_feature`) VALUES
(17, 2, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(18, 2, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(19, 2, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(20, 2, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(21, 2, 'TleUu0waFsTCePkXuIqJuA1DDJ2hY3FGvzYX'),
(22, 2, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(23, 2, 'a99AXGc0fRtw8wPbfCq16dmAfETaN5jZQc8R'),
(24, 2, 'mOFQURHvlxqXre9cyx7FMjFtzqc1zWb0x2RD'),
(33, 3, 'a99AXGc0fRtw8wPbfCq16dmAfETaN5jZQc8R'),
(34, 3, 'mOFQURHvlxqXre9cyx7FMjFtzqc1zWb0x2RD'),
(77, 8, 'a99AXGc0fRtw8wPbfCq16dmAfETaN5jZQc8R'),
(78, 8, 'mOFQURHvlxqXre9cyx7FMjFtzqc1zWb0x2RD'),
(191, 1, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(192, 1, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(193, 1, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(194, 1, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(195, 1, 'mx0HdJRPFScVla7nCyFTIhAfbdGLYfwDpblB'),
(196, 1, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(197, 1, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(198, 1, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(199, 1, 'KX9gf0vhmDPh6ewWEZhJBkfzzJSP381lGM8e'),
(200, 1, 'DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD'),
(201, 1, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(202, 1, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(203, 1, 'fErKPHIY6bEuhp7sOivMHglXHOP2gVubzGyw'),
(204, 1, 'FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7'),
(205, 1, 'FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX'),
(206, 1, 'D3f1WdUaRaPRZAoyVb317LsXAu1TA983ehbZ');

-- --------------------------------------------------------

--
-- Table structure for table `access_reset`
--

DROP TABLE IF EXISTS `access_reset`;
CREATE TABLE IF NOT EXISTS `access_reset` (
  `id_access_reset` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_access_reset`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `access_reset`
--

INSERT INTO `access_reset` (`id_access_reset`, `id_access`, `datetime_creat`, `token`) VALUES
(1, 1, '2025-11-10 01:39:04', 'hMUik7SIDyT9H1a00wUlKnAAd2N3OiKQrCz8'),
(2, 1, '2025-11-10 01:48:27', 'Q7v2lJCcwr78O91DtB79ufKYd1dMharLJEhP'),
(3, 1, '2025-11-10 01:49:24', 'jUM1YQByDUmhIZMp5eXLGqc7qr2IwRDL165l');

-- --------------------------------------------------------

--
-- Table structure for table `api_account`
--

DROP TABLE IF EXISTS `api_account`;
CREATE TABLE IF NOT EXISTS `api_account` (
  `id_api_account` int NOT NULL AUTO_INCREMENT,
  `api_name` varchar(255) NOT NULL COMMENT 'Nama Environment',
  `base_url_api` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `created_at` datetime NOT NULL,
  `duration_expired` bigint UNSIGNED NOT NULL COMMENT 'milisecond',
  PRIMARY KEY (`id_api_account`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `api_account`
--

INSERT INTO `api_account` (`id_api_account`, `api_name`, `base_url_api`, `username`, `password`, `created_at`, `duration_expired`) VALUES
(1, 'Dev-Senalogy', 'http://localhost/rms/API', 'ApiForSenalogy', '$2y$10$asyeqn.UIuBhUMdYToWjbuK/C.hBlGMqJe.jVLPCmZICKyTFl6L0q', '2025-12-19 17:14:41', 604800000),
(3, 'SIMRS', 'http://localhost/rms/API', 'dhiforester', '$2y$10$DY9B7CSAKl2HRjIVJbjUH.dHZ9IX3p4LAI7DvBpQ/KX/I1NmBHd3O', '2025-12-19 19:35:53', 60000);

-- --------------------------------------------------------

--
-- Table structure for table `api_token`
--

DROP TABLE IF EXISTS `api_token`;
CREATE TABLE IF NOT EXISTS `api_token` (
  `id_api_token` int NOT NULL AUTO_INCREMENT,
  `id_api_account` int NOT NULL COMMENT 'From api_account',
  `token` text NOT NULL COMMENT 'Hasing',
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  PRIMARY KEY (`id_api_token`),
  KEY `token_to_account` (`id_api_account`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `api_token`
--

INSERT INTO `api_token` (`id_api_token`, `id_api_account`, `token`, `created_at`, `expired_at`) VALUES
(8, 1, '$2y$10$VhGx5kImvm/0Dfxe36XijevbOtsBBxvYBGeykrKOEWlycaNKgynCO', '2026-01-13 19:09:30', '2026-01-20 19:09:30'),
(9, 1, '$2y$10$tjUbrGYAYuAe2Eua3mDj7.ryGZ./qKofb1gYS2FkSK3P2Jd2Bcz7m', '2026-01-15 09:17:51', '2026-01-22 09:17:51'),
(10, 1, '$2y$10$PdkfY0am7kq4nsYpLRDBJeD6wPykBPANAtntA6muoo/M3F2teZMIe', '2026-01-15 09:18:31', '2026-01-22 09:18:31'),
(11, 1, '$2y$10$0sI0jHbjuFhFB90Oky1CuODlH89qKePhu8H2ud0ULnf7OYv8MRQ7a', '2026-01-15 09:22:40', '2026-01-22 09:22:40');

-- --------------------------------------------------------

--
-- Table structure for table `app_configuration`
--

DROP TABLE IF EXISTS `app_configuration`;
CREATE TABLE IF NOT EXISTS `app_configuration` (
  `id_configuration` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_keyword` json NOT NULL,
  `app_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_base_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_year` int NOT NULL,
  `app_company` json NOT NULL,
  PRIMARY KEY (`id_configuration`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `app_configuration`
--

INSERT INTO `app_configuration` (`id_configuration`, `app_title`, `app_keyword`, `app_description`, `app_favicon`, `app_logo`, `app_base_url`, `app_author`, `app_year`, `app_company`) VALUES
(1, 'Radix v1.0', '[\"radiology\", \"el-syifa\", \"kuningan\"]', 'Radiology Information System RSU El-Syifa Kuningan', '7c1a0ab63bc6599fc51d74f7a15efb.png', '26802d057d82c89ca18070029ede00.png', 'http://182.253.36.132/rms', 'Solihul Hadi', 2025, '{\"company_code\": \"0124R006\", \"company_name\": \"RSU El-Syifa Kuningan\", \"company_email\": \"hallo.rsuelsyifa@gmail.com\", \"company_address\": \"Jalan RE Martadinata No.21 Ancaran Kuningan\", \"company_contact\": \"(0232) 876240\"}');

-- --------------------------------------------------------

--
-- Table structure for table `captcha`
--

DROP TABLE IF EXISTS `captcha`;
CREATE TABLE IF NOT EXISTS `captcha` (
  `id_captcha` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `captcha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  PRIMARY KEY (`id_captcha`)
) ENGINE=InnoDB AUTO_INCREMENT=5697 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `connection_pacs`
--

DROP TABLE IF EXISTS `connection_pacs`;
CREATE TABLE IF NOT EXISTS `connection_pacs` (
  `id_connection_pacs` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ex: Development, Staging, Production',
  `url_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'dari PACS',
  `url_pacs` varchar(255) DEFAULT NULL,
  `username_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'dari PACS',
  `password_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'dari PACS',
  `token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'token dari service login',
  `token_expired` datetime DEFAULT NULL COMMENT 'Informasi waktu expired',
  `status_connection_pacs` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_connection_pacs`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connection_satu_sehat`
--

DROP TABLE IF EXISTS `connection_satu_sehat`;
CREATE TABLE IF NOT EXISTS `connection_satu_sehat` (
  `id_connection_satu_sehat` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_connection_satu_sehat` varchar(255) NOT NULL COMMENT 'Ex: Development, Staging, Production',
  `url_connection_satu_sehat` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `organization_id` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `client_key` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `secret_key` varchar(255) NOT NULL COMMENT 'Dari Satu Sehat',
  `token` varchar(255) NOT NULL,
  `datetime_expired` datetime DEFAULT NULL,
  `status_connection_satu_sehat` tinyint(1) NOT NULL COMMENT 'True Or False',
  PRIMARY KEY (`id_connection_satu_sehat`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connection_simrs`
--

DROP TABLE IF EXISTS `connection_simrs`;
CREATE TABLE IF NOT EXISTS `connection_simrs` (
  `id_connection_simrs` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_connection_simrs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ex: Development, Staging, Local, Production',
  `url_connection_simrs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_key` varchar(255) NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `datetime_expired` datetime DEFAULT NULL,
  `status_connection_simrs` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_connection_simrs`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_klinis`
--

DROP TABLE IF EXISTS `master_klinis`;
CREATE TABLE IF NOT EXISTS `master_klinis` (
  `id_master_klinis` int NOT NULL AUTO_INCREMENT,
  `nama_klinis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nama klinis yang ditampilkan ke dokter',
  `snomed_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Kode SNOMED CT',
  `snomed_display` varchar(255) DEFAULT NULL COMMENT 'Deskripsi SNOMED CT',
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Respirasi, Kardiovaskular, Trauma, dll',
  `aktif` enum('Ya','Tidak') DEFAULT 'Ya',
  `datetime_create` datetime DEFAULT CURRENT_TIMESTAMP,
  `datetime_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_master_klinis`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_klinis`
--

INSERT INTO `master_klinis` (`id_master_klinis`, `nama_klinis`, `snomed_code`, `snomed_display`, `kategori`, `aktif`, `datetime_create`, `datetime_update`) VALUES
(1, 'Sesak napas', '267036007', 'Dyspnea', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(2, 'Batuk', '49727002', 'Cough', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(3, 'Batuk berdahak', '28743005', 'Productive cough', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(4, 'Nyeri dada', '29857009', 'Chest pain', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(5, 'Suspek pneumonia', '233604007', 'Pneumonia', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(6, 'Suspek TB paru', '56717001', 'Tuberculosis of lung', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(7, 'Hemoptisis', '66857006', 'Hemoptysis', 'Respirasi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(8, 'Nyeri dada tipikal', '29857009', 'Chest pain', 'Kardiovaskular', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(9, 'Palpitasi', '80313002', 'Palpitations', 'Kardiovaskular', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(10, 'Gagal jantung', '42343007', 'Congestive heart failure', 'Kardiovaskular', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(11, 'Kardiomegali', '8186001', 'Cardiomegaly', 'Kardiovaskular', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(12, 'Hipertensi', '38341003', 'Hypertensive disorder', 'Kardiovaskular', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(13, 'Suspek penyakit jantung', '56265001', 'Heart disease', 'Kardiovaskular', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(14, 'Sakit kepala', '25064002', 'Headache', 'Neurologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(15, 'Penurunan kesadaran', '3006004', 'Reduced level of consciousness', 'Neurologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(16, 'Kejang', '91175000', 'Seizure', 'Neurologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(17, 'Stroke suspek', '230690007', 'Cerebrovascular accident', 'Neurologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(18, 'Vertigo', '399153001', 'Vertigo', 'Neurologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(19, 'Trauma kepala', '82271004', 'Head injury', 'Trauma', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(20, 'Trauma dada', '162267008', 'Injury of chest', 'Trauma', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(21, 'Nyeri sendi', '57676002', 'Joint pain', 'Muskuloskeletal', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(22, 'Fraktur suspek', '125605004', 'Suspected fracture', 'Trauma', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(23, 'Dislokasi', '263204007', 'Dislocation', 'Trauma', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(24, 'Jatuh', '271436007', 'Fall', 'Trauma', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(25, 'Nyeri perut', '21522001', 'Abdominal pain', 'Digestif', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(26, 'Perut akut', '274666005', 'Acute abdomen', 'Digestif', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(27, 'Mual muntah', '422587007', 'Nausea and vomiting', 'Digestif', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(28, 'Suspek apendisitis', '74400008', 'Appendicitis', 'Digestif', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(29, 'Hepatomegali', '80585000', 'Hepatomegaly', 'Digestif', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(30, 'Ileus', '81060008', 'Intestinal obstruction', 'Digestif', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(31, 'Nyeri pinggang', '279039007', 'Flank pain', 'Urologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(32, 'Disuria', '49650001', 'Dysuria', 'Urologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(33, 'Hematuria', '34436003', 'Hematuria', 'Urologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(34, 'Batu ginjal', '95570007', 'Kidney stone', 'Urologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(35, 'Retensi urin', '236681002', 'Urinary retention', 'Urologi', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(36, 'Kehamilan', '77386006', 'Pregnancy', 'Obstetri', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(37, 'Nyeri perut bawah', '162049009', 'Lower abdominal pain', 'Obstetri', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(38, 'Perdarahan pervaginam', '131148009', 'Vaginal bleeding', 'Obstetri', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(39, 'Kontrol kehamilan', '161714006', 'Antenatal care', 'Obstetri', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(40, 'Suspek kehamilan ektopik', '34801009', 'Ectopic pregnancy', 'Obstetri', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(41, 'Demam', '386661006', 'Fever', 'Umum', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(42, 'Lemah', '84229001', 'Fatigue', 'Umum', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(43, 'Penurunan BB', '89362005', 'Weight loss', 'Umum', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(44, 'Skrining penyakit', '171047005', 'Screening', 'Umum', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00'),
(45, 'Evaluasi lanjutan', '225358003', 'Follow-up examination', 'Umum', 'Ya', '2025-12-21 00:00:00', '2025-12-21 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `master_pemeriksaan`
--

DROP TABLE IF EXISTS `master_pemeriksaan`;
CREATE TABLE IF NOT EXISTS `master_pemeriksaan` (
  `id_master_pemeriksaan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pemeriksaan` varchar(255) NOT NULL,
  `modalitas` enum('XR','CT','US','MR','NM','PT','DX','CR') NOT NULL,
  `pemeriksaan_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Loinc atau Terminology',
  `pemeriksaan_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Loinc atau Terminology',
  `pemeriksaan_sys` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Code System',
  `bodysite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'jika ada',
  `bodysite_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'jika ada',
  `bodysite_sys` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'jika ada',
  `report_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Mapping Untuk Diagnostic Report',
  `report_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Mapping Untuk Diagnostic Report',
  `report_sys` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Mapping Untuk Diagnostic Report',
  PRIMARY KEY (`id_master_pemeriksaan`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_pemeriksaan`
--

INSERT INTO `master_pemeriksaan` (`id_master_pemeriksaan`, `nama_pemeriksaan`, `modalitas`, `pemeriksaan_code`, `pemeriksaan_description`, `pemeriksaan_sys`, `bodysite_code`, `bodysite_description`, `bodysite_sys`, `report_code`, `report_description`, `report_sys`) VALUES
(1, 'Foto Thorax PA', 'XR', '30745-4', 'Chest X-ray PA view', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct', '30745-0', 'Chest X-ray report', 'http://loinc.org'),
(2, 'Foto Thorax AP', 'XR', '30746-2', 'Chest X-ray AP view', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct', '30745-0', 'Chest X-ray report', 'http://loinc.org'),
(3, 'Foto Thorax Lateral', 'XR', '30747-0', 'Chest X-ray lateral view', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct', '30745-0', 'Chest X-ray report', 'http://loinc.org'),
(4, 'Foto Abdomen Polos', 'XR', '30738-9', 'Abdomen X-ray', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(5, 'Foto Pelvis', 'XR', '30739-7', 'Pelvis X-ray', 'http://loinc.org', '12921003', 'Pelvis', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(6, 'Foto Cervical AP/Lateral', 'XR', '30751-2', 'Cervical spine X-ray', '', '122494005', 'Cervical spine', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(7, 'Foto Lumbal AP/Lateral', 'XR', '30753-8', 'Lumbar spine X-ray', 'http://loinc.org', '122496007', 'Lumbar spine', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(8, 'Foto Femur', 'XR', '30757-9', 'Femur X-ray', 'http://loinc.org', '71341001', 'Femur', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(9, 'Foto Cruris', 'XR', '30758-7', 'Lower leg X-ray', 'http://loinc.org', '30021000', 'Lower leg', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(10, 'Foto Sinus Paranasal', 'XR', '30760-3', 'Paranasal sinus X-ray', 'http://loinc.org', '66019005', 'Paranasal sinus', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(11, 'CT Scan Kepala Non Kontras', 'CT', '24627-2', 'CT Head WO contrast', 'http://loinc.org', '69536005', 'Head', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(12, 'CT Scan Kepala Kontras', 'CT', '24628-0', 'CT Head W contrast', 'http://loinc.org', '69536005', 'Head', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(13, 'CT Scan Thorax', 'CT', '24604-1', 'CT Chest', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(14, 'CT Scan Abdomen', 'CT', '24605-8', 'CT Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(15, 'CT Scan Abdomen Kontras', 'CT', '24606-6', 'CT Abdomen W contrast', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(16, 'CT Scan Pelvis', 'CT', '24607-4', 'CT Pelvis', 'http://loinc.org', '12921003', 'Pelvis', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(17, 'CT Scan Sinus', 'CT', '24614-0', 'CT Sinus', 'http://loinc.org', '66019005', 'Paranasal sinus', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(18, 'CT Scan Spine Cervical', 'CT', '24615-7', 'CT Cervical spine', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(19, 'CT Scan Spine Lumbal', 'CT', '24617-3', 'CT Lumbar spine', 'http://loinc.org', '122496007', 'Lumbar spine', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(20, 'USG Abdomen', 'US', '30792-6', 'Ultrasound Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(21, 'USG Hati', 'US', '30793-4', 'Ultrasound Liver', 'http://loinc.org', '10200004', 'Liver', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(22, 'USG Ginjal', 'US', '30794-2', 'Ultrasound Kidney', 'http://loinc.org', '64033007', 'Kidney', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(23, 'USG Kandung Kemih', 'US', '30795-9', 'Ultrasound Bladder', 'http://loinc.org', '89837001', 'Urinary bladder', 'http://snomed.info/sct', '25019‑1', 'US Urinary bladder (Ultrasound Bladder diagnostic report)', 'http://loinc.org'),
(24, 'USG Prostat', 'US', '30796-7', 'Ultrasound Prostate', 'http://loinc.org', '41216001', 'Prostate', 'http://snomed.info/sct', '24884‑9', 'US Prostate transrectal (Ultrasound Prostate diagnostic report)', 'http://loinc.org'),
(25, 'USG Kehamilan', 'US', '30801-5', 'Obstetric ultrasound', 'http://loinc.org', '12738006', 'Uterus', 'http://snomed.info/sct', '11525‑3', 'US for pregnancy (Ultrasound Obstetric Report)', 'http://loinc.org'),
(26, 'USG Transvaginal', 'US', '30802-3', 'Transvaginal ultrasound', 'http://loinc.org', '12738006', 'Uterus', 'http://snomed.info/sct', '24677‑7', 'Transvaginal ultrasound diagnostic report', 'http://loinc.org'),
(27, 'USG Payudara', 'US', '30797-5', 'Ultrasound Breast', 'http://loinc.org', '76752008', 'Breast', 'http://snomed.info/sct', '24601‑7', 'US Breast', 'http://loinc.org'),
(28, 'MRI Brain', 'MR', '24590-2', 'MRI Brain', 'http://loinc.org', '12738006', 'Brain', 'http://snomed.info/sct', '24590‑2', 'MRI Brain diagnostic report', 'http://loinc.org'),
(29, 'MRI Spine Cervical', 'MR', '24601-7', 'MRI Cervical spine', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '24935‑9', 'MRI Cervical spine report', 'http://loinc.org'),
(30, 'MRI Spine Lumbal', 'MR', '24603-3', 'MRI Lumbar spine', 'http://loinc.org', '122496007', 'Lumbar spine', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(31, 'MRI Knee', 'MR', '24594-4', 'MRI Knee', 'http://loinc.org', '72696002', 'Knee', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(32, 'MRI Abdomen', 'MR', '24598-5', 'MRI Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(33, 'Bone Scan', 'NM', '25022-8', 'Bone scintigraphy', 'http://loinc.org', '272673000', 'Skeleton', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(34, 'Thyroid Scan', 'NM', '25023-6', 'Thyroid scintigraphy', 'http://loinc.org', '69748006', 'Thyroid', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(35, 'Renal Scan', 'NM', '25024-4', 'Renal scintigraphy', 'http://loinc.org', '64033007', 'Kidney', 'http://snomed.info/sct', '25025-1', 'Renal scintigraphy report', 'http://loinc.org'),
(36, 'Foto Thorax', 'XR', '30745-0', 'Chest X-ray', 'http://loinc.org', '51185008', 'Thoracic cavity', 'http://snomed.info/sct', '30745-0', 'Chest X-ray report', 'http://loinc.org'),
(37, 'Foto Dada Posisi Decubitus Kanan', 'XR', '24650-4', 'X-Ray Chest Right Lateral Decubitus', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(38, 'Foto Dada Posisi Decubitus Kiri', 'XR', '24650-4', 'X-Ray Chest Left Lateral Decubitus', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(39, 'Foto Dada Posisi Decubitus (Umum)', 'XR', '30734-8', 'X-Ray Chest Lateral Decubitus / View Decubitus', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(40, 'Foto Dada Posisi Top Lordotik', 'XR', '24640‑5', 'XR Chest Apical lordotic', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '18748‑4', 'Diagnostic imaging study', 'http://loinc.org'),
(41, 'Foto Dada Posisi PA + Lateral + Lordotik', 'XR', '30741‑3', 'XR Chest PA and Lateral and Lordotic upright', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '18748‑4', 'Diagnostic imaging study', 'http://loinc.org'),
(42, 'Foto Dada Views 3 + Apical Lordotik (jika pakai kombinasi proyeksi lain)', 'XR', '103500‑5', 'XR Chest Views 3 and Apical lordotic', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '18748‑4', 'Diagnostic imaging study', 'http://loinc.org'),
(43, 'Foto Dada AP/Lateral', 'XR', '36687‑2', 'Chest X-ray', 'http://loinc.org', '51185008', 'Chest', 'http://snomed.info/sct', '36687‑2', 'Chest X-ray AP & Lateral diagnostic report', 'http://loinc.org'),
(44, 'Foto Abdomen 2 Posisi', 'XR', '30740-5', 'Abdomen X-ray 2 views', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '30741-3', 'Abdomen X-ray 2 views report', 'http://loinc.org'),
(45, 'Foto Abdomen 3 Posisi', 'XR', '30742-1', 'Abdomen X-ray 3 views', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '30743-9', 'Abdomen X-ray 3 views report', 'http://loinc.org'),
(46, 'Foto Cranium AP', 'XR', '30744-7', 'Cranium X-ray AP', 'http://loinc.org', '223343009', 'Skull', 'http://snomed.info/sct', '30745-4', 'Cranium X-ray AP report', 'http://loinc.org'),
(47, 'Foto Cranium AP + Lateral', 'XR', '30746-2', 'Cranium X-ray 2 views', 'http://loinc.org', '223343009', 'Skull', 'http://snomed.info/sct', '30747-0', 'Cranium X-ray 2 views report', 'http://loinc.org'),
(48, 'Foto Cranium PA', 'XR', '30748-8', 'Cranium X-ray PA', 'http://loinc.org', '223343009', 'Skull', 'http://snomed.info/sct', '30749-6', 'Cranium X-ray PA report', 'http://loinc.org'),
(49, 'Foto Cranium PA + Lateral', 'XR', '30750-3', 'Cranium X-ray 2 views', 'http://loinc.org', '223343009', 'Skull', 'http://snomed.info/sct', '30751-1', 'Cranium X-ray 2 views report', 'http://loinc.org'),
(50, 'Foto OS Nasale PA', 'XR', '30752-9', 'Paranasal sinuses X-ray PA', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30753-7', 'Paranasal sinuses X-ray PA report', 'http://loinc.org'),
(51, 'Foto OS Nasale PA + Lateral', 'XR', '30754-5', 'Paranasal sinuses X-ray 2 views', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30755-2', 'Paranasal sinuses X-ray 2 views report', 'http://loinc.org'),
(52, 'Foto SPN PA', 'XR', '30756-0', 'Paranasal sinuses X-ray PA', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30757-8', 'Paranasal sinuses X-ray PA report', 'http://loinc.org'),
(53, 'Foto SPN PA + Lateral', 'XR', '30758-6', 'Paranasal sinuses X-ray 2 views', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30759-4', 'Paranasal sinuses X-ray 2 views report', 'http://loinc.org'),
(54, 'Foto SPN PA Waters', 'XR', '30760-2', 'Paranasal sinuses X-ray Waters', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30761-0', 'Paranasal sinuses X-ray Waters report', 'http://loinc.org'),
(55, 'Foto SPN PA + Lateral + Waters', 'XR', '30762-8', 'Paranasal sinuses X-ray 3 views', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30763-6', 'Paranasal sinuses X-ray 3 views report', 'http://loinc.org'),
(56, 'Foto SPN Lateral + Waters', 'XR', '30764-4', 'Paranasal sinuses X-ray 2 views', 'http://loinc.org', '91723000', 'Paranasal sinus', 'http://snomed.info/sct', '30765-1', 'Paranasal sinuses X-ray 2 views report', 'http://loinc.org'),
(57, 'Foto Mastoid AP', 'XR', '30766-9', 'Mastoid X-ray AP', 'http://loinc.org', '91609008', 'Mastoid bone', 'http://snomed.info/sct', '30767-7', 'Mastoid X-ray AP report', 'http://loinc.org'),
(58, 'Foto Mastoid Lateral', 'XR', '30768-5', 'Mastoid X-ray Lateral', 'http://loinc.org', '91609008', 'Mastoid bone', 'http://snomed.info/sct', '30769-3', 'Mastoid X-ray Lateral report', 'http://loinc.org'),
(59, 'Foto Mastoid Schüller', 'XR', '30770-1', 'Mastoid X-ray Schüller', 'http://loinc.org', '91609008', 'Mastoid bone', 'http://snomed.info/sct', '30771-9', 'Mastoid X-ray Schüller report', 'http://loinc.org'),
(60, 'Foto Mastoid AP + Lateral', 'XR', '30772-7', 'Mastoid X-ray 2 views', 'http://loinc.org', '91609008', 'Mastoid bone', 'http://snomed.info/sct', '30773-5', 'Mastoid X-ray 2 views report', 'http://loinc.org'),
(61, 'Foto Mastoid AP + Lateral + Schüller', 'XR', '30774-3', 'Mastoid X-ray 3 views', 'http://loinc.org', '91609008', 'Mastoid bone', 'http://snomed.info/sct', '30775-0', 'Mastoid X-ray 3 views report', 'http://loinc.org'),
(62, 'Foto Mandibula AP', 'XR', '30776-8', 'Mandible X-ray AP', 'http://loinc.org', '123037009', 'Mandible', 'http://snomed.info/sct', '30777-6', 'Mandible X-ray AP report', 'http://loinc.org'),
(63, 'Foto Mandibula Lateral', 'XR', '30778-4', 'Mandible X-ray Lateral', 'http://loinc.org', '123037009', 'Mandible', 'http://snomed.info/sct', '30779-2', 'Mandible X-ray Lateral report', 'http://loinc.org'),
(64, 'Foto Mandibula OPG', 'XR', '30780-0', 'Mandible panoramic X-ray', 'http://loinc.org', '123037009', 'Mandible', 'http://snomed.info/sct', '30781-8', 'Mandible panoramic X-ray report', 'http://loinc.org'),
(65, 'Foto Mandibula AP + Lateral', 'XR', '30782-6', 'Mandible X-ray 2 views', 'http://loinc.org', '123037009', 'Mandible', 'http://snomed.info/sct', '30783-4', 'Mandible X-ray 2 views report', 'http://loinc.org'),
(66, 'Foto TMJ AP', 'XR', '30784-2', 'Temporomandibular joint X-ray AP', 'http://loinc.org', '53620006', 'Temporomandibular joint', 'http://snomed.info/sct', '30785-9', 'Temporomandibular joint X-ray AP report', 'http://loinc.org'),
(67, 'Foto TMJ Lateral', 'XR', '30786-7', 'Temporomandibular joint X-ray Lateral', 'http://loinc.org', '53620006', 'Temporomandibular joint', 'http://snomed.info/sct', '30787-5', 'Temporomandibular joint X-ray Lateral report', 'http://loinc.org'),
(68, 'Foto TMJ AP + Lateral', 'XR', '30788-3', 'Temporomandibular joint X-ray 2 views', 'http://loinc.org', '53620006', 'Temporomandibular joint', 'http://snomed.info/sct', '30789-1', 'Temporomandibular joint X-ray 2 views report', 'http://loinc.org'),
(69, 'Foto TMJ Buka & Tutup Mulut', 'XR', '30790-9', 'Temporomandibular joint X-ray functional', 'http://loinc.org', '53620006', 'Temporomandibular joint', 'http://snomed.info/sct', '30791-7', 'Temporomandibular joint X-ray functional report', 'http://loinc.org'),
(70, 'Foto Pelvis AP', 'XR', '30739-7', 'Pelvis X-ray', 'http://loinc.org', '12921003', 'Pelvis', 'http://snomed.info/sct', '30740-5', 'Pelvis X-ray diagnostic report', 'http://loinc.org'),
(71, 'Foto Pelvis AP + Lateral', 'XR', '30741-3', 'Pelvis X-ray 2 views', 'http://loinc.org', '12921003', 'Pelvis', 'http://snomed.info/sct', '30742-1', 'Pelvis X-ray 2 views report', 'http://loinc.org'),
(72, 'Foto Hip Joint AP', 'XR', '30784-0', 'Hip joint X-ray', 'http://loinc.org', '24136001', 'Hip joint', 'http://snomed.info/sct', '30785-7', 'Hip joint X-ray diagnostic report', 'http://loinc.org'),
(73, 'Foto Hip Joint Lateral', 'XR', '30786-5', 'Hip joint X-ray Lateral', 'http://loinc.org', '24136001', 'Hip joint', 'http://snomed.info/sct', '30787-3', 'Hip joint X-ray Lateral report', 'http://loinc.org'),
(74, 'Foto Hip Joint AP + Lateral', 'XR', '30788-1', 'Hip joint X-ray 2 views', 'http://loinc.org', '24136001', 'Hip joint', 'http://snomed.info/sct', '30789-9', 'Hip joint X-ray 2 views report', 'http://loinc.org'),
(75, 'Foto Hip Joint Kanan', 'XR', '30790-7', 'Hip joint X-ray Right', 'http://loinc.org', '24136001', 'Hip joint (right)', 'http://snomed.info/sct', '30791-5', 'Hip joint X-ray Right report', 'http://loinc.org'),
(76, 'Foto Hip Joint Kiri', 'XR', '30792-3', 'Hip joint X-ray Left', 'http://loinc.org', '24136001', 'Hip joint (left)', 'http://snomed.info/sct', '30793-1', 'Hip joint X-ray Left report', 'http://loinc.org'),
(77, 'Foto Femur AP', 'XR', '30794-9', 'Femur X-ray', 'http://loinc.org', '71341001', 'Femur', 'http://snomed.info/sct', '30795-6', 'Femur X-ray diagnostic report', 'http://loinc.org'),
(78, 'Foto Femur Lateral', 'XR', '30796-4', 'Femur X-ray Lateral', 'http://loinc.org', '71341001', 'Femur', 'http://snomed.info/sct', '30797-2', 'Femur X-ray Lateral report', 'http://loinc.org'),
(79, 'Foto Femur AP + Lateral', 'XR', '30798-0', 'Femur X-ray 2 views', 'http://loinc.org', '71341001', 'Femur', 'http://snomed.info/sct', '30799-8', 'Femur X-ray 2 views report', 'http://loinc.org'),
(80, 'Foto Femur Kanan', 'XR', '30800-6', 'Femur X-ray Right', 'http://loinc.org', '71341001', 'Femur (right)', 'http://snomed.info/sct', '30801-4', 'Femur X-ray Right report', 'http://loinc.org'),
(81, 'Foto Femur Kiri', 'XR', '30802-2', 'Femur X-ray Left', 'http://loinc.org', '71341001', 'Femur (left)', 'http://snomed.info/sct', '30803-0', 'Femur X-ray Left report', 'http://loinc.org'),
(82, 'Foto Genu Joint AP', 'XR', '30804-8', 'Knee joint X-ray', 'http://loinc.org', '72696002', 'Knee joint', 'http://snomed.info/sct', '30805-5', 'Knee joint X-ray diagnostic report', 'http://loinc.org'),
(83, 'Foto Genu Joint Lateral', 'XR', '30806-3', 'Knee joint X-ray Lateral', 'http://loinc.org', '72696002', 'Knee joint', 'http://snomed.info/sct', '30807-1', 'Knee joint X-ray Lateral report', 'http://loinc.org'),
(84, 'Foto Genu Joint AP + Lateral', 'XR', '30808-9', 'Knee joint X-ray 2 views', 'http://loinc.org', '72696002', 'Knee joint', 'http://snomed.info/sct', '30809-7', 'Knee joint X-ray 2 views report', 'http://loinc.org'),
(85, 'Foto Genu Joint Kanan', 'XR', '30810-5', 'Knee joint X-ray Right', 'http://loinc.org', '72696002', 'Knee joint (right)', 'http://snomed.info/sct', '30811-3', 'Knee joint X-ray Right report', 'http://loinc.org'),
(86, 'Foto Genu Joint Kiri', 'XR', '30812-1', 'Knee joint X-ray Left', 'http://loinc.org', '72696002', 'Knee joint (left)', 'http://snomed.info/sct', '30813-9', 'Knee joint X-ray Left report', 'http://loinc.org'),
(87, 'Foto Genu Joint Skyline', 'XR', '30814-7', 'Knee joint X-ray Skyline', 'http://loinc.org', '72696002', 'Knee joint', 'http://snomed.info/sct', '30815-4', 'Knee joint X-ray Skyline report', 'http://loinc.org'),
(88, 'Foto Cruris AP', 'XR', '30816-2', 'Lower leg X-ray AP', 'http://loinc.org', '71270000', 'Tibia & Fibula', 'http://snomed.info/sct', '30817-0', 'Lower leg X-ray diagnostic report', 'http://loinc.org'),
(89, 'Foto Cruris Lateral', 'XR', '30818-8', 'Lower leg X-ray Lateral', 'http://loinc.org', '71270000', 'Tibia & Fibula', 'http://snomed.info/sct', '30819-6', 'Lower leg X-ray Lateral report', 'http://loinc.org'),
(90, 'Foto Cruris AP + Lateral', 'XR', '30820-4', 'Lower leg X-ray 2 views', 'http://loinc.org', '71270000', 'Tibia & Fibula', 'http://snomed.info/sct', '30821-2', 'Lower leg X-ray 2 views report', 'http://loinc.org'),
(91, 'Foto Cruris Kanan', 'XR', '30822-0', 'Lower leg X-ray Right', 'http://loinc.org', '71270000', 'Tibia & Fibula (right)', 'http://snomed.info/sct', '30823-8', 'Lower leg X-ray Right report', 'http://loinc.org'),
(92, 'Foto Cruris Kiri', 'XR', '30824-6', 'Lower leg X-ray Left', 'http://loinc.org', '71270000', 'Tibia & Fibula (left)', 'http://snomed.info/sct', '30825-3', 'Lower leg X-ray Left report', 'http://loinc.org'),
(93, 'Foto Ankle Joint AP', 'XR', '30826-1', 'Ankle joint X-ray', 'http://loinc.org', '70258002', 'Ankle joint', 'http://snomed.info/sct', '30827-9', 'Ankle joint X-ray diagnostic report', 'http://loinc.org'),
(94, 'Foto Ankle Joint Lateral', 'XR', '30828-7', 'Ankle joint X-ray Lateral', 'http://loinc.org', '70258002', 'Ankle joint', 'http://snomed.info/sct', '30829-5', 'Ankle joint X-ray Lateral report', 'http://loinc.org'),
(95, 'Foto Ankle Joint AP + Lateral', 'XR', '30830-3', 'Ankle joint X-ray 2 views', 'http://loinc.org', '70258002', 'Ankle joint', 'http://snomed.info/sct', '30831-1', 'Ankle joint X-ray 2 views report', 'http://loinc.org'),
(96, 'Foto Ankle Joint Mortise', 'XR', '30832-9', 'Ankle joint X-ray Mortise', 'http://loinc.org', '70258002', 'Ankle joint', 'http://snomed.info/sct', '30833-7', 'Ankle joint X-ray Mortise report', 'http://loinc.org'),
(97, 'Foto Ankle Joint Kanan', 'XR', '30834-5', 'Ankle joint X-ray Right', 'http://loinc.org', '70258002', 'Ankle joint (right)', 'http://snomed.info/sct', '30835-2', 'Ankle joint X-ray Right report', 'http://loinc.org'),
(98, 'Foto Ankle Joint Kiri', 'XR', '30836-0', 'Ankle joint X-ray Left', 'http://loinc.org', '70258002', 'Ankle joint (left)', 'http://snomed.info/sct', '30837-8', 'Ankle joint X-ray Left report', 'http://loinc.org'),
(99, 'Foto Pedis AP', 'XR', '30838-6', 'Foot X-ray', 'http://loinc.org', '56459004', 'Foot', 'http://snomed.info/sct', '30839-4', 'Foot X-ray diagnostic report', 'http://loinc.org'),
(100, 'Foto Pedis Lateral', 'XR', '30840-2', 'Foot X-ray Lateral', 'http://loinc.org', '56459004', 'Foot', 'http://snomed.info/sct', '30841-0', 'Foot X-ray Lateral report', 'http://loinc.org'),
(101, 'Foto Pedis AP + Lateral', 'XR', '30842-8', 'Foot X-ray 2 views', 'http://loinc.org', '56459004', 'Foot', 'http://snomed.info/sct', '30843-6', 'Foot X-ray 2 views report', 'http://loinc.org'),
(102, 'Foto Pedis Oblique', 'XR', '30844-4', 'Foot X-ray Oblique', 'http://loinc.org', '56459004', 'Foot', 'http://snomed.info/sct', '30845-1', 'Foot X-ray Oblique report', 'http://loinc.org'),
(103, 'Foto Pedis Kanan', 'XR', '30846-9', 'Foot X-ray Right', 'http://loinc.org', '56459004', 'Foot (right)', 'http://snomed.info/sct', '30847-7', 'Foot X-ray Right report', 'http://loinc.org'),
(104, 'Foto Pedis Kiri', 'XR', '30848-5', 'Foot X-ray Left', 'http://loinc.org', '56459004', 'Foot (left)', 'http://snomed.info/sct', '30849-3', 'Foot X-ray Left report', 'http://loinc.org'),
(105, 'Foto Cervical AP', 'XR', '30751-2', 'Cervical spine X-ray', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '30752-0', 'Cervical spine X-ray diagnostic report', 'http://loinc.org'),
(106, 'Foto Cervical Lateral', 'XR', '30753-8', 'Cervical spine X-ray Lateral', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '30754-6', 'Cervical spine X-ray Lateral report', 'http://loinc.org'),
(107, 'Foto Cervical AP + Lateral', 'XR', '30755-3', 'Cervical spine X-ray 2 views', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '30756-1', 'Cervical spine X-ray 2 views report', 'http://loinc.org'),
(108, 'Foto Cervical Oblique', 'XR', '30757-9', 'Cervical spine X-ray Oblique', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '30758-7', 'Cervical spine X-ray Oblique report', 'http://loinc.org'),
(109, 'Foto Cervical Fleksi–Ekstensi', 'XR', '30759-5', 'Cervical spine X-ray Flexion extension', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '30760-3', 'Cervical spine X-ray Flexion extension report', 'http://loinc.org'),
(110, 'Foto Cervical Open Mouth (Odontoid)', 'XR', '30761-9', 'Cervical spine X-ray Odontoid', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct', '30762-7', 'Cervical spine X-ray Odontoid report', 'http://loinc.org'),
(111, 'Foto Thoracal AP', 'XR', '30763-5', 'Thoracic spine X-ray', 'http://loinc.org', '51185008', 'Thoracic spine', 'http://snomed.info/sct', '30764-3', 'Thoracic spine X-ray diagnostic report', 'http://loinc.org'),
(112, 'Foto Thoracal Lateral', 'XR', '30765-0', 'Thoracic spine X-ray Lateral', 'http://loinc.org', '51185008', 'Thoracic spine', 'http://snomed.info/sct', '30766-8', 'Thoracic spine X-ray Lateral report', 'http://loinc.org'),
(113, 'Foto Thoracal AP + Lateral', 'XR', '30767-6', 'Thoracic spine X-ray 2 views', 'http://loinc.org', '51185008', 'Thoracic spine', 'http://snomed.info/sct', '30768-4', 'Thoracic spine X-ray 2 views report', 'http://loinc.org'),
(114, 'Foto Thoracal Oblique', 'XR', '30769-2', 'Thoracic spine X-ray Oblique', 'http://loinc.org', '51185008', 'Thoracic spine', 'http://snomed.info/sct', '30770-0', 'Thoracic spine X-ray Oblique report', 'http://loinc.org'),
(115, 'Foto Thoracal Fleksi–Ekstensi', 'XR', '30771-8', 'Thoracic spine X-ray Flexion extension', 'http://loinc.org', '51185008', 'Thoracic spine', 'http://snomed.info/sct', '30772-6', 'Thoracic spine X-ray Flexion extension report', 'http://loinc.org'),
(116, 'Foto Thoracolumbal AP', 'XR', '30773-4', 'Thoracolumbar spine X-ray', 'http://loinc.org', '39726000', 'Thoracolumbar spine', 'http://snomed.info/sct', '30774-2', 'Thoracolumbar spine X-ray diagnostic report', 'http://loinc.org'),
(117, 'Foto Thoracolumbal Lateral', 'XR', '30775-9', 'Thoracolumbar spine X-ray Lateral', 'http://loinc.org', '39726000', 'Thoracolumbar spine', 'http://snomed.info/sct', '30776-7', 'Thoracolumbar spine X-ray Lateral report', 'http://loinc.org'),
(118, 'Foto Thoracolumbal AP + Lateral', 'XR', '30777-5', 'Thoracolumbar spine X-ray 2 views', 'http://loinc.org', '39726000', 'Thoracolumbar spine', 'http://snomed.info/sct', '30778-3', 'Thoracolumbar spine X-ray 2 views report', 'http://loinc.org'),
(119, 'Foto Thoracolumbal Fleksi–Ekstensi', 'XR', '30779-1', 'Thoracolumbar spine X-ray Flexion extension', 'http://loinc.org', '39726000', 'Thoracolumbar spine', 'http://snomed.info/sct', '30780-9', 'Thoracolumbar spine X-ray Flexion extension report', 'http://loinc.org'),
(120, 'Foto Lumbar AP', 'XR', '30781-5', 'Lumbar spine X-ray', 'http://loinc.org', '81650007', 'Lumbar spine', 'http://snomed.info/sct', '30782-3', 'Lumbar spine X-ray diagnostic report', 'http://loinc.org'),
(121, 'Foto Lumbar Lateral', 'XR', '30783-1', 'Lumbar spine X-ray Lateral', 'http://loinc.org', '81650007', 'Lumbar spine', 'http://snomed.info/sct', '30784-9', 'Lumbar spine X-ray Lateral report', 'http://loinc.org'),
(122, 'Foto Lumbar AP + Lateral', 'XR', '30785-6', 'Lumbar spine X-ray 2 views', 'http://loinc.org', '81650007', 'Lumbar spine', 'http://snomed.info/sct', '30786-4', 'Lumbar spine X-ray 2 views report', 'http://loinc.org'),
(123, 'Foto Lumbar Oblique', 'XR', '30787-2', 'Lumbar spine X-ray Oblique', 'http://loinc.org', '81650007', 'Lumbar spine', 'http://snomed.info/sct', '30788-0', 'Lumbar spine X-ray Oblique report', 'http://loinc.org'),
(124, 'Foto Lumbar Fleksi–Ekstensi', 'XR', '30789-8', 'Lumbar spine X-ray Flexion extension', 'http://loinc.org', '81650007', 'Lumbar spine', 'http://snomed.info/sct', '30790-6', 'Lumbar spine X-ray Flexion extension report', 'http://loinc.org'),
(125, 'Foto Lumbosacral AP', 'XR', '30791-4', 'Lumbosacral spine X-ray', 'http://loinc.org', '81750000', 'Lumbosacral spine', 'http://snomed.info/sct', '30792-2', 'Lumbosacral spine X-ray diagnostic report', 'http://loinc.org'),
(126, 'Foto Lumbosacral Lateral', 'XR', '30793-0', 'Lumbosacral spine X-ray Lateral', 'http://loinc.org', '81750000', 'Lumbosacral spine', 'http://snomed.info/sct', '30794-8', 'Lumbosacral spine X-ray Lateral report', 'http://loinc.org'),
(127, 'Foto Lumbosacral AP + Lateral', 'XR', '30795-5', 'Lumbosacral spine X-ray 2 views', 'http://loinc.org', '81750000', 'Lumbosacral spine', 'http://snomed.info/sct', '30796-3', 'Lumbosacral spine X-ray 2 views report', 'http://loinc.org'),
(128, 'Foto Lumbosacral Fleksi–Ekstensi', 'XR', '30797-1', 'Lumbosacral spine X-ray Flexion extension', 'http://loinc.org', '81750000', 'Lumbosacral spine', 'http://snomed.info/sct', '30798-9', 'Lumbosacral spine X-ray Flexion extension report', 'http://loinc.org'),
(129, 'Foto Clavicula AP', 'XR', '30802-7', 'Clavicle X-ray AP', 'http://loinc.org', '123037006', 'Clavicle', 'http://snomed.info/sct', '30803-5', 'Clavicle X-ray AP report', 'http://loinc.org'),
(130, 'Foto Clavicula Lateral', 'XR', '30804-3', 'Clavicle X-ray Lateral', 'http://loinc.org', '123037006', 'Clavicle', 'http://snomed.info/sct', '30805-0', 'Clavicle X-ray Lateral report', 'http://loinc.org'),
(131, 'Foto Clavicula AP Axial', 'XR', '30800-1', 'Clavicle X-ray AP Axial', 'http://loinc.org', '123037006', 'Clavicle', 'http://snomed.info/sct', '30801-9', 'Clavicle X-ray AP Axial report', 'http://loinc.org'),
(132, 'Foto Clavicula AP + Lateral', 'XR', '30806-8', 'Clavicle X-ray 2 views', 'http://loinc.org', '123037006', 'Clavicle', 'http://snomed.info/sct', '30807-6', 'Clavicle X-ray 2 views report', 'http://loinc.org'),
(133, 'Foto Shoulder Joint AP', 'XR', '30808-2', 'Shoulder joint X-ray AP', 'http://loinc.org', '24131001', 'Shoulder joint', 'http://snomed.info/sct', '30809-0', 'Shoulder joint X-ray diagnostic report', 'http://loinc.org'),
(134, 'Foto Shoulder Joint Lateral', 'XR', '30810-8', 'Shoulder joint X-ray Lateral', 'http://loinc.org', '24131001', 'Shoulder joint', 'http://snomed.info/sct', '30813-0', 'Shoulder joint X-ray Axial report', 'http://loinc.org'),
(135, 'Foto Shoulder Joint AP + Lateral', 'XR', '30814-8', 'Shoulder joint X-ray 2 views', 'http://loinc.org', '24131001', 'Shoulder joint', 'http://snomed.info/sct', '30815-5', 'Shoulder joint X-ray 2 views report', 'http://loinc.org'),
(136, 'Foto Shoulder Joint Y View', 'XR', '30816-6', 'Shoulder joint X-ray Y view', 'http://loinc.org', '24131001', 'Shoulder joint', 'http://snomed.info/sct', '30817-4', 'Shoulder joint X-ray Y view report', 'http://loinc.org'),
(137, 'Foto Humerus Proksimal AP Kanan', 'XR', '30818-2', 'Humerus X-ray Proximal AP Right', 'http://loinc.org', '123038005', 'Humerus (right)', 'http://snomed.info/sct', '30819-0', 'Humerus X-ray Proximal AP Right report', 'http://loinc.org'),
(138, 'Foto Humerus Proksimal AP Kiri', 'XR', '30820-8', 'Humerus X-ray Proximal AP Left', 'http://loinc.org', '123038005', 'Humerus (left)', 'http://snomed.info/sct', '30821-6', 'Humerus X-ray Proximal AP Left report', 'http://loinc.org'),
(139, 'Foto Humerus Proksimal Lateral Kanan', 'XR', '30822-4', 'Humerus X-ray Proximal Lateral Right', 'http://loinc.org', '123038005', 'Humerus (right)', 'http://snomed.info/sct', '30823-2', 'Humerus X-ray Proximal Lateral Right report', 'http://loinc.org'),
(140, 'Foto Humerus Proksimal Lateral Kiri', 'XR', '30824-0', 'Humerus X-ray Proximal Lateral Left', 'http://loinc.org', '123038005', 'Humerus (left)', 'http://snomed.info/sct', '30825-7', 'Humerus X-ray Proximal Lateral Left report', 'http://loinc.org'),
(141, 'Foto Humerus Distal AP Kanan', 'XR', '30826-5', 'Humerus X-ray Distal AP Right', 'http://loinc.org', '123038005', 'Humerus (right)', 'http://snomed.info/sct', '30827-3', 'Humerus X-ray Distal AP Right report', 'http://loinc.org'),
(142, 'Foto Humerus Distal AP Kiri', 'XR', '30828-1', 'Humerus X-ray Distal AP Left', 'http://loinc.org', '123038005', 'Humerus (left)', 'http://snomed.info/sct', '30829-9', 'Humerus X-ray Distal AP Left report', 'http://loinc.org'),
(143, 'Foto Humerus Distal Lateral Kanan', 'XR', '30830-5', 'Humerus X-ray Distal Lateral Right', 'http://loinc.org', '123038005', 'Humerus (right)', 'http://snomed.info/sct', '30831-3', 'Humerus X-ray Distal Lateral Right report', 'http://loinc.org'),
(144, 'Foto Humerus Distal Lateral Kiri', 'XR', '30832-1', 'Humerus X-ray Distal Lateral Left', 'http://loinc.org', '123038005', 'Humerus (left)', 'http://snomed.info/sct', '30833-9', 'Humerus X-ray Distal Lateral Left report', 'http://loinc.org'),
(145, 'Foto Humerus AP + Lateral Kanan', 'XR', '30834-7', 'Humerus X-ray 2 views Right', 'http://loinc.org', '123038005', 'Humerus (right)', 'http://snomed.info/sct', '30835-4', 'Humerus X-ray 2 views Right report', 'http://loinc.org'),
(146, 'Foto Humerus AP + Lateral Kiri', 'XR', '30836-2', 'Humerus X-ray 2 views Left', 'http://loinc.org', '123038005', 'Humerus (left)', 'http://snomed.info/sct', '30837-0', 'Humerus X-ray 2 views Left report', 'http://loinc.org'),
(147, 'Foto Elbow Joint AP Kanan', 'XR', '30838-1', 'Elbow joint X-ray AP Right', 'http://loinc.org', '24132001', 'Elbow joint (right)', 'http://snomed.info/sct', '30839-9', 'Elbow joint X-ray AP Right report', 'http://loinc.org'),
(148, 'Foto Elbow Joint AP Kiri', 'XR', '30840-7', 'Elbow joint X-ray AP Left', 'http://loinc.org', '24132001', 'Elbow joint (left)', 'http://snomed.info/sct', '30841-5', 'Elbow joint X-ray AP Left report', 'http://loinc.org'),
(149, 'Foto Elbow Joint Lateral Kanan', 'XR', '30842-3', 'Elbow joint X-ray Lateral Right', 'http://loinc.org', '24132001', 'Elbow joint (right)', 'http://snomed.info/sct', '30843-1', 'Elbow joint X-ray Lateral Right report', 'http://loinc.org'),
(150, 'Foto Elbow Joint Lateral Kiri', 'XR', '30844-9', 'Elbow joint X-ray Lateral Left', 'http://loinc.org', '24132001', 'Elbow joint (left)', 'http://snomed.info/sct', '30845-6', 'Elbow joint X-ray Lateral Left report', 'http://loinc.org'),
(151, 'Foto Elbow Joint AP + Lateral Kanan', 'XR', '30846-4', 'Elbow joint X-ray 2 views Right', 'http://loinc.org', '24132001', 'Elbow joint (right)', 'http://snomed.info/sct', '30847-2', 'Elbow joint X-ray 2 views Right report', 'http://loinc.org'),
(152, 'Foto Elbow Joint AP + Lateral Kiri', 'XR', '30848-0', 'Elbow joint X-ray 2 views Left', 'http://loinc.org', '24132001', 'Elbow joint (left)', 'http://snomed.info/sct', '30849-8', 'Elbow joint X-ray 2 views Left report', 'http://loinc.org'),
(153, 'Foto Forearm AP Kanan', 'XR', '30850-4', 'Forearm X-ray AP Right', 'http://loinc.org', '71342001', 'Radius & Ulna (right)', 'http://snomed.info/sct', '30851-2', 'Forearm X-ray AP Right report', 'http://loinc.org'),
(154, 'Foto Forearm AP Kiri', 'XR', '30852-0', 'Forearm X-ray AP Left', 'http://loinc.org', '71342001', 'Radius & Ulna (left)', 'http://snomed.info/sct', '30853-8', 'Forearm X-ray AP Left report', 'http://loinc.org'),
(155, 'Foto Forearm Lateral Kanan', 'XR', '30854-6', 'Forearm X-ray Lateral Right', 'http://loinc.org', '71342001', 'Radius & Ulna (right)', 'http://snomed.info/sct', '30855-3', 'Forearm X-ray Lateral Right report', 'http://loinc.org'),
(156, 'Foto Forearm Lateral Kiri', 'XR', '30856-1', 'Forearm X-ray Lateral Left', 'http://loinc.org', '71342001', 'Radius & Ulna (left)', 'http://snomed.info/sct', '30857-9', 'Forearm X-ray Lateral Left report', 'http://loinc.org'),
(157, 'Foto Forearm AP + Lateral Kanan', 'XR', '30858-7', 'Forearm X-ray 2 views Right', 'http://loinc.org', '71342001', 'Radius & Ulna (right)', 'http://snomed.info/sct', '30859-5', 'Forearm X-ray 2 views Right report', 'http://loinc.org'),
(158, 'Foto Forearm AP + Lateral Kiri', 'XR', '30860-3', 'Forearm X-ray 2 views Left', 'http://loinc.org', '71342001', 'Radius & Ulna (left)', 'http://snomed.info/sct', '30861-1', 'Forearm X-ray 2 views Left report', 'http://loinc.org'),
(159, 'Foto Wrist Joint AP Kanan', 'XR', '30862-9', 'Wrist joint X-ray AP Right', 'http://loinc.org', '71850001', 'Wrist joint (right)', 'http://snomed.info/sct', '30863-7', 'Wrist joint X-ray AP Right report', 'http://loinc.org'),
(160, 'Foto Wrist Joint AP Kiri', 'XR', '30864-5', 'Wrist joint X-ray AP Left', 'http://loinc.org', '71850001', 'Wrist joint (left)', 'http://snomed.info/sct', '30865-2', 'Wrist joint X-ray AP Left report', 'http://loinc.org'),
(161, 'Foto Wrist Joint Lateral Kanan', 'XR', '30866-0', 'Wrist joint X-ray Lateral Right', 'http://loinc.org', '71850001', 'Wrist joint (right)', 'http://snomed.info/sct', '30867-8', 'Wrist joint X-ray Lateral Right report', 'http://loinc.org'),
(162, 'Foto Wrist Joint Lateral Kiri', 'XR', '30868-8', 'Wrist joint X-ray Lateral Left', 'http://loinc.org', '71850001', 'Wrist joint (left)', 'http://snomed.info/sct', '30869-6', 'Wrist joint X-ray Lateral Left report', 'http://loinc.org'),
(163, 'Foto Wrist Joint AP + Lateral Kanan', 'XR', '30870-4', 'Wrist joint X-ray 2 views Right', 'http://loinc.org', '71850001', 'Wrist joint (right)', 'http://snomed.info/sct', '30871-2', 'Wrist joint X-ray 2 views Right report', 'http://loinc.org'),
(164, 'Foto Wrist Joint AP + Lateral Kiri', 'XR', '30872-0', 'Wrist joint X-ray 2 views Left', 'http://loinc.org', '71850001', 'Wrist joint (left)', 'http://snomed.info/sct', '30873-8', 'Wrist joint X-ray 2 views Left report', 'http://loinc.org'),
(165, 'Foto Hand AP Kanan', 'XR', '30874-6', 'Hand X-ray AP Right', 'http://loinc.org', '56458002', 'Hand (right)', 'http://snomed.info/sct', '30875-3', 'Hand X-ray AP Right report', 'http://loinc.org'),
(166, 'Foto Hand AP Kiri', 'XR', '30876-1', 'Hand X-ray AP Left', 'http://loinc.org', '56458002', 'Hand (left)', 'http://snomed.info/sct', '30877-9', 'Hand X-ray AP Left report', 'http://loinc.org'),
(167, 'Foto Hand Lateral Kanan', 'XR', '30878-7', 'Hand X-ray Lateral Right', 'http://loinc.org', '56458002', 'Hand (right)', 'http://snomed.info/sct', '30879-5', 'Hand X-ray Lateral Right report', 'http://loinc.org'),
(168, 'Foto Hand Lateral Kiri', 'XR', '30880-3', 'Hand X-ray Lateral Left', 'http://loinc.org', '56458002', 'Hand (left)', 'http://snomed.info/sct', '30881-1', 'Hand X-ray Lateral Left report', 'http://loinc.org'),
(169, 'Foto Hand Oblique Kanan', 'XR', '30882-9', 'Hand X-ray Oblique Right', 'http://loinc.org', '56458002', 'Hand (right)', 'http://snomed.info/sct', '30883-7', 'Hand X-ray Oblique Right report', 'http://loinc.org'),
(170, 'Foto Hand Oblique Kiri', 'XR', '30884-5', 'Hand X-ray Oblique Left', 'http://loinc.org', '56458002', 'Hand (left)', 'http://snomed.info/sct', '30885-2', 'Hand X-ray Oblique Left report', 'http://loinc.org'),
(171, 'Foto Hand AP + Lateral Kanan', 'XR', '30886-0', 'Hand X-ray 2 views Right', 'http://loinc.org', '56458002', 'Hand (right)', 'http://snomed.info/sct', '30887-8', 'Hand X-ray 2 views Right report', 'http://loinc.org'),
(172, 'Foto Hand AP + Lateral Kiri', 'XR', '30888-6', 'Hand X-ray 2 views Left', 'http://loinc.org', '56458002', 'Hand (left)', 'http://snomed.info/sct', '30889-4', 'Hand X-ray 2 views Left report', 'http://loinc.org'),
(173, 'USG Hepar', 'US', '30793-4', 'Ultrasound Liver', 'http://loinc.org', '10200004', 'Liver (Hepar)', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(174, 'USG Kandung Empedu', 'US', '30792-6', 'Ultrasound Gallbladder', 'http://loinc.org', '28231008', 'Gallbladder (Kandung empedu)', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(175, 'USG Lien', 'US', '30791-8', 'Ultrasound Spleen', 'http://loinc.org', '78961009', 'Spleen (Lien)', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(176, 'USG Pankreas', 'US', '30790-0', 'Ultrasound Pancreas', 'http://loinc.org', '15776009', 'Pancreas', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(177, 'USG Ginjal Kanan', 'US', '30794-2', 'Ultrasound Kidney', 'http://loinc.org', '181414000', 'Right kidney', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(178, 'USG Ginjal Kiri', 'US', '30794-2', 'Ultrasound Kidney', 'http://loinc.org', '181415004', 'Left kidney', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(179, 'USG Vesika Urinaria', 'US', '30795-9', 'Ultrasound Bladder', 'http://loinc.org', '89837001', 'Urinary bladder', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(180, 'USG Ginjal / Renal Bilateral', 'US', '38036-0', 'Ultrasound Kidney', 'http://loinc.org', '88837001', 'Kidney structure', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(181, 'USG Ginjal Terbatas', 'US', '38035-2', 'US Kidney limited', 'http://loinc.org', '88837001', 'Kidney structure', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(182, 'USG Ginjal Bilateral', 'US', '43774-9', 'US Kidney - bilateral', 'http://loinc.org', '88837001', 'Kidney structure', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(183, 'USG Ginjal + Vesika Urinaria', 'US', '69402-6', 'US Kidney - bilateral and Urinary bladder', 'http://loinc.org', '88837001', 'Kidney structure', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(184, 'USG Ginjal + Vesika Urinaria (Body Site : Urinary bladder)', 'US', '69402-6', 'US Kidney - bilateral and Urinary bladder', 'http://loinc.org', '89837001', 'Urinary bladder', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(185, 'USG Kandung Kemih Terbatas', 'US', '69280-6', 'US Urinary bladder limited', 'http://loinc.org', '89837001', 'Urinary bladder', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(186, 'USG Saluran Kemih', 'US', '30795-9', 'Ultrasound Bladder', 'http://loinc.org', '89837001', 'Urinary bladder', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(187, 'USG Ureter', 'US', '30800-7', 'Ultrasound Ureter', 'http://loinc.org', '31342008', 'Ureter', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(188, 'USG Thyroid', 'US', '30797-5', 'Ultrasound Thyroid', 'http://loinc.org', '10200004', 'Thyroid gland', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(189, 'USG Whole Abdomen', 'US', '30793-4', 'Ultrasound Liver', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(190, 'USG Appendix', 'US', '30804-9', 'Ultrasound Appendix', 'http://loinc.org', '81607009', 'Appendix vermiformis', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(191, 'USG Doppler Arteri Karotis', 'US', '30805-6', 'Ultrasound Doppler Carotid', 'http://loinc.org', '49949009', 'Carotid artery', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(192, 'USG Doppler Vena Femoral', 'US', '30806-4', 'Ultrasound Doppler Vein', 'http://loinc.org', '182346003', 'Femoral vein', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(193, 'USG Doppler Arteri Renal', 'US', '30807-2', 'Ultrasound Doppler Renal', 'http://loinc.org', '6736007', 'Renal artery', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(194, 'USG Doppler Limbs', 'US', '30808-0', 'Ultrasound Doppler Limb', 'http://loinc.org', '8171007', 'Peripheral artery of limb', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(195, 'USG Doppler Abdomen', 'US', '30809-8', 'Ultrasound Doppler Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(196, 'USG KGB', 'US', '30810-6', 'Ultrasound Lymph Node', 'http://loinc.org', '39607008', 'Lymph node', 'http://snomed.info/sct', '18748-4', 'Diagnostic imaging study', 'http://loinc.org'),
(197, 'Prosedur studi ekokardiogram jantung', 'US', '18106-5', 'Cardiac echo study Procedure', 'http://loinc.org', 'T-32000', 'Heart', 'http://snomed.info/sct', '59281-6', 'US Heart Transthoracic', 'http://loinc.org'),
(198, 'Echocardiogram study', 'US', '18003-4', 'Echocardiogram study', 'http://loinc.org', '113257007', 'Cardiovascular system', 'http://snomed.info/sct', '778-1', 'Left ventricular ejection fraction (LVEF)', 'http://loinc.org');

-- --------------------------------------------------------

--
-- Table structure for table `master_service_prices`
--

DROP TABLE IF EXISTS `master_service_prices`;
CREATE TABLE IF NOT EXISTS `master_service_prices` (
  `id_master_service_prices` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) NOT NULL,
  `service_category` varchar(255) NOT NULL,
  `modality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'XR, US, MR, DLL',
  `patient_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Kelas Inap Untuk Ranap (Jika Ada)',
  `insurance_type` varchar(255) NOT NULL COMMENT 'UMUM, BPJS, DLL',
  `base_price` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Harga dasar',
  `doctor_fee` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Jasa dokter',
  `radiographers_fee` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Jasa radiografer',
  `facility_fee` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Jasa RS',
  `equipment_fee` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'BHP Alkes',
  `total_price` decimal(15,2) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL COMMENT 'True Or False',
  `effective_date` date NOT NULL COMMENT 'Tanggal Insert',
  `expired_date` date NOT NULL COMMENT 'Otomatis 1 Tahun Setelah Insert',
  PRIMARY KEY (`id_master_service_prices`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_service_prices`
--

INSERT INTO `master_service_prices` (`id_master_service_prices`, `service_name`, `service_category`, `modality`, `patient_class`, `insurance_type`, `base_price`, `doctor_fee`, `radiographers_fee`, `facility_fee`, `equipment_fee`, `total_price`, `is_active`, `effective_date`, `expired_date`) VALUES
(1, 'USG Echo', 'USG', 'USG', '', 'UMUM', 325000.00, 0.00, 0.00, 0.00, 0.00, 325000.00, 1, '2025-12-30', '2026-12-30'),
(7, 'OS Nasale', 'Nasale', 'XR', '', 'Umum dan BPJS', 95000.00, 0.00, 0.00, 0.00, 0.00, 95000.00, 1, '2026-01-14', '2027-01-14'),
(8, 'LSAP/Lat', 'Ap/Lat', 'XR', '', 'Umum dan BPJS', 190000.00, 0.00, 0.00, 0.00, 0.00, 190000.00, 1, '2026-01-14', '2027-01-14'),
(9, 'LS Ap/Lat', 'Ap/Lat', 'XR', '', 'Umum dan BPJS', 190000.00, 0.00, 0.00, 0.00, 0.00, 190000.00, 1, '2026-01-14', '2027-01-14'),
(10, 'Genue AP/LAT', 'Ap/Lat', 'XR', '', 'Umum dan BPJS', 195000.00, 0.00, 0.00, 0.00, 0.00, 195000.00, 1, '2026-01-14', '2027-01-14'),
(11, 'Pelvis Ap/Lat', 'Ap/Lat', 'XR', '', 'Umum dan BPJS', 95000.00, 0.00, 0.00, 0.00, 0.00, 95000.00, 1, '2026-01-14', '2027-01-14');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `id_question` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_questionnaire` varchar(255) DEFAULT NULL COMMENT 'Dari satu sehat',
  `link_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Nilai ID Pertanyaan Dari Satu Sehat',
  `question_group` varchar(255) NOT NULL,
  `question_text` text NOT NULL COMMENT 'Pertanyaan dalam kalimat',
  `question_type` varchar(255) NOT NULL COMMENT 'boolean, choice, quantity, text',
  `satu_sehat` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_question`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id_question`, `id_questionnaire`, `link_id`, `question_group`, `question_text`, `question_type`, `satu_sehat`) VALUES
(1, 'fc085f8b-26a1-42bb-a9a9-bcb9537892a6', 'dbuAQsNk0CqVULUw1oBv0CceOSFUXEHi', 'Kehamilan', 'Apakah anda sedang hamil?', 'boolean', 1),
(2, 'e1053ff4-2d05-473c-a55a-b93a385906fb', 'hBqxBAFKAVxOx04QfDQjhPBQXgnNcljo', 'Kehamilan', 'Berapa bulan usia kehamilan anda?', 'integer', 1),
(3, '1ea684be-7075-4f5d-85a7-b1c4860f9b4b', 'JPNYRfPetunug59qJd9TOTtPyEHYYf45', 'Penyakit Kronis', 'Apakah anda memiliki penyakit kronis?', 'boolean', 1),
(4, '4e58529b-700a-447e-aa41-2655cc214a72', '9Lv80ec7cgJc4DwJJbjKJCahD3Bk40Ch', 'Implan', 'Apakah anda memiliki implan pada organ tubuh?', 'boolean', 1),
(5, 'f4dc7bd8-474a-46a4-afa6-6f7775075ed6', '0jam8BrZlwefatI1mYMCCP7DPwpoJWc8', 'Implan', 'Apakah anda menggunakan implan silikon?', 'boolean', 1);

-- --------------------------------------------------------

--
-- Table structure for table `question_response`
--

DROP TABLE IF EXISTS `question_response`;
CREATE TABLE IF NOT EXISTS `question_response` (
  `id_question_response` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_question` int UNSIGNED NOT NULL,
  `id_radiologi` int UNSIGNED NOT NULL,
  `id_questionnaire_response` varchar(255) DEFAULT NULL COMMENT 'dari satu sehat',
  `answer` varchar(255) NOT NULL,
  PRIMARY KEY (`id_question_response`),
  KEY `id_question` (`id_question`),
  KEY `id_radiologi` (`id_radiologi`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi`
--

DROP TABLE IF EXISTS `radiologi`;
CREATE TABLE IF NOT EXISTS `radiologi` (
  `id_radiologi` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED DEFAULT NULL COMMENT 'Akses radiografer terisi setelah diterima',
  `id_pasien` int NOT NULL COMMENT 'Dari SIMRS',
  `id_kunjungan` int NOT NULL COMMENT 'Dari SIMRS',
  `accession_number` varchar(255) NOT NULL COMMENT 'RSES-RAD-datetime-id',
  `id_service_request` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Response dari satu sehat',
  `id_procedure` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Response Dari Satu Sehat',
  `id_imaging_study` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Response Dari Satu Sehat ',
  `id_observation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Response Dari Satu Sehat ',
  `id_diagnostic_report` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Response Dari Satu Sehat ',
  `pacs` tinyint(1) DEFAULT NULL COMMENT 'true or false',
  `nama_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Perlu validasi pembaharuan dari SIMRS',
  `priority` enum('routine','urgent','stat') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'routine(Biasa), urgent(Segera), stat(Gawat)',
  `asal_kiriman` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Diisi dengan Ruangan, Unit atau poli',
  `alat_pemeriksa` varchar(255) NOT NULL COMMENT 'Rontgent, USG, MRI, CT, Dll ',
  `kode_dokter_pengirim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'kode dokter (SIMRS-BPJS) pengirim',
  `ihs_dokter_pengirim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'id practitioner satu  sehat dokter pengirim',
  `nama_dokter_pengirim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nama Dokter pengirim',
  `kode_dokter_penerima` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'kode dokter (SIMRS-BPJS) Penerima',
  `ihs_dokter_penerima` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'id practitioner satu  sehat dokter penerima',
  `nama_dokter_penerima` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Nama Dokter Penerima',
  `radiografer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Nama petugas terisi setelah diterima',
  `pesan` text,
  `kesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'Kesimpulan dari temuan',
  `klinis` json DEFAULT NULL COMMENT 'Mapping SNOMED CT',
  `permintaan_pemeriksaan` json DEFAULT NULL COMMENT 'Berdasarkan LOINC',
  `kv` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'faktor eksposi (x-ray)',
  `ma` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'faktor eksposi (x-ray)',
  `sec` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'faktor eksposi (x-ray)',
  `tujuan` enum('Rajal','Ranap') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Rajal/Ranap',
  `pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'UMUM, BPJS',
  `datetime_diminta` datetime NOT NULL COMMENT 'permintaan dibuat',
  `datetime_dikerjakan` datetime DEFAULT NULL COMMENT 'petugas mengisi kesan, klinis, faktor exposi, dokter penerima',
  `datetime_hasil` datetime DEFAULT NULL COMMENT 'Menunggu hasil, petugas mengisi expert',
  `datetime_selesai` datetime DEFAULT NULL COMMENT 'Petugas awal mencetak',
  `status_pemeriksaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Diminta, Dikerjakan, Hasil, Selesai, Batal',
  `alasan_pembatalan` text,
  PRIMARY KEY (`id_radiologi`),
  UNIQUE KEY `accession_number_2` (`accession_number`),
  KEY `id_access` (`id_access`),
  KEY `id_pasien` (`id_pasien`),
  KEY `id_kunjungan` (`id_kunjungan`),
  KEY `accession_number` (`accession_number`),
  KEY `id_service_request` (`id_service_request`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_dicom`
--

DROP TABLE IF EXISTS `radiologi_dicom`;
CREATE TABLE IF NOT EXISTS `radiologi_dicom` (
  `id_radiologi_dicom` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_radiologi` int UNSIGNED NOT NULL,
  `accession_number` varchar(255) NOT NULL,
  `data_dicom` json DEFAULT NULL,
  PRIMARY KEY (`id_radiologi_dicom`),
  KEY `id_radiologi` (`id_radiologi`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_dicom_conv`
--

DROP TABLE IF EXISTS `radiologi_dicom_conv`;
CREATE TABLE IF NOT EXISTS `radiologi_dicom_conv` (
  `id_radiologi_dicom_conv` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_radiologi_file` varchar(255) NOT NULL,
  `id_radiologi` int UNSIGNED NOT NULL,
  `id_imaging_study` varchar(255) DEFAULT NULL,
  `accession_number` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `dicom_metadata` json DEFAULT NULL,
  PRIMARY KEY (`id_radiologi_dicom_conv`),
  KEY `id_radiologi_file` (`id_radiologi_file`),
  KEY `id_radiologi` (`id_radiologi`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_expertise`
--

DROP TABLE IF EXISTS `radiologi_expertise`;
CREATE TABLE IF NOT EXISTS `radiologi_expertise` (
  `id_radiologi_expertise` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_radiologi` int UNSIGNED NOT NULL,
  `accession_number` varchar(255) NOT NULL,
  `description` text,
  `timestamp` datetime DEFAULT NULL,
  `finding` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `study_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `attachments` text,
  `viewer_link` text,
  `study_instance_uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cardiac_silhouette` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `aorta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `mediastinum` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `lungs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `trachea` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `diaphragm_and_costophrenic_angles` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `visualized_structures` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `impression` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `recommendation` text,
  `doctor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_radiologi_expertise`),
  KEY `id_radiologi` (`id_radiologi`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_expertise_usg`
--

DROP TABLE IF EXISTS `radiologi_expertise_usg`;
CREATE TABLE IF NOT EXISTS `radiologi_expertise_usg` (
  `id_radiologi_expertise_usg` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_radiologi` int UNSIGNED NOT NULL,
  `accession_number` varchar(255) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timestamp` datetime DEFAULT NULL,
  `finding` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `study_number` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `imaging_study_uuid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `viewer_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `study_instance_uid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `recommendation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `doctor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gestational_sac_size` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `crown_rump_length` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fetal_heart_rate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `biparietal_diameter` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `head_circumference` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `abdominal_circumference` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `femur_length` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `single_deepest_pocket` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `estimated_fetal_weight` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fetal_position` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `estimated_gestational_age` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `estimated_date_birth` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fetal_presentation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id_radiologi_expertise_usg`),
  KEY `id_radiologi` (`id_radiologi`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_file`
--

DROP TABLE IF EXISTS `radiologi_file`;
CREATE TABLE IF NOT EXISTS `radiologi_file` (
  `id_radiologi_file` varchar(255) NOT NULL COMMENT 'UUID',
  `id_radiologi` int UNSIGNED NOT NULL,
  `id_access` int UNSIGNED DEFAULT NULL COMMENT 'user yang upload',
  `folder_name` varchar(255) NOT NULL COMMENT 'YYYYmm (folder Bulanan)',
  `file_datetime` datetime NOT NULL COMMENT 'tanggal waktu upload',
  `file_description` text COMMENT 'catatan tentang file',
  `file_type` varchar(255) NOT NULL COMMENT 'JPG, PNG, GIF',
  `file_size` int NOT NULL COMMENT 'byte',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'rm-reg-random.tipe',
  PRIMARY KEY (`id_radiologi_file`),
  KEY `file_to_radiologi` (`id_radiologi`),
  KEY `file_to_access` (`id_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_invoice`
--

DROP TABLE IF EXISTS `radiologi_invoice`;
CREATE TABLE IF NOT EXISTS `radiologi_invoice` (
  `id_radiologi_invoice` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_radiologi` int UNSIGNED NOT NULL,
  `id_master_service_prices` int UNSIGNED DEFAULT NULL,
  `service_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `total_price` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Harga per tindakan',
  `quantity` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Kuantitas',
  `amount` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Jumlah tagihan',
  PRIMARY KEY (`id_radiologi_invoice`),
  KEY `invoice_to_radiologi` (`id_radiologi`),
  KEY `invoice_to_master_price` (`id_master_service_prices`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiologi_local_exp`
--

DROP TABLE IF EXISTS `radiologi_local_exp`;
CREATE TABLE IF NOT EXISTS `radiologi_local_exp` (
  `id_radiologi_local_exp` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_radiologi` int UNSIGNED NOT NULL,
  `temuan` mediumtext,
  `kesan` mediumtext,
  `saran` mediumtext,
  `catatan` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id_radiologi_local_exp`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_email_gateway`
--

DROP TABLE IF EXISTS `setting_email_gateway`;
CREATE TABLE IF NOT EXISTS `setting_email_gateway` (
  `id_setting_email_gateway` int NOT NULL AUTO_INCREMENT,
  `email_gateway` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `password_gateway` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url_provider` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `port_gateway` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `nama_pengirim` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url_service` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `validasi_email` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `redirect_validasi` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pesan_validasi_email` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_setting_email_gateway`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_token`
--
ALTER TABLE `api_token`
  ADD CONSTRAINT `token_to_account` FOREIGN KEY (`id_api_account`) REFERENCES `api_account` (`id_api_account`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_response`
--
ALTER TABLE `question_response`
  ADD CONSTRAINT `response_to_question` FOREIGN KEY (`id_question`) REFERENCES `question` (`id_question`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `response_to_radiologi` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi`
--
ALTER TABLE `radiologi`
  ADD CONSTRAINT `rad_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_dicom`
--
ALTER TABLE `radiologi_dicom`
  ADD CONSTRAINT `disom_to_rad` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_dicom_conv`
--
ALTER TABLE `radiologi_dicom_conv`
  ADD CONSTRAINT `dicom_to_radiologi` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `file_to_dcm` FOREIGN KEY (`id_radiologi_file`) REFERENCES `radiologi_file` (`id_radiologi_file`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_expertise`
--
ALTER TABLE `radiologi_expertise`
  ADD CONSTRAINT `rad_to_exp` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_expertise_usg`
--
ALTER TABLE `radiologi_expertise_usg`
  ADD CONSTRAINT `usg_to_rad` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_file`
--
ALTER TABLE `radiologi_file`
  ADD CONSTRAINT `file_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `file_to_radiologi` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_invoice`
--
ALTER TABLE `radiologi_invoice`
  ADD CONSTRAINT `invoice_to_master_price` FOREIGN KEY (`id_master_service_prices`) REFERENCES `master_service_prices` (`id_master_service_prices`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_to_radiologi` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
