<?php
require_once __DIR__ . '/../../admin/config/database.php';
function e($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
function tanggal_id($v)
{
  if (!$v) return '-';
  $b = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  $t = strtotime($v);
  return $t ? date('d', $t) . ' ' . $b[(int)date('m', $t) - 1] . ' ' . date('Y', $t) : $v;
}
$prodiList = $pdo->query("SELECT id,nama,jenjang,gelar FROM program_studi WHERE status='aktif' ORDER BY nama ASC")->fetchAll();
$kurikulum = $pdo->query("SELECT k.id,k.prodi_id,k.kode_mk,k.nama_mk,k.semester,k.sks,k.jenis,p.nama prodi_nama, (SELECT s.file_dokumen FROM akademik_silabus s WHERE s.kurikulum_id=k.id AND s.status='aktif' ORDER BY s.id DESC LIMIT 1) silabus_file FROM prodi_kurikulum k LEFT JOIN program_studi p ON p.id=k.prodi_id ORDER BY p.nama,k.semester,k.nomor_urut,k.id")->fetchAll();
$totalSks = 0;
foreach ($kurikulum as $k) $totalSks += (float)$k['sks'];
$kalender = $pdo->query("SELECT * FROM akademik_kalender WHERE status='aktif' ORDER BY tanggal_mulai ASC,id ASC")->fetchAll();
$jadwal = $pdo->query("SELECT j.*,p.nama prodi_nama FROM akademik_jadwal j LEFT JOIN program_studi p ON p.id=j.prodi_id WHERE j.status='aktif' ORDER BY j.tanggal ASC,j.jam_mulai ASC,j.id ASC")->fetchAll();
$registrasi = $pdo->query("SELECT * FROM akademik_registrasi WHERE status='aktif' ORDER BY tanggal_mulai ASC,id ASC")->fetchAll();
$dokumen = $pdo->query("SELECT * FROM akademik_dokumen WHERE status='aktif' ORDER BY kategori,nomor_urut,id DESC")->fetchAll();
$penilaian = $pdo->query("SELECT * FROM akademik_penilaian WHERE status='aktif' ORDER BY nomor_urut,id ASC")->fetchAll();
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Akademik | FIKES - Fakultas Ilmu Kesehatan</title>

    <meta name="description"
      content="Website resmi Fakultas Ilmu Kesehatan - Informasi akademik, program studi, kemahasiswaan, pelayanan dan informasi FIKES." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap");

    :root {
      --primary: #087f5b;
      --primary-dark: #056044;
      --primary-light: #e7f7f1;
      --secondary: #f4b942;
      --dark: #12372a;
      --text: #52635d;
      --light: #f7faf9;
      --white: #fff;
      --border: #e5ece9;
      --shadow: 0 20px 60px rgba(18, 55, 42, .1);
      --radius: 18px;
      --transition: .3s ease
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    html {
      scroll-behavior: smooth
    }

    body {
      font-family: Inter, sans-serif;
      color: var(--text);
      background: #fff;
      line-height: 1.7;
      overflow-x: hidden
    }

    h1,
    h2,
    h3,
    h4 {
      font-family: "Plus Jakarta Sans", sans-serif;
      color: var(--dark);
      line-height: 1.3
    }

    a {
      text-decoration: none;
      color: inherit
    }

    .container {
      width: min(1180px, calc(100% - 40px));
      margin: auto
    }

    .section {
      padding: 90px 0
    }

    .topbar {
      background: var(--dark);
      color: #d9e8e2;
      font-size: 13px
    }

    .topbar-inner {
      min-height: 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px
    }

    .topbar-info,
    .topbar-social {
      display: flex;
      gap: 22px;
      align-items: center
    }

    .navbar {
      position: sticky;
      top: 0;
      z-index: 999;
      background: rgba(255, 255, 255, .95);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid var(--border)
    }

    .nav-inner {
      min-height: 82px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 25px
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px
    }

    .logo-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), #13a878);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 17px
    }

    .logo-text strong {
      display: block;
      color: var(--dark);
      font-size: 17px
    }

    .logo-text small {
      display: block;
      font-size: 10px;
      color: var(--primary);
      font-weight: 700
    }

    .nav-menu {
      display: flex;
      gap: 3px
    }

    .nav-link {
      min-height: 82px;
      padding: 0 13px;
      display: flex;
      align-items: center;
      font-size: 13px;
      font-weight: 600;
      color: #344b43
    }

    .nav-link:hover {
      color: var(--primary)
    }

    .nav-cta {
      padding: 12px 19px;
      border-radius: 10px;
      background: var(--primary);
      color: #fff;
      font-size: 13px;
      font-weight: 700
    }

    .menu-toggle {
      display: none
    }

    /* =========================
   HERO SLIDER - CENTER
========================= */
    .hero-slide {
      position: relative;
    }

    .hero-content {
      width: min(100% - 40px, 900px);
      margin: 0 auto;
      padding: 0 20px;
      text-align: center;

      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .hero-content h1 {
      text-align: center;
      margin-left: auto;
      margin-right: auto;
    }

    .hero-content p {
      text-align: center;
      margin-left: auto;
      margin-right: auto;
    }

    .hero-content .hero-buttons {
      justify-content: center;
    }

    .slider-container {
      height: 100%;
      position: relative;
      overflow: hidden
    }

    .slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      visibility: hidden;
      transition: opacity .7s
    }

    .slide.active {
      opacity: 1;
      visibility: visible
    }

    .slide>img {
      width: 100%;
      height: 100%;
      object-fit: cover
    }

    .slide-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(5, 32, 24, .82), rgba(5, 32, 24, .38), rgba(5, 32, 24, .08))
    }

    .slide-content {
      position: absolute;
      z-index: 2;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      width: min(900px, calc(100% - 80px));
      max-width: 900px;
      color: #fff;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .slide-label {
      display: inline-block;
      color: var(--secondary);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 1.5px;
      margin-bottom: 18px
    }

    .slide h1 {
      font-size: 54px;
      color: #fff
    }

    .slide h1 span {
      display: block;
      color: var(--secondary)
    }

    .slide p {
      max-width: 720px;
      margin: 18px auto 28px;
      color: rgba(255, 255, 255, .88);
      text-align: center;
    }

    .slide-actions {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }

    .slide-btn {
      padding: 13px 18px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 700;
      display: inline-flex;
      gap: 14px;
      align-items: center
    }

    .slide-btn-primary {
      background: var(--primary);
      color: #fff
    }

    .slide-btn-outline {
      border: 1px solid rgba(255, 255, 255, .5);
      color: #fff
    }

    .slider-btn {
      position: absolute;
      z-index: 4;
      top: 50%;
      transform: translateY(-50%);
      width: 46px;
      height: 46px;
      border: 1px solid rgba(255, 255, 255, .35);
      border-radius: 50%;
      background: rgba(0, 0, 0, .2);
      color: #fff
    }

    .slider-prev {
      left: 22px
    }

    .slider-next {
      right: 22px
    }

    .slider-bottom {
      position: absolute;
      z-index: 5;
      bottom: 28px;
      left: 0;
      right: 0;
      display: flex;
      justify-content: space-between;
      padding: 0 30px
    }

    .slider-dots {
      display: flex;
      gap: 8px
    }

    .slider-dot {
      width: 28px;
      height: 4px;
      border: 0;
      background: rgba(255, 255, 255, .4);
      cursor: pointer
    }

    .slider-dot.active {
      background: var(--secondary)
    }

    .scroll-indicator {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #fff;
      font-size: 9px
    }

    .scroll-line {
      width: 50px;
      height: 1px;
      background: var(--secondary)
    }

    .section-header {
      text-align: center;
      max-width: 720px;
      margin: 0 auto 45px
    }

    .section-label {
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 1px
    }

    .section-title {
      font-size: 34px;
      margin: 8px 0 12px
    }

    .section-description {
      font-size: 14px
    }

    .news-section {
      background: var(--light)
    }

    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px
    }

    .news-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 12px 35px rgba(18, 55, 42, .07);
      transition: .3s
    }

    .news-card:hover {
      transform: translateY(-7px);
      box-shadow: var(--shadow)
    }

    .news-image {
      height: 220px;
      display: block;
      position: relative;
      overflow: hidden;
      background: var(--primary-light)
    }

    .news-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: .4s
    }

    .news-card:hover .news-image img {
      transform: scale(1.05)
    }

    .news-placeholder {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 30px;
      font-weight: 800
    }

    .news-category {
      position: absolute;
      left: 14px;
      top: 14px;
      background: var(--secondary);
      color: var(--dark);
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 800
    }

    .news-body {
      padding: 20px
    }

    .news-date {
      font-size: 11px;
      color: #7a8a84;
      margin-bottom: 8px
    }

    .news-body h3 {
      font-size: 18px;
      margin-bottom: 9px
    }

    .news-body p {
      font-size: 13px;
      color: var(--text);
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden
    }

    .news-link {
      display: inline-block;
      margin-top: 14px;
      color: var(--primary);
      font-size: 12px;
      font-weight: 800
    }

    .program-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 24px;
      align-items: stretch;
    }

    .program-card {
      position: relative;
      min-width: 0;
      min-height: 265px;
      padding: 28px 26px 24px;
      border: 1px solid var(--border);
      border-radius: 20px;
      background: #fff;
      box-shadow: 0 10px 30px rgba(18, 55, 42, .06);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }

    .program-card::after {
      content: "";
      position: absolute;
      width: 82px;
      height: 82px;
      right: -30px;
      bottom: -30px;
      border-radius: 50%;
      background: var(--primary-light);
      pointer-events: none;
    }

    .program-card:hover {
      transform: translateY(-6px);
      border-color: rgba(8, 127, 91, .25);
      box-shadow: 0 18px 45px rgba(18, 55, 42, .11);
    }

    .program-icon {
      width: 58px;
      height: 58px;
      flex: 0 0 58px;
      margin-bottom: 18px;
      border-radius: 16px;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 29px;
      line-height: 1;
    }

    .program-card h3 {
      font-size: 18px;
      line-height: 1.4;
      margin-bottom: 8px;
      min-height: 51px;
      display: flex;
      align-items: flex-start;
    }

    .program-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
      margin-bottom: 10px;
    }

    .program-meta span {
      display: inline-flex;
      align-items: center;
      min-height: 25px;
      padding: 4px 9px;
      border-radius: 999px;
      background: #f1f7f4;
      color: var(--primary-dark);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .15px;
    }

    .program-card p {
      font-size: 13px;
      line-height: 1.65;
      margin: 0;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .program-link {
      position: relative;
      z-index: 1;
      margin-top: auto;
      padding-top: 18px;
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      width: fit-content;
    }

    .program-link:hover {
      color: var(--primary-dark);
    }

    .program-empty {
      grid-column: 1 / -1;
      padding: 35px;
      text-align: center;
      border: 1px dashed var(--border);
      border-radius: 18px;
      background: var(--light);
    }


    .about-grid,
    .student-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px
    }

    .about-image {
      min-height: 400px;
      border-radius: 24px;
      background: linear-gradient(135deg, var(--primary), #13a878);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-align: center
    }

    .about-logo {
      font-size: 55px;
      font-weight: 800
    }

    .about-image h3 {
      color: #fff;
      margin-top: 8px
    }

    .feature-list {
      display: grid;
      gap: 16px;
      margin-top: 25px
    }

    .feature {
      display: flex;
      gap: 12px
    }

    .feature-check {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      flex-shrink: 0
    }

    .feature strong,
    .feature span {
      display: block
    }

    .feature span {
      font-size: 12px
    }

    .services {
      background: var(--light)
    }

    .service-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px
    }

    .service-card,
    .student-card {
      padding: 28px;
      border: 1px solid var(--border);
      border-radius: 18px;
      background: #fff
    }

    .service-card-icon {
      font-size: 28px;
      margin-bottom: 10px
    }

    .service-card p,
    .student-card p {
      font-size: 13px
    }

    .student-card.secondary {
      background: var(--dark);
      color: #d9e8e2
    }

    .student-card.secondary h3 {
      color: #fff
    }

    .student-links {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 18px
    }

    .student-links a {
      padding: 8px 12px;
      border-radius: 8px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 11px;
      font-weight: 700
    }

    .cta {
      padding: 70px 0
    }

    .cta-box {
      padding: 55px;
      border-radius: 24px;
      background: var(--dark);
      color: #fff;
      text-align: center
    }

    .cta-box h2 {
      color: #fff
    }

    .cta-box p {
      margin: 10px auto 20px;
      max-width: 650px
    }

    .btn {
      display: inline-block;
      padding: 13px 18px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 13px
    }

    .btn-primary {
      background: var(--primary);
      color: #fff
    }

    footer {
      background: var(--dark);
      color: rgba(255, 255, 255, .55)
    }

    .footer-main {
      padding: 70px 0 45px;
      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr 1fr;
      gap: 45px
    }

    .footer-brand p {
      max-width: 320px;
      font-size: 13px;
      margin: 17px 0
    }

    .footer-logo strong {
      color: #fff
    }

    .footer-title {
      color: #fff;
      font-size: 14px;
      margin-bottom: 17px
    }

    .footer-links {
      display: grid;
      gap: 10px
    }

    .footer-links a {
      font-size: 12px
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, .08);
      padding: 20px 0;
      display: flex;
      justify-content: space-between;
      font-size: 11px
    }

    .back-top {
      position: fixed;
      right: 25px;
      bottom: 25px;
      width: 42px;
      height: 42px;
      border: 0;
      border-radius: 10px;
      background: var(--primary);
      color: #fff;
      display: none;
      z-index: 100
    }

    .back-top.show {
      display: block
    }

    .article-section {
      background: var(--light)
    }

    .back-link {
      display: inline-block;
      color: var(--primary);
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 20px
    }

    .article-wrap {
      max-width: 900px;
      margin: auto;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 22px;
      padding: 35px;
      box-shadow: var(--shadow)
    }

    .article-category {
      display: inline-block;
      color: var(--primary);
      font-weight: 800;
      font-size: 12px;
      margin-bottom: 8px
    }

    .article-wrap h1 {
      font-size: 40px
    }

    .article-meta {
      font-size: 12px;
      color: #7b8984;
      margin: 12px 0 24px
    }

    .article-image {
      width: 100%;
      max-height: 520px;
      object-fit: cover;
      border-radius: 16px;
      margin-bottom: 28px
    }

    .article-content {
      font-size: 16px;
      line-height: 1.9;
      color: #40544d
    }

    .related {
      max-width: 1100px;
      margin: 50px auto 0
    }

    .related h2 {
      margin-bottom: 22px
    }

    @media(max-width:900px) {
      .topbar {
        display: none
      }

      .nav-inner {
        min-height: 72px
      }

      .menu-toggle {
        display: block;
        width: 44px;
        height: 44px;
        border: 0;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 22px
      }

      .nav-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        display: none;
        background: #fff;
        padding: 10px 20px 25px
      }

      .nav-menu.active {
        display: block
      }

      .nav-link {
        min-height: 48px
      }

      .nav-cta {
        display: none
      }

      .hero-slider {
        height: 540px
      }

      .slide h1 {
        font-size: 38px
      }

      .news-grid,
      .program-grid {
        grid-template-columns: 1fr 1fr
      }

      .service-grid {
        grid-template-columns: 1fr 1fr
      }

      .about-grid,
      .student-grid {
        grid-template-columns: 1fr
      }

      .footer-main {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:600px) {
      .container {
        width: min(100% - 28px, 1180px)
      }

      .hero-slider {
        height: 600px
      }

      .slide-content {
        left: 50%;
        right: auto;
        width: calc(100% - 40px);
        transform: translate(-50%, -50%);
      }

      .slide h1 {
        font-size: 32px
      }

      .slider-bottom {
        padding: 0 15px
      }

      .scroll-indicator {
        display: none
      }

      .news-grid,
      .program-grid,
      .service-grid {
        grid-template-columns: 1fr
      }

      .section-title {
        font-size: 28px
      }

      .footer-main {
        grid-template-columns: 1fr
      }

      .article-wrap {
        padding: 22px
      }

      .article-wrap h1 {
        font-size: 28px
      }
    }
    </style>
  </head>

  <body>
    <!-- =========================================================
     TOP BAR
