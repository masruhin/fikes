-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2026 at 09:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fikes`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(10) UNSIGNED NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('publish','draft') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` int(10) UNSIGNED NOT NULL,
  `nidn` varchar(30) DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `program_studi` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `nidn`, `nama`, `program_studi`, `jabatan`, `email`, `foto`, `status`, `created_at`) VALUES
(2, 'ds231233', 'adssss', 'Keperawatan', 'sdasd', 'furqonkamal9@gmail.com', 'dosen_20260909101621_5eb8169b.jpg', 'aktif', '2026-09-09 08:16:21'),
(3, '213123', 'aaaa', 'Keperawatan', 'aaaa', 'furqonkamal9@gmail.com', 'dosen_20260909102521_1aaa54ee.jpg', 'aktif', '2026-09-09 08:25:21');

-- --------------------------------------------------------

--
-- Table structure for table `dosen_bidang_ajar`
--

CREATE TABLE `dosen_bidang_ajar` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `nilai` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen_bidang_ajar`
--

INSERT INTO `dosen_bidang_ajar` (`id`, `dosen_id`, `nilai`, `created_at`) VALUES
(3, 2, 'k3', '2026-09-09 08:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `dosen_keilmuan`
--

CREATE TABLE `dosen_keilmuan` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `nilai` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen_keilmuan`
--

