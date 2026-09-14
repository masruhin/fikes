<?php
require_once __DIR__ . '/admin/config/database.php';
function e($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Membuat URL gambar slider yang aman untuk frontend.
// Jika database hanya menyimpan nama file, otomatis diarahkan ke admin/uploads/slider/.
function sliderImageUrl($gambar)
{
  $gambar = trim((string) $gambar);
  if ($gambar === '') {
    return '';
  }

  // URL eksternal tetap digunakan apa adanya.
  if (preg_match('~^(https?:)?//~i', $gambar)) {
    return $gambar;
  }

  $gambar = str_replace('\\', '/', $gambar);

  // Path yang sudah lengkap/relatif tidak ditambahkan folder lagi.
  if (
    str_starts_with($gambar, '/') ||
    str_starts_with($gambar, 'admin/') ||
    str_starts_with($gambar, 'assets/') ||
    str_starts_with($gambar, './') ||
    str_starts_with($gambar, '../') ||
    str_contains($gambar, '/')
  ) {
    return $gambar;
  }

  return 'admin/uploads/slider/' . rawurlencode(basename($gambar));
}

function newsImageUrl($gambar)
{
  $gambar = trim((string) $gambar);
  if ($gambar === '') {
    return '';
  }
  if (preg_match('~^(https?:)?//~i', $gambar)) {
    return $gambar;
  }
  $gambar = str_replace('\\', '/', $gambar);
  if (
    str_starts_with($gambar, '/') ||
    str_starts_with($gambar, 'admin/') ||
    str_starts_with($gambar, 'assets/') ||
    str_starts_with($gambar, './') ||
    str_starts_with($gambar, '../') ||
    str_contains($gambar, '/')
  ) {
    return $gambar;
  }
  return 'admin/uploads/berita/' . rawurlencode(basename($gambar));
}

$sliders = $pdo->query("SELECT * FROM slider_beranda WHERE status='aktif' ORDER BY nomor_urut ASC, id DESC")->fetchAll();

$stmt = $pdo->query("SELECT id,kode_prodi,nama,jenjang,gelar,deskripsi FROM program_studi WHERE status='aktif' ORDER BY id ASC");
$prodiList = $stmt->fetchAll();

// Statistik homepage diambil langsung dari database.
// Helper ini mengecek tabel terlebih dahulu agar homepage tidak error
// jika tabel mahasiswa belum tersedia.
function tableExists(PDO $pdo, $table)
{
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
  $stmt->execute([$table]);
  return (bool) $stmt->fetchColumn();
}

$jumlahProdi = count($prodiList);
$jumlahDosen = tableExists($pdo, 'dosen')
  ? (int) $pdo->query("SELECT COUNT(*) FROM dosen")->fetchColumn()
  : 0;
$jumlahMahasiswa = tableExists($pdo, 'mahasiswa')
  ? (int) $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn()
  : 0;

$stmt = $pdo->query("SELECT * FROM berita WHERE status='terbit' ORDER BY tanggal_terbit DESC, id DESC LIMIT 6");
$berita = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>FIKES - Fakultas Ilmu Kesehatan</title>

  <meta name="description"
    content="Website resmi Fakultas Ilmu Kesehatan - Informasi akademik, program studi, kemahasiswaan, pelayanan dan informasi FIKES." />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
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
      <!-- LOGO -->

      <a href="index.php" class="logo">
        <div class="logo-icon">F</div>

        <div class="logo-text">
          <strong>FIKES</strong>
          <small>FAKULTAS ILMU KESEHATAN</small>
        </div>
      </a>

      <!-- MOBILE BUTTON -->

      <button class="menu-toggle" id="menuToggle">☰</button>

      <!-- NAVIGATION -->

      <nav class="nav-menu" id="navMenu">
        <!-- TENTANG FIKES -->

        <div class="nav-item has-dropdown">
          <a href="#" class="nav-link">
            Tentang FIKES
            <span class="arrow">▾</span>
          </a>

          <div class="dropdown">
            <div class="dropdown-item">
              <a href="page/tentang-fikes/visi-misi.php" class="dropdown-link"> Visi Misi </a>
            </div>

            <div class="dropdown-item">
              <a href="page/tentang-fikes/struktur-organisasi.php" class="dropdown-link">
                Struktur Organisasi
              </a>
            </div>

            <div class="dropdown-item">
              <a href="page/tentang-fikes/sertifikat-akreditasi.php" class="dropdown-link">
                Sertifikat Akreditasi
              </a>
            </div>

            <div class="dropdown-item">
              <a href="page/tentang-fikes/unduh-logo.php" class="dropdown-link"> Unduh Logo </a>
            </div>

            <!-- DAFTAR DOSEN -->
            <div class="dropdown-item">
              <a href="page/dosen/dosen.php" class="dropdown-link"> Daftar Dosen </a>
            </div>
            <!-- <div class="dropdown-item has-dropdown">
                <a href="#" class="dropdown-link">
                  Daftar Dosen
                  <span>›</span>
                </a>

                <div class="dropdown">
                  <div class="dropdown-item">
                    <a href="page/dosen/dosen.php" class="dropdown-link"> Keperawatan </a>
                  </div>
                  <div class="dropdown-item">
                    <a href="dosen.html" class="dropdown-link"> Keperawatan </a>
                  </div>

                  <div class="dropdown-item">
                    <a href="#" class="dropdown-link"> Kebidanan </a>
                  </div>

                  <div class="dropdown-item">
                    <a href="#" class="dropdown-link"> Farmasi </a>
                  </div>

                  <div class="dropdown-item">
                    <a href="#" class="dropdown-link"> K3 </a>
                  </div>
                </div>
              </div> -->
          </div>
        </div>



        <div class="nav-item has-dropdown">
          <a href="#" class="nav-link">
            Kemahasiswaan
            <span class="arrow">▾</span>
          </a>

          <div class="dropdown">
            <div class="dropdown-item">
              <a href="himpunan-mahasiswa.html" class="dropdown-link">
                Himpunan Mahasiswa
              </a>
            </div>

            <div class="dropdown-item">
              <a href="unit-kegiatan-mahasiswa.html" class="dropdown-link">
                Unit Kegiatan Mahasiswa
              </a>
            </div>
          </div>
        </div>

        <!-- PROGRAM VOKASI -->



        <div class="nav-item">
          <a href="page/program-studi/program-studi.php" class="nav-link"> Program </a>
        </div>
        <!-- AKADEMIK -->

        <div class="nav-item">
          <a href="#akademik" class="nav-link"> Akademik </a>
        </div>

        <!-- PELAYANAN -->

        <div class="nav-item">
          <a href="#pelayanan" class="nav-link"> Pelayanan FIKES </a>
        </div>

        <!-- SURVEY -->

        <div class="nav-item">
          <a href="#survey" class="nav-link"> Survey </a>
        </div>
      </nav>

      <a href="#program" class="nav-cta"> Jelajahi Program </a>
    </div>
  </header>

  <!-- =========================================================
     HERO
========================================================= -->

  <main>
    <!-- ==========================================
     PREMIUM HERO SLIDER
========================================== -->

    <section class="hero-slider">
      <div class="slider-container">
        <?php if ($sliders): foreach ($sliders as $i => $slide): ?>
            <div class="slide <?= $i === 0 ? 'active' : '' ?>">
              <img src="<?= e(sliderImageUrl($slide['gambar'])) ?>" alt="<?= e($slide['judul']) ?>" />
              <div class="slide-overlay"></div>
              <div class="slide-content">
                <?php if (!empty($slide['label'])): ?><span
                    class="slide-label"><?= e($slide['label']) ?></span><?php endif; ?>
                <h1>
                  <?= nl2br(e($slide['judul'])) ?><?php if (!empty($slide['highlight'])): ?><span><?= e($slide['highlight']) ?></span><?php endif; ?>
                </h1>
                <?php if (!empty($slide['deskripsi'])): ?><p><?= e($slide['deskripsi']) ?></p><?php endif; ?>
                <div class="slide-actions">
                  <?php if (!empty($slide['link_utama'])): ?><a href="<?= e($slide['link_utama']) ?>"
                      class="slide-btn slide-btn-primary"><span><?= e($slide['teks_tombol_utama'] ?: 'Lihat Selengkapnya') ?></span><i>→</i></a><?php endif; ?>
                  <?php if (!empty($slide['link_kedua'])): ?><a href="<?= e($slide['link_kedua']) ?>"
                      class="slide-btn slide-btn-outline"><span><?= e($slide['teks_tombol_kedua'] ?: 'Selengkapnya') ?></span><i>↗</i></a><?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach;
        else: ?>
          <div class="slide active">
            <div class="slide-overlay"></div>
            <div class="slide-content"><span class="slide-label">FAKULTAS ILMU KESEHATAN</span>
              <h1>Membangun Generasi <span>Tenaga Kesehatan Profesional</span></h1>
              <p>Wujudkan pendidikan kesehatan yang unggul, profesional, inovatif, dan berintegritas.</p>
            </div>
          </div>
        <?php endif; ?>

        <button class="slider-btn slider-prev" onclick="changeSlide(-1)"
          aria-label="Slide sebelumnya"><span>←</span></button>
        <button class="slider-btn slider-next" onclick="changeSlide(1)"
          aria-label="Slide berikutnya"><span>→</span></button>

        <div class="slider-bottom">
          <div class="slider-dots">
            <?php foreach ($sliders as $i => $slide): ?><button class="slider-dot <?= $i === 0 ? 'active' : '' ?>"
                onclick="currentSlide(<?= $i + 1 ?>)" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?>
          </div>
          <div class="scroll-indicator"><span>SCROLL UNTUK MENJELAJAHI</span>
            <div class="scroll-line"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- BERITA TERBARU -->
    <section class="section news-section" id="berita">
      <div class="container">
        <div class="section-header">
          <span class="section-label">Informasi Terkini</span>
          <h2 class="section-title">Berita & Kegiatan FIKES</h2>
          <p class="section-description">Informasi terbaru, kegiatan, pengumuman, dan kabar seputar Fakultas Ilmu
            Kesehatan.</p>
        </div>
        <div class="news-grid">
          <?php foreach ($berita as $b): ?>
            <article class="news-card">
              <a href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>" class="news-image">
                <?php if (!empty($b['gambar'])): ?><img src="<?= e(newsImageUrl($b['gambar'])) ?>"
                    alt="<?= e($b['judul']) ?>"><?php else: ?><div class="news-placeholder">FIKES</div><?php endif; ?>
                <span class="news-category"><?= e($b['kategori']) ?></span>
              </a>
              <div class="news-body">
                <div class="news-date">📅 <?= date('d M Y', strtotime($b['tanggal_terbit'])) ?></div>
                <h3><a href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>"><?= e($b['judul']) ?></a></h3>
                <p><?= e($b['ringkasan']) ?></p>
                <a href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>" class="news-link">Baca Selengkapnya →</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="stats">
      <div class="container">
        <div class="stats-grid">
          <div class="stat">
            <div class="stat-number"><?= $jumlahProdi ?></div>
            <div class="stat-label">Program Studi</div>
          </div>

          <div class="stat">
            <div class="stat-number"><?= $jumlahDosen ?></div>
            <div class="stat-label">Dosen</div>
          </div>

          <div class="stat">
            <div class="stat-number"><?= $jumlahMahasiswa ?></div>
            <div class="stat-label">Mahasiswa</div>
          </div>

          <div class="stat">
            <div class="stat-number" data-target="20">0</div>
            <div class="stat-label">Tahun Pengalaman</div>
          </div>
        </div>
      </div>
    </section>

    <!-- =========================================================
     ABOUT
========================================================= -->

    <section class="section about" id="tentang">
      <div class="container about-grid">
        <div class="about-image">
          <div class="about-image-content">
            <div class="about-logo">FIKES</div>

            <h3>Fakultas Ilmu Kesehatan</h3>

            <p>Pendidikan kesehatan untuk masa depan yang lebih baik.</p>
          </div>
        </div>

        <div class="about-content">
          <span class="section-label"> Tentang FIKES </span>

          <h2>Menjadi bagian dari perjalanan masa depan kesehatan.</h2>

          <p>
            Fakultas Ilmu Kesehatan merupakan lingkungan pendidikan yang
            berkomitmen menghasilkan lulusan profesional, kompeten,
            berkarakter, dan mampu memberikan kontribusi nyata bagi
            masyarakat.
          </p>

          <div class="feature-list">
            <div class="feature">
              <div class="feature-check">✓</div>

              <div>
                <strong> Pendidikan Berkualitas </strong>

                <span>
                  Proses pembelajaran yang relevan dengan kebutuhan dunia
                  kesehatan.
                </span>
              </div>
            </div>

            <div class="feature">
              <div class="feature-check">✓</div>

              <div>
                <strong> Tenaga Pengajar Profesional </strong>

                <span>
                  Didukung dosen dan tenaga akademik yang kompeten.
                </span>
              </div>
            </div>

            <div class="feature">
              <div class="feature-check">✓</div>

              <div>
                <strong> Berorientasi Masyarakat </strong>

                <span>
                  Mengembangkan pendidikan yang memberikan dampak bagi
                  masyarakat.
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =========================================================
     PROGRAM STUDI
========================================================= -->

    <section class="section" id="program">
      <div class="container">
        <div class="section-header">
          <span class="section-label"> Program Pendidikan </span>

          <h2 class="section-title">Program Studi FIKES</h2>

          <p class="section-description">
            Pilih program pendidikan yang sesuai dengan minat dan tujuan
            karier Anda.
          </p>
        </div>

        <div class="program-grid">
          <?php if ($prodiList): ?>
            <?php foreach ($prodiList as $p): ?>
              <?php
              $namaProdi = trim((string)($p['nama'] ?? ''));
              $namaLower = strtolower($namaProdi);
              $icon = '🎓';

              if (str_contains($namaLower, 'farmasi')) {
                $icon = '💊';
              } elseif (str_contains($namaLower, 'bidan') || str_contains($namaLower, 'kebidanan')) {
                $icon = '👩‍🍼';
              } elseif (str_contains($namaLower, 'k3') || str_contains($namaLower, 'keselamatan')) {
                $icon = '🦺';
              } elseif (str_contains($namaLower, 'keperawatan') || str_contains($namaLower, 'ners')) {
                $icon = '🩺';
              }
              ?>
              <article class="program-card">
                <div class="program-icon" aria-hidden="true"><?= $icon ?></div>

                <h3><?= e($namaProdi) ?></h3>

                <div class="program-meta">
                  <?php if (!empty($p['jenjang'])): ?>
                    <span><?= e($p['jenjang']) ?></span>
                  <?php endif; ?>

                  <?php if (!empty($p['gelar'])): ?>
                    <span><?= e($p['gelar']) ?></span>
                  <?php endif; ?>
                </div>

                <p>
                  <?= e($p['deskripsi'] ?: 'Informasi program studi Fakultas Ilmu Kesehatan.') ?>
                </p>

                <a href="page/program-studi/detail-prodi.php?kode=<?= urlencode($p['kode_prodi']) ?>"
                  class="program-link">
                  Selengkapnya <span>→</span>
                </a>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="program-empty">
              Belum ada program studi aktif yang tersedia.
            </div>
          <?php endif; ?>
        </div>
    </section>

    <!-- =========================================================
     PELAYANAN
========================================================= -->

    <section class="section services" id="pelayanan">
      <div class="container">
        <div class="section-header">
          <span class="section-label"> Pelayanan FIKES </span>

          <h2 class="section-title">Layanan Untuk Sivitas Akademika</h2>

          <p class="section-description">
            Akses berbagai layanan akademik dan informasi yang tersedia di
            FIKES.
          </p>
        </div>

        <div class="service-grid">
          <div class="service-card">
            <div class="service-card-icon">📚</div>

            <h3>Layanan Akademik</h3>

            <p>Informasi administrasi dan kebutuhan akademik mahasiswa.</p>
          </div>

          <div class="service-card">
            <div class="service-card-icon">📝</div>

            <h3>Informasi Pendaftaran</h3>

            <p>Informasi penerimaan mahasiswa baru dan proses pendaftaran.</p>
          </div>

          <div class="service-card">
            <div class="service-card-icon">👨‍🎓</div>

            <h3>Kemahasiswaan</h3>

            <p>Informasi organisasi dan kegiatan mahasiswa FIKES.</p>
          </div>

          <div class="service-card">
            <div class="service-card-icon">🔎</div>

            <h3>Survey</h3>

            <p>
              Berikan masukan untuk meningkatkan kualitas pelayanan FIKES.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- =========================================================
     KEMAHASISWAAN
========================================================= -->

    <section class="section" id="akademik">
      <div class="container">
        <div class="student-grid">
          <div class="student-card">
            <h3>Kehidupan Kemahasiswaan</h3>

            <p>
              Kembangkan potensi, kreativitas, kepemimpinan, dan pengalaman
              organisasi selama menjadi mahasiswa FIKES.
            </p>

            <div class="student-links">
              <a href="#"> Himpunan Mahasiswa </a>

              <a href="#"> UKM </a>

              <a href="#"> BEM </a>

              <a href="#"> DPM </a>
            </div>
          </div>

          <div class="student-card secondary">
            <h3>Akademik</h3>

            <p>
              Temukan informasi akademik, kalender akademik, jadwal,
              pengumuman, dan berbagai informasi pembelajaran.
            </p>

            <div class="student-links">
              <a href="#"> Kalender Akademik </a>

              <a href="#"> Jadwal </a>

              <a href="#"> Pengumuman </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =========================================================
     SURVEY / CTA
========================================================= -->

    <section class="cta" id="survey">
      <div class="container">
        <div class="cta-box">
          <h2>Suara Anda Penting Bagi Kami</h2>

          <p>
            Bantu FIKES meningkatkan kualitas pelayanan dengan memberikan
            penilaian dan masukan melalui survey kepuasan.
          </p>

          <a href="#" class="btn btn-primary" style="background: #f4b942; color: #12372a">
            Isi Survey Sekarang →
          </a>
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
          <a href="#"> Akademik </a>

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
</body>

</html>