========================================================= -->

    <div class="topbar">
      <div class="container topbar-inner">
        <div class="topbar-info">
          <span>📍 Kampus FIKES</span>

          <span>✉️ info@fikes.ac.id</span>

          <span>📞 (021) 1234567</span>
        </div>

        <div class="topbar-social">
          <a href="#">Instagram</a>
          <a href="#">Facebook</a>
          <a href="#">YouTube</a>
        </div>
      </div>
    </div>

    <!-- =========================================================
     NAVBAR
========================================================= -->

    <header class="navbar" id="navbar">
      <div class="container nav-inner">
        <a href="/fikes/index.php" class="logo">
          <div class="logo-icon">F</div>
          <div class="logo-text"><strong>FIKES</strong><small>FAKULTAS ILMU KESEHATAN</small></div>
        </a>
        <button class="menu-toggle" id="menuToggle">☰</button>
        <nav class="nav-menu" id="navMenu">
          <div class="nav-item has-dropdown">
            <a href="#" class="nav-link">Tentang FIKES <span class="arrow">▾</span></a>
            <div class="dropdown">
              <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/visi-misi.php" class="dropdown-link">Visi
                  Misi</a></div>
              <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/struktur-organisasi.php"
                  class="dropdown-link">Struktur Organisasi</a></div>
              <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/sertifikat-akreditasi.php"
                  class="dropdown-link">Sertifikat Akreditasi</a></div>
              <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/unduh-logo.php" class="dropdown-link">Unduh
                  Logo</a></div>
            </div>
          </div>
          <div class="nav-item has-dropdown">
            <a href="#" class="nav-link">Kemahasiswaan <span class="arrow">▾</span></a>
            <div class="dropdown">
              <div class="dropdown-item"><a href="/fikes/page/kemahasiswaan/hima.php" class="dropdown-link">Unit
                  Himpunan Mahasiswa</a></div>
              <div class="dropdown-item"><a href="/fikes/page/kemahasiswaan/ukm.php" class="dropdown-link">UKM
                  Kemahasiswaan</a></div>
            </div>
          </div>
          <div class="nav-item"><a href="/fikes/page/program-studi/program-studi.php" class="nav-link">Program</a></div>
          <div class="nav-item has-dropdown">
            <a href="/fikes/page/akademik/akademik.php" class="nav-link">Akademik <span class="arrow">▾</span></a>
            <div class="dropdown">
              <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#kurikulum"
                  class="dropdown-link">Kurikulum &amp; Silabus</a></div>
              <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#kalender"
                  class="dropdown-link">Kalender Pendidikan</a></div>
              <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#jadwal" class="dropdown-link">Jadwal
                  Kuliah &amp; Ujian</a></div>
              <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#registrasi"
                  class="dropdown-link">Jadwal Registrasi</a></div>
              <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#dokumen"
                  class="dropdown-link">Administrasi &amp; Dokumen Mahasiswa</a></div>
              <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#penilaian"
                  class="dropdown-link">Sistem Penilaian</a></div>
            </div>
          </div>
          <!-- <div class="nav-item"><a href="#pelayanan" class="nav-link">Pelayanan FIKES</a></div> -->
          <div class="nav-item"><a href="/fikes/page/survey.php" class="nav-link">Survey</a></div>
        </nav>
        <a href="/fikes/page/program-studi/program-studi.php" class="nav-cta">Jelajahi Program <span>→</span></a>
      </div>
    </header>

    <!-- =========================================================
     HERO