INSERT INTO `dosen_keilmuan` (`id`, `dosen_id`, `nilai`, `created_at`) VALUES
(2, 2, 'gadar', '2026-09-09 08:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `dosen_pendidikan`
--

CREATE TABLE `dosen_pendidikan` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `nilai` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen_pendidikan`
--

INSERT INTO `dosen_pendidikan` (`id`, `dosen_id`, `nilai`, `created_at`) VALUES
(3, 2, 'S1', '2026-09-09 08:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `kemahasiswaan`
--

CREATE TABLE `kemahasiswaan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `judul` varchar(180) NOT NULL,
  `isi` text DEFAULT NULL,
  `status` enum('publish','draft') DEFAULT 'publish',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `misi`
--

CREATE TABLE `misi` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomor` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `isi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `misi`
--

INSERT INTO `misi` (`id`, `nomor`, `judul`, `isi`, `created_at`) VALUES
(1, 1, 'Pendidikan', 'Menyelenggarakan pendidikan dan pengajaran di bidang ilmu kesehatan mengacu kepada Kurikulum Kerangka Kualifikasi Nasional Indonesia.', '2026-09-08 07:46:57'),
(2, 2, 'Pengembangan Keilmuan', 'Menyelenggarakan proses pendidikan dan menghasilkan lulusan yang berakhlak mulia, berkemampuan IPTEKs dan berjiwa wirausaha.', '2026-09-08 07:46:57'),
(3, 3, 'Pengabdian Masyarakat', 'Menyelenggarakan dan mengembangkan ilmu pengetahuan dan riset di bidang kesehatan.', '2026-09-08 07:46:57'),
(4, 4, 'Pengembangan Sumber Daya', 'Menyelenggarakan dan mengembangkan pengabdian kepada masyarakat di bidang kesehatan.', '2026-09-08 07:46:57'),
(5, 5, 'Kerja Sama Strategis', 'Membangun dan memperluas kerja sama dengan berbagai pihak untuk mendukung pengembangan pendidikan, penelitian, dan pengabdian kepada masyarakat.', '2026-09-08 07:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_kampus` varchar(180) DEFAULT 'Fakultas Ilmu Kesehatan',
  `email` varchar(150) DEFAULT 'info@fikes.ac.id',
  `telepon` varchar(50) DEFAULT '(021) 1234567',
  `alamat` text DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `maps_embed` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_kampus`, `email`, `telepon`, `alamat`, `instagram`, `facebook`, `youtube`, `maps_embed`) VALUES
(1, 'Fakultas Ilmu Kesehatan', 'info@fikes.ac.id', '(021) 1234567', 'Alamat Fakultas Ilmu Kesehatan, Universitas Bhamada Slawi', NULL, NULL, NULL, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbef42471658d%3A0x883656d1325ef066!2sUniversitas%20Bhamada%20Slawi!5e0!3m2!1sid!2sid!4v1787544396003!5m2!1sid!2sid');

-- --------------------------------------------------------

--
-- Table structure for table `prodi_capaian_pembelajaran`
--

CREATE TABLE `prodi_capaian_pembelajaran` (
  `id` int(11) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `isi` text NOT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi_capaian_pembelajaran`
--

INSERT INTO `prodi_capaian_pembelajaran` (`id`, `prodi_id`, `kategori`, `isi`, `nomor_urut`, `created_at`) VALUES
(1, 1, 'Sikap', 'Mampu menunjukkan sikap profesional dan bertanggung jawab.', 1, '2026-09-10 04:33:52'),
(2, 1, 'Pengetahuan', 'Menguasai konsep dan teori ilmu keperawatan.', 2, '2026-09-10 04:33:52'),
(3, 1, 'Keterampilan Umum', 'Mampu menerapkan komunikasi efektif dalam pelayanan kesehatan.', 3, '2026-09-10 04:33:52'),
(4, 1, 'Keterampilan Khusus', 'Mampu memberikan asuhan keperawatan secara profesional.', 4, '2026-09-10 04:33:52'),
(5, 7, 'ASDASD', 'ASDASDASD', 1, '2026-09-10 06:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `prodi_fasilitas`
--

CREATE TABLE `prodi_fasilitas` (
  `id` int(11) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `nama_fasilitas` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi_fasilitas`
--

INSERT INTO `prodi_fasilitas` (`id`, `prodi_id`, `nama_fasilitas`, `deskripsi`, `gambar`, `nomor_urut`, `created_at`) VALUES
(1, 1, 'Laboratorium Keperawatan', 'Laboratorium untuk praktik mahasiswa keperawatan.', NULL, 1, '2026-09-10 04:34:23'),
(2, 1, 'Laboratorium Komputer', 'Laboratorium komputer untuk mendukung kegiatan pembelajaran.', NULL, 2, '2026-09-10 04:34:23'),
(3, 1, 'Perpustakaan', 'Perpustakaan dengan koleksi buku dan referensi kesehatan.', NULL, 3, '2026-09-10 04:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `prodi_kurikulum`
--

CREATE TABLE `prodi_kurikulum` (
  `id` int(11) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `kode_mk` varchar(30) DEFAULT NULL,
  `nama_mk` varchar(150) NOT NULL,
  `semester` varchar(30) DEFAULT NULL,
  `sks` decimal(4,1) DEFAULT 0.0,
  `jenis` varchar(50) DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi_kurikulum`
--

INSERT INTO `prodi_kurikulum` (`id`, `prodi_id`, `kode_mk`, `nama_mk`, `semester`, `sks`, `jenis`, `nomor_urut`, `created_at`) VALUES
(1, 1, 'KEP101', 'Dasar-Dasar Keperawatan', '1', 3.0, 'Wajib', 1, '2026-09-10 04:34:07'),
(2, 1, 'KEP102', 'Anatomi dan Fisiologi', '1', 4.0, 'Wajib', 2, '2026-09-10 04:34:07'),
(3, 1, 'KEP201', 'Keperawatan Medikal Bedah', '2', 4.0, 'Wajib', 3, '2026-09-10 04:34:07'),
(4, 1, 'KEP301', 'Keperawatan Anak', '3', 3.0, 'Wajib', 4, '2026-09-10 04:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `prodi_misi`
--

CREATE TABLE `prodi_misi` (
  `id` int(11) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `isi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi_misi`
--

INSERT INTO `prodi_misi` (`id`, `prodi_id`, `nomor_urut`, `isi`, `created_at`) VALUES
(1, 1, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(2, 2, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(3, 3, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(4, 4, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(5, 5, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(6, 6, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(8, 1, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(9, 2, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(10, 3, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(11, 4, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(12, 5, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(13, 6, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(15, 1, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(16, 2, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(17, 3, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(18, 4, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(19, 5, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(20, 6, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(22, 7, 1, 'FOYA', '2026-09-10 06:36:31'),
(23, 7, 2, 'VOYA', '2026-09-10 06:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `program_studi`
--

CREATE TABLE `program_studi` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_prodi` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenjang` varchar(50) NOT NULL,
  `gelar` varchar(80) DEFAULT NULL,
  `kaprodi_nama` varchar(150) DEFAULT NULL,
  `kaprodi_nidn` varchar(50) DEFAULT NULL,
  `kaprodi_email` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `akreditasi` varchar(100) DEFAULT NULL,
  `nomor_akreditasi` varchar(150) DEFAULT NULL,
  `tanggal_akreditasi` date DEFAULT NULL,
  `sekretaris_nama` varchar(150) DEFAULT NULL,
  `sekretaris_nidn` varchar(50) DEFAULT NULL,
  `kontak_telepon` varchar(50) DEFAULT NULL,
  `kontak_email` varchar(150) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `durasi_studi` varchar(50) DEFAULT NULL,
  `sks_lulus` int(11) DEFAULT NULL,
  `jumlah_tenaga_kependidikan` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `brosur` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_studi`
--

INSERT INTO `program_studi` (`id`, `kode_prodi`, `nama`, `jenjang`, `gelar`, `kaprodi_nama`, `kaprodi_nidn`, `kaprodi_email`, `deskripsi`, `foto`, `visi`, `akreditasi`, `nomor_akreditasi`, `tanggal_akreditasi`, `sekretaris_nama`, `sekretaris_nidn`, `kontak_telepon`, `kontak_email`, `alamat`, `durasi_studi`, `sks_lulus`, `jumlah_tenaga_kependidikan`, `gambar`, `brosur`, `status`, `created_at`) VALUES
(1, 'S1-NERS', 'Profesi Ners', 'Profesi', 'Ns.', NULL, NULL, NULL, NULL, NULL, 'Menjadi Program Studi Keperawatan yang unggul dalam pendidikan, penelitian, dan pengabdian kepada masyarakat.', 'Baik Sekali', NULL, NULL, 'Nama Sekretaris Prodi', '0123456789', '081234567890', 'keperawatan@fikes.ac.id', 'Fakultas Ilmu Kesehatan', '4 Tahun', 144, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(2, 'S1-KEP', 'Ilmu Keperawatan', 'Sarjana', 'S.Kep', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(3, 'D3-FAR', 'Farmasi', 'Sarjana', 'S.Farm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(4, 'D3-KEP', 'Keperawatan', 'Diploma', 'A.Md.Kep.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(5, 'D3-KEB', 'Kebidanan', 'Diploma', 'A.Md.Keb.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(6, 'D3-K3', 'Keselamatan dan Kesehatan Kerja', 'Diploma', 'S.Tr.KKK.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(7, 'K5SD', 'FEB', 'S1', 'SARJANA', NULL, NULL, NULL, 'DASDASD DFACDAS', NULL, '1. ACSDDDD\r\n2.SDSADASD', 'B', '3434JKKJK343', '2026-09-10', 'SDASD', 'SDSD', '33333333', 'furqonkamal9@gmail.com', 'SADADASDAS', '4', 20, 0, NULL, NULL, 'aktif', '2026-09-10 06:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `sertifikat_akreditasi`
--

CREATE TABLE `sertifikat_akreditasi` (
  `id_sertifikat` int(10) UNSIGNED NOT NULL,
  `id_prodi` varchar(100) NOT NULL,
  `id_institusi` varchar(100) NOT NULL,
  `id_lembaga` varchar(100) NOT NULL,
  `nomor_sk` varchar(255) NOT NULL,
  `peringkat` varchar(100) NOT NULL,
  `tanggal_sk` date NOT NULL,
  `tanggal_kadaluarsa` date NOT NULL,
  `file_sertifikat` varchar(255) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sertifikat_akreditasi`
--

INSERT INTO `sertifikat_akreditasi` (`id_sertifikat`, `id_prodi`, `id_institusi`, `id_lembaga`, `nomor_sk`, `peringkat`, `tanggal_sk`, `tanggal_kadaluarsa`, `file_sertifikat`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Keperawatan', 'Universitas Bhamada Slawi', 'LAM-PTKes', 'Contoh/0001/AKR/2024', 'Baik Sekali', '2024-05-01', '2029-05-01', 'sertifikat_20260909050852_8fc13869.docx', 1, '2026-09-09 02:42:09', '2026-09-09 03:08:52'),
(2, 'oooo', 'univ bhamada', 'lam', '232/asda1sda', 'baik', '2026-09-09', '2026-09-30', 'sertifikat_20260909050050_cb480201.xls', 1, '2026-09-09 03:00:50', '2026-09-09 03:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `struktur_organisasi`
--

CREATE TABLE `struktur_organisasi` (
  `id` int(11) NOT NULL,
  `periode` varchar(50) NOT NULL,
  `sk_rektor` varchar(200) NOT NULL,
  `dekan` varchar(200) NOT NULL,
  `wakil_dekan_akademik` varchar(200) NOT NULL,
  `wakil_dekan_adum_keu` varchar(200) NOT NULL,
  `wakil_dekan_kemahasiswaan` varchar(200) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `struktur_organisasi`
--

INSERT INTO `struktur_organisasi` (`id`, `periode`, `sk_rektor`, `dekan`, `wakil_dekan_akademik`, `wakil_dekan_adum_keu`, `wakil_dekan_kemahasiswaan`, `gambar`, `created_at`, `updated_at`) VALUES
(1, '2024 - 2026', 'Nomor 030/Univ.BHAMADA/KEP/V/2024', 'Rosmalia, S.T.,M.Kes.', 'Siswati, S.Si.T.,Bdn.,M.Kes.', 'Sri Hidayati, Ns.,M.Kep.,Sp.Kep.MB.', 'Deni Irawan, Ns.,M.Kep.', 'struktur_20260908111729_e58cb6b9.png', '2026-09-08 07:07:10', '2026-09-08 09:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator FIKES', 'admin', '0192023a7bbd73250516f069df18b500', 'admin', '2026-09-08 05:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `visi_misi`
--

CREATE TABLE `visi_misi` (
  `id` int(10) UNSIGNED NOT NULL,
  `visi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visi_misi`
--

INSERT INTO `visi_misi` (`id`, `visi`, `created_at`, `updated_at`) VALUES
(1, 'Menjadi institusi pendidikan tinggi kesehatan yang unggul, profesional, inovatif, berintegritas, dan mampu memberikan kontribusi nyata bagi peningkatan derajat kesehatan masyarakat.sdsdsd', '2026-09-08 05:17:15', '2026-09-08 07:46:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dosen_bidang_ajar`
--
ALTER TABLE `dosen_bidang_ajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indexes for table `dosen_keilmuan`
--
ALTER TABLE `dosen_keilmuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indexes for table `dosen_pendidikan`
--
ALTER TABLE `dosen_pendidikan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indexes for table `kemahasiswaan`
--
ALTER TABLE `kemahasiswaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `misi`
--
ALTER TABLE `misi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prodi_capaian_pembelajaran`
--
ALTER TABLE `prodi_capaian_pembelajaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prodi_cpl` (`prodi_id`);

--
-- Indexes for table `prodi_fasilitas`
--
ALTER TABLE `prodi_fasilitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prodi_fasilitas` (`prodi_id`);

--
-- Indexes for table `prodi_kurikulum`
--
ALTER TABLE `prodi_kurikulum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prodi_kurikulum` (`prodi_id`);

--
-- Indexes for table `prodi_misi`
--
ALTER TABLE `prodi_misi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prodi_misi` (`prodi_id`);

--
-- Indexes for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_kode_prodi` (`kode_prodi`);

--
-- Indexes for table `sertifikat_akreditasi`
--
ALTER TABLE `sertifikat_akreditasi`
  ADD PRIMARY KEY (`id_sertifikat`);

--
-- Indexes for table `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `visi_misi`
--
ALTER TABLE `visi_misi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dosen_bidang_ajar`
--
ALTER TABLE `dosen_bidang_ajar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dosen_keilmuan`
--
ALTER TABLE `dosen_keilmuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dosen_pendidikan`
--
ALTER TABLE `dosen_pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kemahasiswaan`
--
ALTER TABLE `kemahasiswaan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `misi`
--
ALTER TABLE `misi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prodi_capaian_pembelajaran`
--
ALTER TABLE `prodi_capaian_pembelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prodi_fasilitas`
--
ALTER TABLE `prodi_fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prodi_kurikulum`
--
ALTER TABLE `prodi_kurikulum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prodi_misi`
--
ALTER TABLE `prodi_misi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `program_studi`
--
ALTER TABLE `program_studi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sertifikat_akreditasi`
--
ALTER TABLE `sertifikat_akreditasi`
  MODIFY `id_sertifikat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visi_misi`
--
ALTER TABLE `visi_misi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
