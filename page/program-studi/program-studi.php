<?php
require_once __DIR__ . '/../../admin/config/database.php';

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* =====================================================
   DAFTAR PROGRAM STUDI
   ===================================================== */

$prodiList = $pdo->query("
    SELECT id, kode_prodi, nama, jenjang, gelar, akreditasi, gambar
    FROM program_studi
    WHERE status = 'aktif'
    ORDER BY nama ASC
")->fetchAll();


/* =====================================================
   FILTER & SEARCH
   ===================================================== */

$filter = trim($_GET['jenjang'] ?? '');
$search = trim($_GET['search'] ?? '');

$where = ["status = 'aktif'"];
$params = [];


/* Filter jenjang */
if ($filter !== '') {
  $where[] = "jenjang = :jenjang";
  $params[':jenjang'] = $filter;
}


/* Pencarian */
if ($search !== '') {
  $where[] = "(nama LIKE :search OR kode_prodi LIKE :search)";
  $params[':search'] = '%' . $search . '%';
}


/* =====================================================
   QUERY PROGRAM STUDI
   ===================================================== */

$sql = "
    SELECT
        id,
        kode_prodi,
        nama,
        jenjang,
        gelar,
        deskripsi,
        akreditasi,
        gambar
    FROM program_studi
    WHERE " . implode(' AND ', $where) . "
    ORDER BY nama ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programs = $stmt->fetchAll();


/* =====================================================
   DAFTAR JENJANG
   ===================================================== */

$jenjangList = $pdo->query("
    SELECT DISTINCT jenjang
    FROM program_studi
    WHERE status = 'aktif'
    ORDER BY jenjang
")->fetchAll(PDO::FETCH_COLUMN);
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Program Studi | FIKES</title>
    <link rel="stylesheet" href="prodi.css">
    <style>
    /* =========================================================
   DETAIL PROGRAM STUDI - MODERN FIKES
   ========================================================= */

    :root {
      --green: #008f72;
      --green-dark: #006b59;
      --green-soft: #eaf7f3;
      --green-light: #f4fbf9;
      --text: #173b36;
      --text-soft: #607773;
      --border: #dcebe7;
      --white: #ffffff;
      --bg: #f7faf9;
      --shadow: 0 15px 45px rgba(0, 80, 65, .08);
    }


    /* =========================================================
   GLOBAL
   ========================================================= */

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: "Poppins", "Segoe UI", Arial, sans-serif;
      line-height: 1.6;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .container {
      width: min(1180px, calc(100% - 40px));
      margin: auto;
    }


    /* =========================================================
   HERO
   ========================================================= */

    .prodi-hero {
      position: relative;
      overflow: hidden;
      padding: 38px 0 60px;
      background:
        radial-gradient(circle at 90% 20%, rgba(255, 255, 255, .20), transparent 30%),
        linear-gradient(120deg, #00685a 0%, #008f78 55%, #8ad0c1 150%);
      color: #fff;
    }

    .prodi-hero::after {
      content: "";
      position: absolute;
      width: 320px;
      height: 320px;
      right: -100px;
      bottom: -180px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .08);
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 28px;
      font-size: 13px;
      color: rgba(255, 255, 255, .82);
    }

    .breadcrumb a:hover {
      color: #fff;
    }

    .hero-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 7px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: .8px;
      text-transform: uppercase;
    }

    .prodi-hero h1 {
      margin: 0;
      font-size: clamp(38px, 5vw, 58px);
      line-height: 1.05;
      font-weight: 800;
      letter-spacing: -1px;
    }

    .hero-description {
      max-width: 680px;
      margin: 15px 0 0;
      color: rgba(255, 255, 255, .9);
      font-size: 15px;
    }


    /* =========================================================
   MAIN PROFILE CARD
   ========================================================= */

    .prodi-main {
      position: relative;
      z-index: 2;
      margin-top: -28px;
      padding-bottom: 65px;
    }

    .prodi-profile {
      display: grid;
      grid-template-columns: 280px minmax(0, 1fr) 285px;
      gap: 28px;
      padding: 24px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 22px;
      box-shadow: var(--shadow);
    }


    /* =========================================================
   PHOTO FRAME
   ========================================================= */

    .prodi-photo-wrap {
      position: relative;
    }

    .prodi-photo {
      position: relative;
      height: 330px;
      overflow: hidden;
      border-radius: 20px;
      background: linear-gradient(145deg, #e8f7f3, #d8efea);
      border: 10px solid #eef8f5;
      box-shadow: inset 0 0 0 1px #d3ebe5;
    }

    .prodi-photo::before {
      content: "";
      position: absolute;
      width: 100px;
      height: 100px;
      left: -30px;
      top: -30px;
      border-radius: 50%;
      background: var(--green);
      opacity: .95;
    }

    .prodi-photo::after {
      content: "";
      position: absolute;
      width: 90px;
      height: 90px;
      right: -40px;
      bottom: -35px;
      border-radius: 50%;
      background: #009d80;
    }

    .prodi-photo img {
      position: relative;
      z-index: 2;
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .prodi-photo-placeholder {
      position: relative;
      z-index: 2;
      display: flex;
      width: 100%;
      height: 100%;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: var(--green);
    }

    .prodi-photo-placeholder strong {
      font-size: 62px;
      line-height: 1;
      font-weight: 800;
    }

    .prodi-photo-placeholder span {
      margin-top: 12px;
      font-size: 14px;
      color: var(--text);
    }

    .status-badge {
      position: absolute;
      left: 20px;
      bottom: 18px;
      z-index: 5;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 13px;
      border-radius: 999px;
      background: var(--green);
      color: #fff;
      font-size: 12px;
      font-weight: 700;
      box-shadow: 0 7px 20px rgba(0, 143, 114, .25);
    }


    /* =========================================================
   PROFILE CONTENT
   ========================================================= */

    .prodi-content {
      padding: 25px 4px;
    }

    .section-kicker {
      margin-bottom: 7px;
      color: var(--green);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    .prodi-content h2 {
      margin: 0;
      font-size: 30px;
      line-height: 1.2;
      font-weight: 800;
      color: var(--text);
    }

    .prodi-degree {
      margin-top: 6px;
      color: var(--green);
      font-size: 14px;
      font-weight: 600;
    }

    .prodi-description {
      margin-top: 18px;
      color: var(--text-soft);
      font-size: 14px;
    }


    /* =========================================================
   INFO GRID
   ========================================================= */

    .prodi-info-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-top: 22px;
    }

    .info-item {
      display: flex;
      align-items: flex-start;
      gap: 11px;
      padding: 12px;
      border-radius: 13px;
      background: var(--green-light);
      border: 1px solid #e3f1ed;
    }

    .info-icon {
      flex: 0 0 34px;
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      background: var(--green-soft);
      color: var(--green);
      font-size: 14px;
    }

    .info-item small {
      display: block;
      margin-bottom: 2px;
      color: #75908b;
      font-size: 10px;
    }

    .info-item strong {
      display: block;
      color: var(--text);
      font-size: 13px;
    }


    /* =========================================================
   RIGHT INFORMATION PANEL
   ========================================================= */

    .short-info {
      align-self: stretch;
      padding: 22px 20px;
      border-radius: 18px;
      background: #eff9f6;
      border: 1px solid #e0efeb;
    }

    .short-info-title {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 18px;
      color: var(--text);
      font-size: 14px;
      font-weight: 800;
    }

    .short-info-title i {
      color: var(--green);
    }

    .short-item {
      padding: 12px 0;
      border-bottom: 1px solid #d6e9e4;
    }

    .short-item:last-child {
      border-bottom: 0;
    }

    .short-item small {
      display: block;
      margin-bottom: 3px;
      color: #76908c;
      font-size: 10px;
    }

    .short-item strong {
      display: block;
      color: var(--text);
      font-size: 13px;
      line-height: 1.4;
    }


    /* =========================================================
   BACK BUTTON
   ========================================================= */

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 22px;
      padding: 10px 17px;
      border: 1px solid var(--green);
      border-radius: 11px;
      color: var(--green);
      background: #fff;
      font-size: 13px;
      font-weight: 700;
      transition: .2s ease;
    }

    .btn-back:hover {
      background: var(--green);
      color: #fff;
      transform: translateY(-2px);
    }


    /* =========================================================
   CONTENT SECTIONS
   ========================================================= */

    .prodi-section {
      padding: 70px 0 0;
    }

    .section-heading {
      margin-bottom: 28px;
    }

    .section-heading .section-kicker {
      margin-bottom: 5px;
    }

    .section-heading h2 {
      margin: 0;
      font-size: 28px;
      line-height: 1.2;
    }

    .section-heading p {
      max-width: 700px;
      margin: 9px 0 0;
      color: var(--text-soft);
      font-size: 14px;
    }


    /* =========================================================
   VISI
   ========================================================= */

    .visi-box {
      position: relative;
      padding: 30px 34px;
      overflow: hidden;
      border-radius: 18px;
      background: linear-gradient(120deg, #006d5d, #00977b);
      color: #fff;
      box-shadow: 0 14px 35px rgba(0, 100, 80, .14);
    }

    .visi-box::after {
      content: "";
      position: absolute;
      width: 180px;
      height: 180px;
      right: -70px;
      top: -80px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .09);
    }

    .visi-box p {
      position: relative;
      z-index: 2;
      margin: 0;
      font-size: 17px;
      line-height: 1.8;
    }


    /* =========================================================
   MISI
   ========================================================= */

    .misi-list {
      display: grid;
      gap: 12px;
      margin: 0;
      padding: 0;
      list-style: none;
      counter-reset: misi;
    }

    .misi-list li {
      position: relative;
      display: flex;
      gap: 15px;
      padding: 18px;
      border: 1px solid var(--border);
      border-radius: 14px;
      background: #fff;
    }

    .misi-list li::before {
      counter-increment: misi;
      content: counter(misi);
      flex: 0 0 34px;
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      background: var(--green-soft);
      color: var(--green);
      font-size: 13px;
      font-weight: 800;
    }


    /* =========================================================
   CPL
   ========================================================= */

    .cpl-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
    }

    .cpl-card {
      padding: 22px;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 16px;
      transition: .2s ease;
    }

    .cpl-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(0, 80, 65, .07);
    }

    .cpl-category {
      display: inline-block;
      margin-bottom: 10px;
      padding: 5px 10px;
      border-radius: 999px;
      background: var(--green-soft);
      color: var(--green);
      font-size: 11px;
      font-weight: 800;
    }

    .cpl-card p {
      margin: 0;
      color: var(--text-soft);
      font-size: 13px;
    }


    /* =========================================================
   KURIKULUM
   ========================================================= */

    .table-wrap {
      overflow-x: auto;
      border: 1px solid var(--border);
      border-radius: 16px;
      background: #fff;
    }

    .kurikulum-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 700px;
    }

    .kurikulum-table th {
      padding: 14px 16px;
      background: var(--green);
      color: #fff;
      text-align: left;
      font-size: 12px;
      font-weight: 700;
    }

    .kurikulum-table td {
      padding: 14px 16px;
      border-bottom: 1px solid #edf3f1;
      color: var(--text-soft);
      font-size: 13px;
    }

    .kurikulum-table tbody tr:hover {
      background: var(--green-light);
    }

    .kurikulum-table tbody tr:last-child td {
      border-bottom: 0;
    }


    /* =========================================================
   FASILITAS
   ========================================================= */

    .fasilitas-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .fasilitas-card {
      overflow: hidden;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 16px;
      transition: .2s ease;
    }

    .fasilitas-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0, 80, 65, .08);
    }

    .fasilitas-image {
      height: 170px;
      overflow: hidden;
      background: var(--green-soft);
    }

    .fasilitas-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .fasilitas-content {
      padding: 18px;
    }

    .fasilitas-content h3 {
      margin: 0 0 6px;
      font-size: 16px;
    }

    .fasilitas-content p {
      margin: 0;
      color: var(--text-soft);
      font-size: 12px;
    }


    /* =========================================================
   CTA
   ========================================================= */

    .prodi-cta {
      margin-top: 70px;
      padding: 35px;
      border-radius: 20px;
      background: var(--green-soft);
      border: 1px solid #d9eee8;
      text-align: center;
    }

    .prodi-cta h2 {
      margin: 0 0 8px;
      font-size: 24px;
    }

    .prodi-cta p {
      max-width: 650px;
      margin: 0 auto 20px;
      color: var(--text-soft);
      font-size: 13px;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 11px 20px;
      border-radius: 11px;
      background: var(--green);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      transition: .2s ease;
    }

    .btn-primary:hover {
      background: var(--green-dark);
      transform: translateY(-2px);
    }


    /* =========================================================
   RESPONSIVE - TABLET
   ========================================================= */

    @media (max-width: 1050px) {

      .prodi-profile {
        grid-template-columns: 230px minmax(0, 1fr);
      }

      .short-info {
        grid-column: 1 / -1;
      }

      .prodi-photo {
        height: 300px;
      }

      .fasilitas-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }


    /* =========================================================
   RESPONSIVE - MOBILE
   ========================================================= */

    @media (max-width: 700px) {

      .container {
        width: min(100% - 28px, 1180px);
      }

      .prodi-hero {
        padding: 28px 0 55px;
      }

      .breadcrumb {
        font-size: 11px;
        gap: 8px;
        margin-bottom: 20px;
      }

      .prodi-hero h1 {
        font-size: 38px;
      }

      .hero-description {
        font-size: 13px;
      }

      .prodi-main {
        margin-top: -25px;
      }

      .prodi-profile {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
        border-radius: 18px;
      }

      .prodi-photo {
        height: 300px;
      }

      .prodi-content {
        padding: 8px 5px;
      }

      .prodi-content h2 {
        font-size: 26px;
      }

      .prodi-info-grid {
        grid-template-columns: 1fr;
      }

      .short-info {
        grid-column: auto;
      }

      .prodi-section {
        padding-top: 48px;
      }

      .section-heading h2 {
        font-size: 24px;
      }

      .cpl-grid {
        grid-template-columns: 1fr;
      }

      .fasilitas-grid {
        grid-template-columns: 1fr;
      }

      .visi-box {
        padding: 24px;
      }

      .visi-box p {
        font-size: 14px;
      }

      .prodi-cta {
        padding: 28px 20px;
        margin-top: 50px;
      }
    }
    </style>
  </head>

  <body>

    <header class="top-nav">
      <div class="nav-inner">
        <a class="brand" href="../../index.php">
          <span class="brand-mark">F</span>
          <span><strong>FIKES</strong><small>FAKULTAS ILMU KESEHATAN</small></span>
        </a>

        <button class="menu-toggle" id="menuToggle">☰</button>

        <nav id="mainNav">
          <div class="nav-item has-dropdown">
            <button class="nav-link dropdown-btn">Tentang FIKES <span>⌄</span></button>
            <div class="dropdown">
              <a href="../tentang-fikes/visi-misi.php">Visi Misi</a>
              <a href="../tentang-fikes/struktur-organisasi.php">Struktur Organisasi</a>
              <a href="../tentang-fikes/sertifikat-akreditasi.php">Sertifikat Akreditasi</a>
              <a href="../tentang-fikes/unduh-logo.php">Unduh Logo</a>
            </div>
          </div>
          <div class="nav-item has-dropdown">
            <button class="nav-link dropdown-btn">Kemahasiswaan <span>⌄</span></button>
            <div class="dropdown">
              <a href="../kemahasiswaan/hima.php">Unit Himpunan Mahasiswa</a>
              <a href="../kemahasiswaan/ukm.php">UKM Kemahasiswaan</a>
            </div>
          </div>
          <div class="nav-item has-dropdown">
            <button class="nav-link dropdown-btn">Program Vokasi <span>⌄</span></button>
            <div class="dropdown">
              <a href="detail-prodi.php?id=6">Program Profesi Ners</a>
            </div>
          </div>
          <a class="nav-link" href="../akademik.php">Akademik</a>
          <a class="nav-link" href="../pelayanan-fikes.php">Pelayanan FIKES</a>
          <a class="nav-link" href="../survey.php">Survey</a>
        </nav>

        <a class="nav-cta" href="#daftar-prodi">Jelajahi Program <span>→</span></a>
      </div>
    </header>

    <section class="hero">
      <div class="hero-overlay"></div>
      <div class="container hero-content">
        <div class="breadcrumb">⌂ <span>Beranda</span><b>›</b><span>Program Studi</span></div>
        <h1>Program Studi</h1>
        <p>Kenali program pendidikan FIKES dan temukan program studi yang sesuai dengan minat serta tujuan karier Anda.
        </p>
      </div>
    </section>

    <main class="container page-content" id="daftar-prodi">
      <div class="section-heading">
        <span class="eyebrow">PENDIDIKAN FIKES</span>
        <h2>Program Studi</h2>
        <p>Pilih program studi untuk melihat informasi lengkap mengenai profil, kurikulum, capaian pembelajaran,
          fasilitas, dan informasi akademik lainnya.</p>
      </div>

      <form class="filter-bar" method="get">
        <div class="filter-tabs">
          <a class="<?= $filter === '' ? 'active' : '' ?>" href="program-studi.php">Semua</a>
          <?php foreach ($jenjangList as $j): ?>
          <a class="<?= $filter === $j ? 'active' : '' ?>" href="?jenjang=<?= urlencode($j) ?>"><?= e($j) ?></a>
          <?php endforeach; ?>
        </div>
        <div class="search-box">
          <span>⌕</span>
          <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari program studi...">
          <?php if ($filter !== ''): ?>
          <input type="hidden" name="jenjang" value="<?= e($filter) ?>">
          <?php endif; ?>
        </div>
      </form>

      <?php if (!$programs): ?>
      <div class="empty-state">Program studi yang Anda cari belum tersedia.</div>
      <?php else: ?>
      <div class="program-grid">
        <?php foreach ($programs as $p): ?>
        <?php
          $foto = !empty($p['gambar'])
            ? '../../admin/uploads/program-studi/' . rawurlencode($p['gambar'])
            : '';
          ?>
        <article class="program-card">
          <div class="program-image">
            <?php if ($foto): ?>
            <img src="<?= e($foto) ?>" alt="<?= e($p['nama']) ?>">
            <?php else: ?>
            <div class="image-placeholder"><span>F</span></div>
            <?php endif; ?>
            <span class="jenjang-badge"><?= e($p['jenjang']) ?></span>
          </div>
          <div class="program-body">
            <div class="code"><?= e($p['kode_prodi']) ?></div>
            <h3><?= e($p['nama']) ?></h3>
            <p><?= e($p['deskripsi']) ?></p>
            <div class="meta-row">
              <span>🎓 <?= e($p['gelar']) ?></span>
              <span>✓ <?= e($p['akreditasi']) ?></span>
            </div>
            <a class="detail-btn" href="detail-prodi.php?kode=<?= urlencode($p['kode_prodi']) ?>">Lihat Detail
              <span>→</span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </main>

    <section class="cta container">
      <div>
        <span class="eyebrow">BERSAMA FIKES</span>
        <h2>Bangun Masa Depan Bersama Kami</h2>
        <p>Temukan program studi yang mendukung perjalanan akademik dan profesional Anda.</p>
      </div>
      <a href="#daftar-prodi">Lihat Program Studi →</a>
    </section>

    <footer class="footer">
      <div class="container footer-grid">
        <div>
          <div class="footer-brand"><span>F</span>
            <div><strong>FIKES</strong><small>FAKULTAS ILMU KESEHATAN</small></div>
          </div>
          <p>Bersama membangun generasi sehat, cerdas dan berdaya saing.</p>
        </div>
        <div>
          <h4>Link Cepat</h4><a href="../tentang-fikes/visi-misi.php">Tentang FIKES</a><a
            href="program-studi.php">Program Studi</a><a href="../akademik.php">Akademik</a><a
            href="../kemahasiswaan/hima.php">Kemahasiswaan</a>
        </div>
        <div>
          <h4>Program Studi</h4><?php foreach (array_slice($prodiList, 0, 4) as $p): ?><a
            href="detail-prodi.php?id=<?= (int)$p['id'] ?>"><?= e($p['nama']) ?></a><?php endforeach; ?>
        </div>
        <div>
          <h4>Kontak Kami</h4>
          <p>📍 Jl. Pendidikan No. 1, Kota Sehat Indonesia</p>
          <p>☎ (021) 1234 5678</p>
          <p>✉ info@fikes.ac.id</p>
        </div>
      </div>
      <div class="footer-bottom">© <?= date('Y') ?> FIKES - Fakultas Ilmu Kesehatan. All rights reserved.</div>
    </footer>

    <script src="prodi.js"></script>
  </body>

</html>