========================================================= -->

    <main>
      <style>
      .akademik-hero {
        position: relative;
        overflow: hidden;
        padding: 58px 0 72px;
        background: radial-gradient(circle at 90% 20%, rgba(255, 255, 255, .18), transparent 30%), linear-gradient(120deg, #00685a 0%, #008f78 55%, #8ad0c1 150%);
        color: #fff
      }

      .akademik-hero:after {
        content: "";
        position: absolute;
        width: 360px;
        height: 360px;
        right: -120px;
        bottom: -210px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08)
      }

      .ak-breadcrumb {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 13px;
        color: rgba(255, 255, 255, .8);
        margin-bottom: 25px
      }

      .ak-breadcrumb a:hover {
        color: #fff
      }

      .akademik-hero h1 {
        font-size: clamp(38px, 5vw, 60px);
        margin: 0 0 14px;
        color: #fff;
        line-height: 1.05
      }

      .akademik-hero h1 span {
        color: #a5ead7
      }

      .akademik-hero p {
        max-width: 760px;
        margin: 0;
        color: rgba(255, 255, 255, .86);
        font-size: 16px
      }

      .akademik-wrap {
        padding: 58px 0 90px;
        background: #f7faf9
      }

      .ak-layout {
        display: grid;
        grid-template-columns: 245px minmax(0, 1fr);
        gap: 28px;
        align-items: start
      }

      .ak-side {
        position: sticky;
        top: 95px;
        background: #fff;
        border: 1px solid #e2ece8;
        border-radius: 18px;
        padding: 12px;
        box-shadow: 0 12px 35px rgba(18, 55, 42, .07)
      }

      .ak-side-title {
        padding: 13px 12px 9px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #087f5b
      }

      .ak-side a {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        color: #52635d
      }

      .ak-side a:hover,
      .ak-side a.active {
        background: #e7f7f1;
        color: #087f5b
      }

      .ak-side i {
        font-style: normal;
        width: 22px;
        text-align: center
      }

      .ak-section {
        scroll-margin-top: 100px;
        background: #fff;
        border: 1px solid #e3ece9;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 24px;
        box-shadow: 0 12px 35px rgba(18, 55, 42, .06)
      }

      .ak-section-head {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
        margin-bottom: 22px
      }

      .ak-label {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #087f5b;
        background: #e7f7f1;
        padding: 7px 10px;
        border-radius: 999px;
        margin-bottom: 9px
      }

      .ak-section h2 {
        font-size: 25px;
        margin: 0 0 8px
      }

      .ak-section-desc {
        margin: 0;
        color: #687a74;
        font-size: 14px
      }

      .ak-stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px
      }

      .ak-stat {
        padding: 16px;
        border: 1px solid #e3ece9;
        border-radius: 14px;
        background: #fbfdfc
      }

      .ak-stat strong {
        display: block;
        font-size: 22px;
        color: #087f5b
      }

      .ak-stat span {
        font-size: 12px;
        color: #6b7c77
      }

      .ak-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px
      }

      .ak-table th,
      .ak-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #edf1ef;
        text-align: left;
        vertical-align: top
      }

      .ak-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #687a74;
        background: #f7faf9
      }

      .ak-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        background: #e7f7f1;
        color: #087f5b;
        font-size: 11px;
        font-weight: 800
      }

      .ak-empty {
        padding: 25px;
        text-align: center;
        border: 1px dashed #cbdad5;
        border-radius: 14px;
        color: #70817b
      }

      .ak-calendar {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px
      }

      .ak-event {
        border-left: 4px solid #087f5b;
        background: #f7faf9;
        padding: 15px 16px;
        border-radius: 12px
      }

      .ak-event strong {
        display: block;
        color: #12372a
      }

      .ak-event small {
        color: #087f5b;
        font-weight: 800
      }

      .ak-event p {
        margin: 7px 0 0;
        font-size: 13px
      }

      .ak-doc-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px
      }

      .ak-doc {
        border: 1px solid #e3ece9;
        border-radius: 15px;
        padding: 18px;
        background: #fff
      }

      .ak-doc-icon {
        font-size: 27px;
        margin-bottom: 10px
      }

      .ak-doc h3 {
        font-size: 15px;
        margin: 0 0 7px
      }

      .ak-doc p {
        font-size: 12px;
        color: #687a74;
        margin: 0 0 13px
      }

      .ak-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #087f5b;
        color: #fff;
        padding: 9px 13px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 800
      }

      .ak-btn.outline {
        background: #fff;
        color: #087f5b;
        border: 1px solid #bfe1d5
      }

      .ak-filter {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px
      }

      .ak-filter select {
        padding: 10px 12px;
        border: 1px solid #dce8e4;
        border-radius: 10px;
        background: #fff
      }

      .ak-note {
        background: #fff8e7;
        border: 1px solid #f3dfaa;
        border-radius: 13px;
        padding: 14px;
        font-size: 13px;
        color: #765c1d;
        margin-top: 16px
      }

      .ak-list {
        margin: 0;
        padding-left: 20px
      }

      .ak-list li {
        margin: 7px 0
      }

      .ak-rating {
        display: grid;
        grid-template-columns: 1.2fr .7fr 2fr;
        gap: 10px;
        align-items: center;
        padding: 11px 0;
        border-bottom: 1px solid #edf1ef;
        font-size: 13px
      }

      .ak-rating:first-child {
        font-weight: 800;
        color: #687a74
      }

      .ak-progress {
        height: 8px;
        background: #edf3f0;
        border-radius: 999px;
        overflow: hidden
      }

      .ak-progress span {
        display: block;
        height: 100%;
        background: #087f5b
      }

      @media(max-width:900px) {
        .ak-layout {
          grid-template-columns: 1fr
        }

        .ak-side {
          position: static
        }

        .ak-doc-grid {
          grid-template-columns: 1fr 1fr
        }

        .ak-calendar {
          grid-template-columns: 1fr
        }

        .ak-stat-row {
          grid-template-columns: 1fr 1fr
        }
      }

      @media(max-width:600px) {
        .akademik-hero {
          padding: 42px 0 55px
        }

        .ak-section {
          padding: 21px
        }

        .ak-doc-grid,
        .ak-stat-row {
          grid-template-columns: 1fr
        }

        .ak-table {
          min-width: 680px
        }

        .ak-table-wrap {
          overflow: auto
        }

        .ak-rating {
          grid-template-columns: 1fr 55px 1fr
        }

        .ak-section h2 {
          font-size: 22px
        }
      }
      </style>
      <section class="akademik-hero">
        <div class="container">
          <div class="ak-breadcrumb"><a href="/fikes/index.php">⌂ Beranda</a><span>›</span><span>Akademik</span></div>
          <div class="ak-label" style="background:rgba(255,255,255,.15);color:#fff">PUSAT INFORMASI AKADEMIK</div>
          <h1>Akademik <span>FIKES</span></h1>
          <p>Informasi kurikulum, kalender pendidikan, jadwal perkuliahan dan ujian, registrasi, administrasi mahasiswa,
            dokumen akademik, hingga sistem penilaian.</p>
        </div>
      </section>
      <section class="akademik-wrap">
        <div class="container">
          <div class="ak-layout">
            <aside class="ak-side">
              <div class="ak-side-title">MENU AKADEMIK</div><a href="#kurikulum"><i>📚</i>Kurikulum &amp; Silabus</a><a
                href="#kalender"><i>🗓️</i>Kalender Akademik</a><a href="#jadwal"><i>🕘</i>Jadwal Kuliah &amp;
                Ujian</a><a href="#registrasi"><i>📝</i>Jadwal Registrasi</a><a href="#dokumen"><i>📁</i>Administrasi
                &amp; Dokumen</a><a href="#penilaian"><i>📊</i>Sistem Penilaian</a>
            </aside>
            <div>
              <section class="ak-section" id="kurikulum">
                <div class="ak-section-head">
                  <div><span class="ak-label">KURIKULUM &amp; SILABUS</span>
                    <h2>Kurikulum dan Silabus</h2>
                    <p class="ak-section-desc">Daftar mata kuliah wajib dan pilihan beserta bobot SKS yang terhubung
                      langsung dengan Program Studi.</p>
                  </div>
                </div>
                <div class="ak-filter"><select id="prodiFilter">
                    <option value="">Semua Program Studi</option><?php foreach ($prodiList as $p): ?><option
                      value="<?= e($p['id']) ?>"><?= e($p['nama']) ?> — <?= e($p['jenjang']) ?></option>
                    <?php endforeach; ?>
                  </select></div>
                <div class="ak-stat-row">
                  <div class="ak-stat"><strong><?= count($kurikulum) ?></strong><span>Mata kuliah aktif</span></div>
                  <div class="ak-stat"><strong><?= number_format($totalSks, 1) ?></strong><span>Total SKS</span></div>
                  <div class="ak-stat"><strong><?= count($prodiList) ?></strong><span>Program Studi</span></div>
                </div>
                <div class="ak-table-wrap">
                  <table class="ak-table" id="kurTable">
                    <thead>
                      <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>SKS</th>
                        <th>Jenis</th>
                        <th>Silabus</th>
                      </tr>
                    </thead>
                    <tbody><?php foreach ($kurikulum as $k): ?><tr data-prodi="<?= e($k['prodi_id']) ?>">
                        <td><?= e($k['kode_mk']) ?></td>
                        <td><strong><?= e($k['nama_mk']) ?></strong></td>
                        <td><?= e($k['prodi_nama']) ?></td>
                        <td><?= e($k['semester']) ?></td>
                        <td><?= number_format((float)$k['sks'], 1) ?></td>
                        <td><span class="ak-badge"><?= e($k['jenis'] ?: 'Wajib') ?></span></td>
                        <td><?php if (!empty($k['silabus_file'])): ?><a class="ak-btn outline"
                            href="/fikes/admin/uploads/akademik/<?= rawurlencode(basename($k['silabus_file'])) ?>"
                            target="_blank">Lihat</a><?php else: ?>- <?php endif; ?></td>
                      </tr><?php endforeach; ?></tbody>
                  </table>
                </div>
                <?php if (!$kurikulum): ?><div class="ak-empty">Belum ada data kurikulum. Data akan tampil setelah
                  dimasukkan melalui Dashboard Admin.</div><?php endif; ?>
              </section>

              <section class="ak-section" id="kalender">
                <div class="ak-section-head">
                  <div><span class="ak-label">KALENDER PENDIDIKAN</span>
                    <h2>Kalender Akademik</h2>
                    <p class="ak-section-desc">Jadwal penting selama satu tahun ajaran, termasuk masuk kuliah, minggu
                      tenang, ujian, dan masa libur.</p>
                  </div>
                </div>
                <div class="ak-calendar"><?php foreach ($kalender as $x): ?><div class="ak-event">
                    <small><?= e($x['tahun_ajaran']) ?> ·
                      <?= e($x['kategori']) ?></small><strong><?= e($x['judul']) ?></strong>
                    <p>
                      <?= tanggal_id($x['tanggal_mulai']) ?><?= $x['tanggal_selesai'] ? ' — ' . tanggal_id($x['tanggal_selesai']) : '' ?><?= $x['keterangan'] ? '<br>' . e($x['keterangan']) : '' ?>
                    </p>
                  </div><?php endforeach; ?></div><?php if (!$kalender): ?><div class="ak-empty">Belum ada kalender
                  akademik.</div><?php endif; ?>
              </section>

              <section class="ak-section" id="jadwal">
                <div class="ak-section-head">
                  <div><span class="ak-label">JADWAL KULIAH &amp; UJIAN</span>
                    <h2>Jadwal Perkuliahan dan Ujian</h2>
                    <p class="ak-section-desc">Waktu, ruang, dan pelaksanaan kuliah, UTS, serta UAS.</p>
                  </div>
                </div>
                <div class="ak-table-wrap">
                  <table class="ak-table">
                    <thead>
                      <tr>
                        <th>Jenis</th>
                        <th>Kegiatan / Mata Kuliah</th>
                        <th>Program Studi</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Ruang</th>
                      </tr>
                    </thead>
                    <tbody><?php foreach ($jadwal as $j): ?><tr>
                        <td><span class="ak-badge"><?= e($j['jenis']) ?></span></td>
                        <td>
                          <strong><?= e($j['nama_kegiatan']) ?></strong><?= $j['kode_mk'] ? '<br><small>' . e($j['kode_mk']) . '</small>' : '' ?>
                        </td>
                        <td><?= e($j['prodi_nama'] ?: 'Semua Prodi') ?></td>
                        <td><?= tanggal_id($j['tanggal']) ?></td>
                        <td><?= e(substr($j['jam_mulai'], 0, 5)) ?> - <?= e(substr($j['jam_selesai'], 0, 5)) ?></td>
                        <td><?= e($j['ruang'] ?: '-') ?></td>
                      </tr><?php endforeach; ?></tbody>
                  </table>
                </div><?php if (!$jadwal): ?><div class="ak-empty">Belum ada jadwal kuliah atau ujian.</div>
                <?php endif; ?>
              </section>

              <section class="ak-section" id="registrasi">
                <div class="ak-section-head">
                  <div><span class="ak-label">JADWAL REGISTRASI</span>
                    <h2>Pembayaran &amp; Pengisian KRS</h2>
                    <p class="ak-section-desc">Batas waktu pembayaran UKT/SPP, pengisian KRS, dan proses persetujuan
                      akademik.</p>
                  </div>
                </div>
                <div class="ak-calendar"><?php foreach ($registrasi as $r): ?><div class="ak-event">
                    <small><?= e($r['tahun_ajaran']) ?> ·
                      <?= e($r['jenis']) ?></small><strong><?= e($r['judul']) ?></strong>
                    <p>
                      <?= tanggal_id($r['tanggal_mulai']) ?><?= $r['tanggal_selesai'] ? ' — ' . tanggal_id($r['tanggal_selesai']) : '' ?><?= $r['keterangan'] ? '<br>' . e($r['keterangan']) : '' ?>
                    </p>
                  </div><?php endforeach; ?></div><?php if (!$registrasi): ?><div class="ak-empty">Belum ada jadwal
                  registrasi.</div><?php endif; ?><div class="ak-note">Perhatikan batas waktu pembayaran UKT/SPP dan
                  pengisian KRS agar status akademik semester dapat diproses tepat waktu.</div>
              </section>

              <section class="ak-section" id="dokumen">
                <div class="ak-section-head">
                  <div><span class="ak-label">ADMINISTRASI &amp; DOKUMEN MAHASISWA</span>
                    <h2>Panduan, Formulir &amp; Layanan Kelulusan</h2>
                    <p class="ak-section-desc">Dokumen digital dan informasi layanan akademik mahasiswa.</p>
                  </div>
                </div>
                <div class="ak-doc-grid">
                  <?php $icons = ['panduan' => '📘', 'formulir' => '📄', 'kelulusan' => '🎓'];
                foreach ($dokumen as $d): ?><article class="ak-doc">
                    <div class="ak-doc-icon"><?= $icons[$d['kategori']] ?? '📁' ?></div>
                    <h3><?= e($d['judul']) ?></h3>
                    <p><?= e($d['deskripsi']) ?></p><?php if ($d['kategori'] === 'kelulusan' && $d['isi']): ?><ul
                      class="ak-list">
                      <?php foreach (preg_split('/\r\n|\r|\n/', trim($d['isi'])) as $li): if (trim($li) !== ''): ?><li>
                        <?= e($li) ?></li><?php endif;
                                                      endforeach; ?></ul>
                    <?php endif; ?><?php if (!empty($d['file_dokumen'])): ?><a class="ak-btn"
                      href="/fikes/admin/uploads/akademik/<?= rawurlencode(basename($d['file_dokumen'])) ?>"
                      target="_blank">Unduh Dokumen</a><?php elseif (!empty($d['link_url'])): ?><a class="ak-btn"
                      href="<?= e($d['link_url']) ?>" target="_blank">Buka Informasi</a><?php endif; ?>
                  </article><?php endforeach; ?></div><?php if (!$dokumen): ?><div class="ak-empty">Belum ada dokumen
                  atau informasi administrasi mahasiswa.</div><?php endif; ?>
              </section>

              <section class="ak-section" id="penilaian">
                <div class="ak-section-head">
                  <div><span class="ak-label">SISTEM PENILAIAN</span>
                    <h2>Komponen &amp; Skala Penilaian</h2>
                    <p class="ak-section-desc">Informasi komponen penilaian pembelajaran yang ditetapkan dan dikelola
                      melalui Dashboard Admin.</p>
                  </div>
                </div>
                <div class="ak-rating">
                  <div>Komponen</div>
                  <div>Bobot</div>
                  <div>Keterangan</div>
                </div><?php foreach ($penilaian as $n): ?><div class="ak-rating">
                  <div><strong><?= e($n['komponen']) ?></strong></div>
                  <div><span class="ak-badge"><?= number_format((float)$n['bobot'], 0) ?>%</span></div>
                  <div><?= e($n['keterangan']) ?></div>
                </div><?php endforeach; ?><?php if (!$penilaian): ?><div class="ak-empty">Belum ada komponen penilaian.
                </div><?php endif; ?><div class="ak-note">Skala nilai huruf dan konversi nilai dapat disesuaikan dengan
                  ketentuan akademik FIKES melalui Dashboard Admin.</div>
              </section>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- =========================================================
     FOOTER
