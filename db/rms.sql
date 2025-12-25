-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 25, 2025 at 07:38 PM
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
('Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA', 'Bantuan', 'Lainnya', 'Halaman untuk mengelola konten bantuan atau dokumentasi', '2025-09-06 14:36:36'),
('DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD', 'Pemeriksaan', 'Master', 'Halaman untuk mengelola pemeriksaan radiologi', '2025-12-19 20:02:10'),
('FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX', 'Kode Pemeriksaan', 'Referensi', 'Halaman untuk mengelola referensi pemeriksaan', '2025-12-21 01:27:29'),
('FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7', 'Kode Klinis', 'Referensi', 'Halaman untuk mengelola kode klinis pasien', '2025-12-20 20:28:14'),
('Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH', 'Pengaturan Umum', 'Pengaturan', 'Halaman yang berfungsi untuk mengatur aplikasi secara umum', '2025-09-01 19:27:07'),
('aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY', 'Email Gateway', 'Pengaturan', 'Halaman yang berguna untuk menyimpan pengaturan email gateway', '2025-09-01 19:32:54'),
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
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
(226, 1, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(227, 1, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(228, 1, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(229, 1, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(230, 1, 'mx0HdJRPFScVla7nCyFTIhAfbdGLYfwDpblB'),
(231, 1, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(232, 1, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(233, 1, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(234, 1, 'DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD'),
(235, 1, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(236, 1, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(237, 1, 'FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7'),
(238, 1, 'FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX'),
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
(251, 8, 'FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX');

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
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
(149, 1, 'jO3M0NopVQeXi4VuDHpvD9SRJzntpUGAe6Sw'),
(150, 1, 'lInyeHHg924zNLaXZ3SmjjnuyCOYBnUyUuTD'),
(151, 1, 'nSYinRWpCF9MHNUIlW7Up5vTip70gNNLlrqv'),
(152, 1, 'nkYXm3U8XWpOt1cD3PNeCwDQzesMYmmUUbee'),
(153, 1, 'mx0HdJRPFScVla7nCyFTIhAfbdGLYfwDpblB'),
(154, 1, '5a7yRbkFPs6fXNHQf8a7bI79IZcbbIaijE0E'),
(155, 1, '36grsDsU11UKOCFPKlh5Gx7K2YbR6XpRHJ5y'),
(156, 1, 'Dnd2UZLzazCqJ9WfuzQKlIOpYueb2fXxNHXA'),
(157, 1, 'DqA0kUSiUGYtR6msgXj0V7Lx2Sh9NkZW1NRD'),
(158, 1, 'aziAs4ZofHmVooUohitYSojDp7oR2zbjrwpY'),
(159, 1, 'Mt24BYzC76RJBEuHdY95bmMKrulttEQzblzH'),
(160, 1, 'FXVReJEjxB2Q564nlvSE0G0m0yJ6iz5ipGQ7'),
(161, 1, 'FSilUhdT6ijSRH2LyzF8y1zBBLXM1W1u5kLX');

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=3794 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `connection_pacs`
--

DROP TABLE IF EXISTS `connection_pacs`;
CREATE TABLE IF NOT EXISTS `connection_pacs` (
  `id_connection_pacs` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ex: Development, Staging, Production',
  `url_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'dari PACS',
  `username_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'dari PACS',
  `password_connection_pacs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'dari PACS',
  `token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'token dari service login',
  `token_expired` datetime DEFAULT NULL COMMENT 'Informasi waktu expired',
  `status_connection_pacs` tinyint(1) NOT NULL COMMENT 'true or false',
  PRIMARY KEY (`id_connection_pacs`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`id_master_pemeriksaan`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_pemeriksaan`
--

INSERT INTO `master_pemeriksaan` (`id_master_pemeriksaan`, `nama_pemeriksaan`, `modalitas`, `pemeriksaan_code`, `pemeriksaan_description`, `pemeriksaan_sys`, `bodysite_code`, `bodysite_description`, `bodysite_sys`) VALUES
(1, 'Foto Thorax PA', 'XR', '30745-4', 'Chest X-ray PA view', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct'),
(2, 'Foto Thorax AP', 'XR', '30746-2', 'Chest X-ray AP view', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct'),
(3, 'Foto Thorax Lateral', 'XR', '30747-0', 'Chest X-ray lateral view', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct'),
(4, 'Foto Abdomen Polos', 'XR', '30738-9', 'Abdomen X-ray', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct'),
(5, 'Foto Pelvis', 'XR', '30739-7', 'Pelvis X-ray', 'http://loinc.org', '12921003', 'Pelvis', 'http://snomed.info/sct'),
(6, 'Foto Cervical AP/Lateral', 'XR', '30751-2', 'Cervical spine X-ray', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct'),
(7, 'Foto Lumbal AP/Lateral', 'XR', '30753-8', 'Lumbar spine X-ray', 'http://loinc.org', '122496007', 'Lumbar spine', 'http://snomed.info/sct'),
(8, 'Foto Femur', 'XR', '30757-9', 'Femur X-ray', 'http://loinc.org', '71341001', 'Femur', 'http://snomed.info/sct'),
(9, 'Foto Cruris', 'XR', '30758-7', 'Lower leg X-ray', 'http://loinc.org', '30021000', 'Lower leg', 'http://snomed.info/sct'),
(10, 'Foto Sinus Paranasal', 'XR', '30760-3', 'Paranasal sinus X-ray', 'http://loinc.org', '66019005', 'Paranasal sinus', 'http://snomed.info/sct'),
(11, 'CT Scan Kepala Non Kontras', 'CT', '24627-2', 'CT Head WO contrast', 'http://loinc.org', '69536005', 'Head', 'http://snomed.info/sct'),
(12, 'CT Scan Kepala Kontras', 'CT', '24628-0', 'CT Head W contrast', 'http://loinc.org', '69536005', 'Head', 'http://snomed.info/sct'),
(13, 'CT Scan Thorax', 'CT', '24604-1', 'CT Chest', 'http://loinc.org', '51185008', 'Thorax', 'http://snomed.info/sct'),
(14, 'CT Scan Abdomen', 'CT', '24605-8', 'CT Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct'),
(15, 'CT Scan Abdomen Kontras', 'CT', '24606-6', 'CT Abdomen W contrast', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct'),
(16, 'CT Scan Pelvis', 'CT', '24607-4', 'CT Pelvis', 'http://loinc.org', '12921003', 'Pelvis', 'http://snomed.info/sct'),
(17, 'CT Scan Sinus', 'CT', '24614-0', 'CT Sinus', 'http://loinc.org', '66019005', 'Paranasal sinus', 'http://snomed.info/sct'),
(18, 'CT Scan Spine Cervical', 'CT', '24615-7', 'CT Cervical spine', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct'),
(19, 'CT Scan Spine Lumbal', 'CT', '24617-3', 'CT Lumbar spine', 'http://loinc.org', '122496007', 'Lumbar spine', 'http://snomed.info/sct'),
(20, 'USG Abdomen', 'US', '30792-6', 'Ultrasound Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct'),
(21, 'USG Hati', 'US', '30793-4', 'Ultrasound Liver', 'http://loinc.org', '10200004', 'Liver', 'http://snomed.info/sct'),
(22, 'USG Ginjal', 'US', '30794-2', 'Ultrasound Kidney', 'http://loinc.org', '64033007', 'Kidney', 'http://snomed.info/sct'),
(23, 'USG Kandung Kemih', 'US', '30795-9', 'Ultrasound Bladder', 'http://loinc.org', '89837001', 'Urinary bladder', 'http://snomed.info/sct'),
(24, 'USG Prostat', 'US', '30796-7', 'Ultrasound Prostate', 'http://loinc.org', '41216001', 'Prostate', 'http://snomed.info/sct'),
(25, 'USG Kehamilan', 'US', '30801-5', 'Obstetric ultrasound', 'http://loinc.org', '12738006', 'Uterus', 'http://snomed.info/sct'),
(26, 'USG Transvaginal', 'US', '30802-3', 'Transvaginal ultrasound', 'http://loinc.org', '12738006', 'Uterus', 'http://snomed.info/sct'),
(27, 'USG Payudara', 'US', '30797-5', 'Ultrasound Breast', 'http://loinc.org', '76752008', 'Breast', 'http://snomed.info/sct'),
(28, 'MRI Brain', 'MR', '24590-2', 'MRI Brain', 'http://loinc.org', '12738006', 'Brain', 'http://snomed.info/sct'),
(29, 'MRI Spine Cervical', 'MR', '24601-7', 'MRI Cervical spine', 'http://loinc.org', '122494005', 'Cervical spine', 'http://snomed.info/sct'),
(30, 'MRI Spine Lumbal', 'MR', '24603-3', 'MRI Lumbar spine', 'http://loinc.org', '122496007', 'Lumbar spine', 'http://snomed.info/sct'),
(31, 'MRI Knee', 'MR', '24594-4', 'MRI Knee', 'http://loinc.org', '72696002', 'Knee', 'http://snomed.info/sct'),
(32, 'MRI Abdomen', 'MR', '24598-5', 'MRI Abdomen', 'http://loinc.org', '818983003', 'Abdomen', 'http://snomed.info/sct'),
(33, 'Bone Scan', 'NM', '25022-8', 'Bone scintigraphy', 'http://loinc.org', '272673000', 'Skeleton', 'http://snomed.info/sct'),
(34, 'Thyroid Scan', 'NM', '25023-6', 'Thyroid scintigraphy', 'http://loinc.org', '69748006', 'Thyroid', 'http://snomed.info/sct'),
(35, 'Renal Scan', 'NM', '25024-4', 'Renal scintigraphy', 'http://loinc.org', '64033007', 'Kidney', 'http://snomed.info/sct');

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
  `kesan` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `attachments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Constraints for table `radiologi_expertise`
--
ALTER TABLE `radiologi_expertise`
  ADD CONSTRAINT `rad_to_exp` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `radiologi_expertise_usg`
--
ALTER TABLE `radiologi_expertise_usg`
  ADD CONSTRAINT `usg_to_rad` FOREIGN KEY (`id_radiologi`) REFERENCES `radiologi` (`id_radiologi`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
