CREATE DATABASE IF NOT EXISTS fikes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fikes;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','editor') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (nama, username, password, role)
VALUES ('Administrator FIKES', 'admin', MD5('admin123'), 'admin');

CREATE TABLE visi_misi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO visi_misi (visi) VALUES
('Menjadi institusi pendidikan tinggi kesehatan yang unggul, profesional, inovatif, berintegritas, dan mampu memberikan kontribusi nyata bagi peningkatan derajat kesehatan masyarakat.');

CREATE TABLE misi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO misi (nomor, judul, isi) VALUES
(1,'Pendidikan','Menyelenggarakan pendidikan dan pengajaran di bidang ilmu kesehatan mengacu kepada Kurikulum Kerangka Kualifikasi Nasional Indonesia.'),
(2,'Pengembangan Keilmuan','Menyelenggarakan proses pendidikan dan menghasilkan lulusan yang berakhlak mulia, berkemampuan IPTEKs dan berjiwa wirausaha.'),
(3,'Pengabdian Masyarakat','Menyelenggarakan dan mengembangkan ilmu pengetahuan dan riset di bidang kesehatan.'),
(4,'Pengembangan Sumber Daya','Menyelenggarakan dan mengembangkan pengabdian kepada masyarakat di bidang kesehatan.'),
(5,'Kerja Sama Strategis','Membangun dan memperluas kerja sama dengan berbagai pihak untuk mendukung pengembangan pendidikan, penelitian, dan pengabdian kepada masyarakat.');

CREATE TABLE dosen (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(30),
    nama VARCHAR(150) NOT NULL,
    program_studi VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100),
    email VARCHAR(150),
    foto VARCHAR(255),
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE program_studi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    jenjang VARCHAR(50) NOT NULL,
    gelar VARCHAR(80),
    deskripsi TEXT,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO program_studi (nama,jenjang,gelar) VALUES
('Profesi Ners','Profesi','Ns.'),
('Ilmu Keperawatan','Sarjana','S.Kep'),
('Farmasi','Sarjana','S.Farm'),
('Keperawatan','Diploma','A.Md.Kep.'),
('Kebidanan','Diploma','A.Md.Keb.'),
('Keselamatan dan Kesehatan Kerja','Diploma','S.Tr.KKK.');

CREATE TABLE kemahasiswaan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR(100) NOT NULL,
    judul VARCHAR(180) NOT NULL,
    isi TEXT,
    status ENUM('publish','draft') DEFAULT 'publish',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE berita (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT,
    gambar VARCHAR(255),
    status ENUM('publish','draft') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pengaturan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kampus VARCHAR(180) DEFAULT 'Fakultas Ilmu Kesehatan',
    email VARCHAR(150) DEFAULT 'info@fikes.ac.id',
    telepon VARCHAR(50) DEFAULT '(021) 1234567',
    alamat TEXT,
    instagram VARCHAR(255),
    facebook VARCHAR(255),
    youtube VARCHAR(255),
    maps_embed TEXT
);

INSERT INTO pengaturan (alamat, maps_embed) VALUES
('Alamat Fakultas Ilmu Kesehatan, Universitas Bhamada Slawi', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbef42471658d%3A0x883656d1325ef066!2sUniversitas%20Bhamada%20Slawi!5e0!3m2!1sid!2sid!4v1787544396003!5m2!1sid!2sid');