========================================================= -->

    <footer>
      <div class="container footer-main">
        <div class="footer-brand">
          <div class="logo footer-logo">
            <div class="logo-icon">F</div>

            <div class="logo-text">
              <strong style="color: white"> FIKES </strong>

              <small> FAKULTAS ILMU KESEHATAN </small>
            </div>
          </div>

          <p>
            Membangun generasi kesehatan yang profesional, berintegritas,
            inovatif, dan berorientasi kepada masyarakat.
          </p>
        </div>

        <div>
          <h4 class="footer-title">Tentang FIKES</h4>

          <div class="footer-links">
            <a href="#"> Visi Misi </a>

            <a href="#"> Struktur Organisasi </a>

            <a href="#"> Akreditasi </a>

            <a href="#"> Daftar Dosen </a>
          </div>
        </div>

        <div>
          <h4 class="footer-title">Program Studi</h4>

          <div class="footer-links">
            <a href="#"> Profesi Ners </a>

            <a href="#"> Ilmu Keperawatan </a>

            <a href="#"> Farmasi </a>

            <a href="#"> Kebidanan </a>

            <a href="#"> K3 </a>
          </div>
        </div>

        <div>
          <h4 class="footer-title">Informasi</h4>

          <div class="footer-links">
            <a href="/fikes/page/akademik/akademik.php"> Akademik </a>

            <a href="#"> Kemahasiswaan </a>

            <a href="#"> Pelayanan FIKES </a>

            <a href="#"> Survey </a>
          </div>
        </div>

        <!--MAP PETA-->
        <!-- =========================================================
     LOKASI & PETA
