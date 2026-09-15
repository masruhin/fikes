-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2026 at 10:49 AM
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
-- Table structure for table `akademik_dokumen`
--

CREATE TABLE `akademik_dokumen` (
  `id` int(11) NOT NULL,
  `kategori` enum('panduan','formulir','kelulusan') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `isi` longtext DEFAULT NULL,
  `file_dokumen` varchar(255) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `akademik_jadwal`
--

CREATE TABLE `akademik_jadwal` (
  `id` int(11) NOT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `jenis` enum('Kuliah','UTS','UAS') NOT NULL DEFAULT 'Kuliah',
  `kode_mk` varchar(30) DEFAULT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `akademik_kalender`
--

CREATE TABLE `akademik_kalender` (
  `id` int(11) NOT NULL,
  `tahun_ajaran` varchar(30) NOT NULL,
  `kategori` varchar(60) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akademik_kalender`
--

INSERT INTO `akademik_kalender` (`id`, `tahun_ajaran`, `kategori`, `judul`, `tanggal_mulai`, `tanggal_selesai`, `keterangan`, `nomor_urut`, `status`, `created_at`) VALUES
(1, '2026/2027', 'Perkuliahan', 'Awal Perkuliahan Semester Ganjil', '2026-09-01', '2026-09-01', 'Contoh data awal; silakan sesuaikan dengan kalender resmi FIKES..', 1, 'aktif', '2026-09-14 06:30:01'),
(2, '2026/2027', 'Ujian', 'Ujian Tengah Semester (UTS)', '2026-11-02', '2026-11-07', 'Contoh data awal.', 2, 'aktif', '2026-09-14 06:30:01'),
(3, '2026/2027', 'Ujian', 'Ujian Akhir Semester (UAS)', '2027-01-04', '2027-01-16', 'Contoh data awal.', 3, 'aktif', '2026-09-14 06:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `akademik_penilaian`
--

CREATE TABLE `akademik_penilaian` (
  `id` int(11) NOT NULL,
  `komponen` varchar(150) NOT NULL,
  `bobot` decimal(5,2) NOT NULL DEFAULT 0.00,
  `keterangan` varchar(255) DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akademik_penilaian`
--

INSERT INTO `akademik_penilaian` (`id`, `komponen`, `bobot`, `keterangan`, `nomor_urut`, `status`, `created_at`) VALUES
(1, 'Tugas / Proyek', 20.00, 'Contoh komponen penilaian.', 2, 'aktif', '2026-09-14 06:30:01'),
(2, 'UTS', 30.00, 'Contoh komponen penilaian.', 2, 'aktif', '2026-09-14 06:30:01'),
(3, 'UAS', 40.00, 'Contoh komponen penilaian.', 3, 'aktif', '2026-09-14 06:30:01'),
(5, 'kehadiran', 10.00, 'Contoh komponen penilaian.', 4, 'aktif', '2026-09-14 06:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `akademik_registrasi`
--

CREATE TABLE `akademik_registrasi` (
  `id` int(11) NOT NULL,
  `tahun_ajaran` varchar(30) NOT NULL,
  `jenis` enum('UKT/SPP','KRS','Persetujuan KRS','Registrasi') NOT NULL DEFAULT 'Registrasi',
  `judul` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akademik_registrasi`
--

INSERT INTO `akademik_registrasi` (`id`, `tahun_ajaran`, `jenis`, `judul`, `tanggal_mulai`, `tanggal_selesai`, `keterangan`, `status`, `created_at`) VALUES
(1, '2026/2027', 'UKT/SPP', 'Pembayaran UKT/SPP Semester Ganjil', '2026-08-10', '2026-08-28', 'ASDASD', 'aktif', '2026-09-14 06:30:01'),
(2, '2026/2027', 'UKT/SPP', 'Pengisian KRS', '2026-08-24', '2026-09-05', 'SDFSFSDFSDFDSF', 'aktif', '2026-09-14 06:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `akademik_silabus`
--

CREATE TABLE `akademik_silabus` (
  `id` int(11) NOT NULL,
  `kurikulum_id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_dokumen` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akademik_silabus`
--

INSERT INTO `akademik_silabus` (`id`, `kurikulum_id`, `judul`, `deskripsi`, `file_dokumen`, `status`, `created_at`) VALUES
(1, 29, 'Awal Perkuliahan Semester Ganjilllll', 'admin', '20260915042602_1a43e24a.docx', 'aktif', '2026-09-15 02:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL DEFAULT 'Berita',
  `ringkasan` text DEFAULT NULL,
  `isi` longtext NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `penulis` varchar(150) DEFAULT NULL,
  `tanggal_terbit` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('draft','terbit') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `slug`, `kategori`, `ringkasan`, `isi`, `gambar`, `penulis`, `tanggal_terbit`, `status`, `created_at`, `updated_at`) VALUES
(1, 'asdasdd', 'asdasdd', 'Berita', 'asdasdasd', 'asdasdasd', '20260914034106_7a9ea462.jpeg', 'Admin FIKES', '2026-09-12 16:06:00', 'terbit', '2026-09-12 09:06:42', '2026-09-14 01:41:06'),
(2, 'oke', 'oke', 'Berita', 'asdakjfadfjkbaas\r\nasdajsdajdn', 'asdasdhlnkajsld<div>askdjabskjdbha sd</div><div>asbd asbd</div><div>lasndlasd</div>', '20260914042413_9c52ca0e.jpeg', 'Admin FIKES', '2026-09-14 09:23:00', 'terbit', '2026-09-14 02:24:13', '2026-09-14 02:24:23');

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
(1, 'Fakultas Ilmu Kesehatan', 'fikes.bhama@gmail.ac.id', '(021) 1234567', 'Alamat Fakultas Ilmu Kesehatan, Universitas Bhamada Slawi', '', '', '', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbef42471658d%3A0x883656d1325ef066!2sUniversitas%20Bhamada%20Slawi!5e0!3m2!1sid!2sid!4v1787544396003!5m2!1sid!2sid');

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
(26, 1, 'Sikap', 'Mampu menunjukkan sikap profesional dan bertanggung jawab.', 1, '2026-09-14 05:58:54'),
(27, 1, 'Pengetahuan', 'Menguasai konsep dan teori ilmu keperawatan.', 2, '2026-09-14 05:58:54'),
(28, 1, 'Keterampilan Umum', 'Mampu menerapkan komunikasi efektif dalam pelayanan kesehatan.', 3, '2026-09-14 05:58:54'),
(29, 1, 'Keterampilan Khusus', 'Mampu memberikan asuhan keperawatan secara profesional.', 4, '2026-09-14 05:58:54');

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
(19, 1, 'Laboratorium Keperawatan', 'Laboratorium untuk praktik mahasiswa keperawatan.', NULL, 1, '2026-09-14 05:58:54'),
(20, 1, 'Laboratorium Komputer', 'Laboratorium komputer untuk mendukung kegiatan pembelajaran.', NULL, 2, '2026-09-14 05:58:54'),
(21, 1, 'Perpustakaan', 'Perpustakaan dengan koleksi buku dan referensi kesehatan.', NULL, 3, '2026-09-14 05:58:54');

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
(25, 1, 'KEP101', 'Dasar-Dasar Keperawatan', '1', 3.0, NULL, 1, '2026-09-14 05:58:54'),
(26, 1, 'KEP102', 'Anatomi dan Fisiologi', '1', 4.0, NULL, 2, '2026-09-14 05:58:54'),
(27, 1, 'KEP201', 'Keperawatan Medikal Bedah', '2', 4.0, NULL, 3, '2026-09-14 05:58:54'),
(28, 1, 'KEP301', 'Keperawatan Anak', '3', 3.0, NULL, 4, '2026-09-14 05:58:54'),
(29, 6, 'K3', 'K3', '4', 2.0, 'Wajib', 5, '2026-09-15 02:25:18');

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
(2, 2, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(3, 3, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(4, 4, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(5, 5, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(6, 6, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-10 04:27:29'),
(9, 2, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(10, 3, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(11, 4, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(12, 5, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(13, 6, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-10 04:27:29'),
(16, 2, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(17, 3, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(18, 4, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(19, 5, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(20, 6, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-10 04:27:29'),
(41, 1, 1, 'Menyelenggarakan pendidikan yang berkualitas dan relevan dengan kebutuhan masyarakat.', '2026-09-14 05:58:54'),
(42, 1, 2, 'Melaksanakan penelitian dan pengabdian kepada masyarakat sesuai bidang keilmuan program studi.', '2026-09-14 05:58:54'),
(43, 1, 3, 'Menghasilkan lulusan yang kompeten, profesional, beretika, adaptif, dan mampu berkolaborasi.', '2026-09-14 05:58:54');

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
(1, 'S1-NERS', 'Profesi Ners', 'Profesi', 'Ns.', 'Khodijah, M.Kep', '2222222222', '', '', '', 'Menjadi Program Studi Keperawatan yang unggul dalam pendidikan, penelitian, dan pengabdian kepada masyarakat.', 'Baik Sekali', '', NULL, 'Susi Muryani, MNS', '01234567891111', '081234567890', 'keperawatan@fikes.ac.id', 'Fakultas Ilmu Kesehatan', '4 Tahun', 144, 0, NULL, '', 'aktif', '2026-09-08 05:17:15'),
(2, 'S1-KEP', 'Ilmu Keperawatan', 'Sarjana', 'S.Kep', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(3, 'D3-FAR', 'Farmasi', 'Sarjana', 'S.Farm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(4, 'D3-KEP', 'Keperawatan', 'Diploma', 'A.Md.Kep.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(5, 'D3-KEB', 'Kebidanan', 'Diploma', 'A.Md.Keb.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15'),
(6, 'D3-K3', 'Keselamatan dan Kesehatan Kerja', 'Diploma', 'S.Tr.KKK.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'aktif', '2026-09-08 05:17:15');

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
(3, '2', 'univ bhamada', 'LAM-PTKes', 'Contoh/0001/AKR/2024', 'Baik Sekali', '2026-09-14', '2026-09-30', '20260914_052216_d38e465b.docx', 1, '2026-09-14 03:21:33', '2026-09-14 03:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `slider_beranda`
--

CREATE TABLE `slider_beranda` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `highlight` varchar(255) DEFAULT NULL,
  `label` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `link_utama` varchar(255) DEFAULT NULL,
  `teks_tombol_utama` varchar(100) DEFAULT NULL,
  `link_kedua` varchar(255) DEFAULT NULL,
  `teks_tombol_kedua` varchar(100) DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slider_beranda`
--

INSERT INTO `slider_beranda` (`id`, `judul`, `highlight`, `label`, `deskripsi`, `gambar`, `link_utama`, `teks_tombol_utama`, `link_kedua`, `teks_tombol_kedua`, `nomor_urut`, `status`, `created_at`) VALUES
(1, 'Membangun Generasi', 'Tenaga Kesehatan Profesional', 'FAKULTAS ILMU KESEHATAN', 'Mewujudkan pendidikan kesehatan yang unggul, profesional, inovatif, dan berintegritas untuk masa depan yang lebih baik.', '20260914033930_14b336cb.jpeg', 'page/program-studi/program-studi.php', 'Lihat Program Studi', '#', 'Pendaftaran', 4, 'aktif', '2026-09-12 07:40:43'),
(2, 'Pendidikan Kesehatan', 'Untuk Masa Depan', 'PENDIDIKAN BERKUALITAS', 'Mengembangkan kompetensi mahasiswa melalui pembelajaran berkualitas, teknologi, penelitian, dan pengalaman praktik.', '20260914034004_1db88d50.jpeg', 'page/program-studi/program-studi.php', 'Lihat Program Studi', '#', 'Pendaftaran', 3, 'aktif', '2026-09-12 07:40:43'),
(4, 'Membangun Generasii', 'Tenaga Kesehatan Profesional', 'FAKULTAS ILMU KESEHATAN', 'Mewujudkan pendidikan kesehatan yang unggul, profesional, inovatif, dan berintegritas untuk masa depan yang lebih baik.', '20260914033939_865a2e52.jpeg', 'page/program-studi/program-studi.php', 'Lihat Program Studi', '#', 'Pendaftaran', 1, 'aktif', '2026-09-12 07:48:41'),
(5, 'Pendidikan Kesehatan', 'Untuk Masa Depan', 'PENDIDIKAN BERKUALITAS', 'Mengembangkan kompetensi mahasiswa melalui pembelajaran berkualitas, teknologi, penelitian, dan pengalaman praktik.', '20260914033949_f75787cb.jpeg', 'page/program-studi/program-studi.php', 'Lihat Program Studi', '#', 'Pendaftaran', 2, 'aktif', '2026-09-12 07:48:41');

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
-- Table structure for table `survey`
--

CREATE TABLE `survey` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `target_responden` varchar(100) NOT NULL DEFAULT 'Umum',
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif',
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey`
--

INSERT INTO `survey` (`id`, `judul`, `slug`, `deskripsi`, `target_responden`, `tanggal_mulai`, `tanggal_selesai`, `status`, `nomor_urut`, `created_at`, `updated_at`) VALUES
(1, 'Penilaian Pelayanan FIKES', 'penilaian-pelayanan-fikes', 'Survey kepuasan terhadap pelayanan Fakultas Ilmu Kesehatan.', 'Mahasiswa', '2026-09-15', NULL, 'aktif', 1, '2026-09-15 07:38:21', '2026-09-15 07:38:21'),
(2, 'Penilaian Pelayanan Sarpras Mahasiswa', 'penilaian-pelayanan-sarpras-mahasiswa', 'Survey penilaian pelayanan sarana dan prasarana untuk mahasiswa/mahasiswi.', 'Mahasiswa', '2026-09-15', NULL, 'aktif', 2, '2026-09-15 07:38:21', '2026-09-15 07:38:21'),
(3, 'Penilaian Pelayanan Sarpras Karyawan', 'penilaian-pelayanan-sarpras-karyawan', 'Survey penilaian pelayanan sarana dan prasarana untuk karyawan/pegawai.', 'Karyawan/Pegawai', '2026-09-15', NULL, 'aktif', 3, '2026-09-15 07:38:21', '2026-09-15 07:38:21'),
(4, 'PELAYANAN FEB', 'survey-1789458006', '', 'Masyarakat', '2026-09-15', '2027-09-30', 'aktif', 1, '2026-09-15 07:40:06', '2026-09-15 07:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `survey_jawaban`
--

CREATE TABLE `survey_jawaban` (
  `id` bigint(20) NOT NULL,
  `responden_id` bigint(20) NOT NULL,
  `pertanyaan_id` int(11) NOT NULL,
  `pilihan_id` int(11) DEFAULT NULL,
  `jawaban_text` text DEFAULT NULL,
  `nilai` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_jawaban`
--

INSERT INTO `survey_jawaban` (`id`, `responden_id`, `pertanyaan_id`, `pilihan_id`, `jawaban_text`, `nilai`) VALUES
(1, 1, 1, 3, NULL, 3.00),
(2, 1, 2, 8, NULL, 3.00),
(3, 2, 1, 2, NULL, 2.00),
(4, 2, 2, 7, NULL, 2.00),
(5, 3, 1, 5, NULL, 5.00),
(6, 3, 2, 10, NULL, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `survey_pertanyaan`
--

CREATE TABLE `survey_pertanyaan` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `tipe` enum('skala','pilihan_ganda','ya_tidak','isian','textarea') NOT NULL DEFAULT 'skala',
  `wajib` tinyint(1) NOT NULL DEFAULT 1,
  `nomor_urut` int(11) NOT NULL DEFAULT 1,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_pertanyaan`
--

INSERT INTO `survey_pertanyaan` (`id`, `survey_id`, `pertanyaan`, `tipe`, `wajib`, `nomor_urut`, `status`, `created_at`) VALUES
(1, 4, 'APAKAH PUAS?', 'skala', 1, 1, 'aktif', '2026-09-15 07:40:27'),
(2, 4, 'apakah fasilitas mendukung?', 'skala', 1, 1, 'aktif', '2026-09-15 08:04:34'),
(3, 4, 'berikan komentar', 'isian', 1, 1, 'aktif', '2026-09-15 08:40:46');

-- --------------------------------------------------------

--
-- Table structure for table `survey_pilihan`
--

CREATE TABLE `survey_pilihan` (
  `id` int(11) NOT NULL,
  `pertanyaan_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `nilai` decimal(8,2) DEFAULT NULL,
  `nomor_urut` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_pilihan`
--

INSERT INTO `survey_pilihan` (`id`, `pertanyaan_id`, `label`, `nilai`, `nomor_urut`) VALUES
(1, 1, 'Sangat Tidak Puas', 1.00, 1),
(2, 1, 'Tidak Puas', 2.00, 2),
(3, 1, 'Cukup', 3.00, 3),
(4, 1, 'Puas', 4.00, 4),
(5, 1, 'Sangat Puas', 5.00, 5),
(6, 2, 'Sangat Tidak Puas', 1.00, 1),
(7, 2, 'Tidak Puas', 2.00, 2),
(8, 2, 'Cukup', 3.00, 3),
(9, 2, 'Puas', 4.00, 4),
(10, 2, 'Sangat Puas', 5.00, 5);

-- --------------------------------------------------------

--
-- Table structure for table `survey_responden`
--

CREATE TABLE `survey_responden` (
  `id` bigint(20) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `kategori_responden` varchar(100) DEFAULT NULL,
  `identitas` varchar(150) DEFAULT NULL,
  `tanggal_isi` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_responden`
--

INSERT INTO `survey_responden` (`id`, `survey_id`, `nama`, `email`, `kategori_responden`, `identitas`, `tanggal_isi`, `ip_address`) VALUES
(1, 4, 'Kamal Furqon', 'furqonkamal9@gmail.com', 'Mahasiswa', '554541351351', '2026-09-15 15:05:36', '::1'),
(2, 4, 'Kamal', 'furqonkal9@gmail.com', 'Karyawan/Pegawai', '', '2026-09-15 15:12:17', '::1'),
(3, 4, 'masruhin', 'mas@gmail.com', 'Masyarakat', '3328545151', '2026-09-15 15:38:44', '::1');

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
-- Indexes for table `akademik_dokumen`
--
ALTER TABLE `akademik_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dokumen` (`kategori`,`nomor_urut`);

--
-- Indexes for table `akademik_jadwal`
--
ALTER TABLE `akademik_jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jadwal` (`tanggal`,`jam_mulai`),
  ADD KEY `idx_jadwal_prodi` (`prodi_id`);

--
-- Indexes for table `akademik_kalender`
--
ALTER TABLE `akademik_kalender`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kalender` (`tahun_ajaran`,`tanggal_mulai`);

--
-- Indexes for table `akademik_penilaian`
--
ALTER TABLE `akademik_penilaian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `akademik_registrasi`
--
ALTER TABLE `akademik_registrasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_registrasi` (`tahun_ajaran`,`tanggal_mulai`);

--
-- Indexes for table `akademik_silabus`
--
ALTER TABLE `akademik_silabus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_silabus_kurikulum` (`kurikulum_id`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_berita_status_tanggal` (`status`,`tanggal_terbit`);

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
-- Indexes for table `slider_beranda`
--
ALTER TABLE `slider_beranda`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_survey_status` (`status`),
  ADD KEY `idx_survey_urut` (`nomor_urut`);

--
-- Indexes for table `survey_jawaban`
--
ALTER TABLE `survey_jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sj_responden` (`responden_id`),
  ADD KEY `idx_sj_pertanyaan` (`pertanyaan_id`);

--
-- Indexes for table `survey_pertanyaan`
--
ALTER TABLE `survey_pertanyaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sp_survey` (`survey_id`),
  ADD KEY `idx_sp_urut` (`survey_id`,`nomor_urut`);

--
-- Indexes for table `survey_pilihan`
--
ALTER TABLE `survey_pilihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_spl_pertanyaan` (`pertanyaan_id`),
  ADD KEY `idx_spl_urut` (`pertanyaan_id`,`nomor_urut`);

--
-- Indexes for table `survey_responden`
--
ALTER TABLE `survey_responden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sr_survey` (`survey_id`),
  ADD KEY `idx_sr_tanggal` (`survey_id`,`tanggal_isi`);

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
-- AUTO_INCREMENT for table `akademik_dokumen`
--
ALTER TABLE `akademik_dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `akademik_jadwal`
--
ALTER TABLE `akademik_jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `akademik_kalender`
--
ALTER TABLE `akademik_kalender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `akademik_penilaian`
--
ALTER TABLE `akademik_penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `akademik_registrasi`
--
ALTER TABLE `akademik_registrasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `akademik_silabus`
--
ALTER TABLE `akademik_silabus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `prodi_fasilitas`
--
ALTER TABLE `prodi_fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `prodi_kurikulum`
--
ALTER TABLE `prodi_kurikulum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `prodi_misi`
--
ALTER TABLE `prodi_misi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `program_studi`
--
ALTER TABLE `program_studi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sertifikat_akreditasi`
--
ALTER TABLE `sertifikat_akreditasi`
  MODIFY `id_sertifikat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `slider_beranda`
--
ALTER TABLE `slider_beranda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `survey`
--
ALTER TABLE `survey`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `survey_jawaban`
--
ALTER TABLE `survey_jawaban`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `survey_pertanyaan`
--
ALTER TABLE `survey_pertanyaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey_pilihan`
--
ALTER TABLE `survey_pilihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `survey_responden`
--
ALTER TABLE `survey_responden`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