========================================================== -->

        <div class="footer-location">
          <div class="location-header">
            <div class="location-icon">
              <i class="fa-solid fa-location-dot"></i>
            </div>

            <div>
              <h3>Lokasi Kampus</h3>

              <p>Fakultas Ilmu Kesehatan</p>
            </div>
          </div>

          <!-- PETA -->

          <div class="map-card">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbef42471658d%3A0x883656d1325ef066!2sUniversitas%20Bhamada%20Slawi!5e0!3m2!1sid!2sid!4v1787544396003!5m2!1sid!2sid"
              width="600" height="450" style="border: 0" allowfullscreen="" loading="lazy"
              referrerpolicy="strict-origin-when-cross-origin" title="Lokasi Fakultas Ilmu Kesehatan" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
            </iframe>

            <div class="map-overlay">
              <div class="map-info">
                <div class="map-info-icon">
                  <i class="fa-solid fa-location-dot"></i>
                </div>

                <div>
                  <strong> Fakultas Ilmu Kesehatan </strong>

                  <span> Lihat lokasi kampus </span>
                </div>
              </div>

              <a href="#" target="_blank" class="map-direction">
                <i class="fa-solid fa-diamond-turn-right"></i>

                Petunjuk Arah
              </a>
            </div>
          </div>

          <!-- ALAMAT -->

          <div class="footer-contact location-contact">
            <i class="fa-solid fa-location-dot"></i>

            <span>
              Alamat Fakultas Ilmu Kesehatan, silakan sesuaikan dengan alamat
              kampus.
            </span>
          </div>

          <div class="footer-contact">
            <i class="fa-solid fa-phone"></i>

            <span> Nomor Telepon FIKES </span>
          </div>

          <div class="footer-contact">
            <i class="fa-solid fa-envelope"></i>

            <span> email@fikes.ac.id </span>
          </div>
        </div>
        <!--MAP PETA-->
      </div>

      <div class="container footer-bottom">
        <span>
          © <span id="year"></span> Fakultas Ilmu Kesehatan. All Rights
          Reserved.
        </span>

        <span> Website FIKES </span>
      </div>
    </footer>

    <!-- BACK TO TOP -->

    <button class="back-top" id="backTop">↑</button>
    <!-- <script src="assets/js/main.js"></script> -->
    <script>
    let current = 0;
    let slides = [];
    let dots = [];

    function showSlide(n) {
      if (!slides.length) return;
      current = (n + slides.length) % slides.length;
      slides.forEach((s, i) => s.classList.toggle("active", i === current));
      dots.forEach((d, i) => d.classList.toggle("active", i === current));
    }

    function changeSlide(n) {
      showSlide(current + n);
    }

    function currentSlide(n) {
      showSlide(n - 1);
    }
    document.addEventListener("DOMContentLoaded", () => {
      slides = [...document.querySelectorAll(".slide")];
      dots = [...document.querySelectorAll(".slider-dot")];
      showSlide(0);
      if (slides.length > 1) setInterval(() => changeSlide(1), 7000);
      const t = document.getElementById("menuToggle"),
        m = document.getElementById("navMenu");
      if (t && m) t.onclick = () => m.classList.toggle("active");
      const nav = document.getElementById("navbar");
      window.addEventListener("scroll", () => {
        if (nav) nav.classList.toggle("scrolled", scrollY > 20);
        const b = document.getElementById("backTop");
        if (b) b.classList.toggle("show", scrollY > 500)
      });
      const b = document.getElementById("backTop");
      if (b) b.onclick = () => scrollTo({
        top: 0,
        behavior: "smooth"
      });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const f = document.getElementById('prodiFilter');
      if (f) {
        f.addEventListener('change', function() {
          document.querySelectorAll('#kurTable tbody tr').forEach(r => {
            r.style.display = !this.value || r.dataset.prodi === this.value ? '' : 'none';
          });
        });
      }
      document.querySelectorAll('.ak-side a').forEach(a => a.addEventListener('click', () => document
        .querySelectorAll('.ak-side a').forEach(x => x.classList.remove('active'))));
    });
    </script>
  </body>

</html>
