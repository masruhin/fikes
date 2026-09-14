<?php
require_once __DIR__ . '/../../admin/config/database.php';
function e($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
function tanggal_id($d)
{
  if (!$d) return '-';
  $b = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  $p = explode('-', substr($d, 0, 10));
  return count($p) === 3 ? (int)$p[2] . ' ' . $b[(int)$p[1]] . ' ' . $p[0] : e($d);
}
function file_icon($f)
{
  $x = strtolower(pathinfo($f, PATHINFO_EXTENSION));
  return strtoupper($x ?: 'FILE');
}
function prodi_key($nama)
{
  $nama = trim(mb_strtolower($nama, 'UTF-8'));
  $nama = preg_replace('/[^a-z0-9]+/u', '-', $nama);
  return trim($nama, '-');
}
$q = $pdo->query("SELECT s.*,p.id AS prodi_id,p.kode_prodi,p.nama AS nama_prodi,p.jenjang,p.gelar,p.akreditasi AS akreditasi_prodi FROM sertifikat_akreditasi s LEFT JOIN program_studi p ON (p.id=CAST(NULLIF(s.id_prodi,'') AS UNSIGNED) OR CONVERT(p.kode_prodi USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(s.id_prodi USING utf8mb4) COLLATE utf8mb4_unicode_ci OR CONVERT(p.nama USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(s.id_prodi USING utf8mb4) COLLATE utf8mb4_unicode_ci) WHERE s.status_aktif=1 ORDER BY p.nama ASC,s.tanggal_kadaluarsa DESC,s.id_sertifikat DESC");
$sertifikat = $q->fetchAll();
$prodi = $pdo->query("SELECT id,kode_prodi,nama,jenjang,gelar FROM program_studi WHERE status='aktif' ORDER BY nama ASC")->fetchAll();
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sertifikat Akreditasi | FIKES</title>
    <meta name="description" content="Sertifikat akreditasi Program Studi Fakultas Ilmu Kesehatan">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
      rel="stylesheet">
    <style>
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

    body {
      font-family: Inter, sans-serif;
      color: var(--text);
      background: #fff;
      line-height: 1.7
    }

    h1,
    h2,
    h3,
    h4 {
      font-family: 'Plus Jakarta Sans', sans-serif;
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

    .topbar-info span {
      display: flex;
      align-items: center;
      gap: 7px
    }

    .topbar-social a:hover {
      color: var(--secondary)
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
      font-size: 17px;
      box-shadow: 0 10px 25px rgba(8, 127, 91, .25)
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
      font-weight: 700;
      letter-spacing: .5px
    }

    .nav-menu {
      display: flex;
      align-items: center;
      gap: 3px
    }

    .nav-item {
      position: relative
    }

    .nav-link {
      min-height: 82px;
      padding: 0 13px;
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 13px;
      font-weight: 600;
      color: #344b43;
      white-space: nowrap
    }

    .nav-link:hover {
      color: var(--primary)
    }

    .arrow {
      font-size: 11px
    }

    .dropdown {
      position: absolute;
      top: calc(100% + 5px);
      left: 0;
      width: 250px;
      padding: 10px;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 14px;
      box-shadow: var(--shadow);
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: var(--transition)
    }

    .nav-item:hover>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: none
    }

    .dropdown-link {
      display: flex;
      justify-content: space-between;
      padding: 10px 13px;
      border-radius: 9px;
      color: #40544d;
      font-size: 13px
    }

    .dropdown-link:hover {
      color: var(--primary);
      background: var(--primary-light)
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

    .page-hero {
      padding: 55px 0 75px;
      background: radial-gradient(circle at 90% 10%, rgba(255, 255, 255, .15), transparent 28%), linear-gradient(120deg, #056044, #087f5b 55%, #69bca7);
      color: #fff
    }

    .breadcrumb {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      font-size: 13px;
      color: rgba(255, 255, 255, .8);
      margin-bottom: 25px
    }

    .breadcrumb a:hover {
      color: #fff
    }

    .hero-label {
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 1.5px;
      color: var(--secondary);
      margin-bottom: 10px
    }

    .page-hero h1 {
      font-size: clamp(38px, 5vw, 58px);
      color: #fff
    }

    .page-hero h1 span {
      color: var(--secondary)
    }

    .page-hero p {
      max-width: 720px;
      margin-top: 16px;
      color: rgba(255, 255, 255, .86)
    }

    .section {
      padding: 90px 0
    }

    .section-header {
      text-align: center;
      max-width: 720px;
      margin: 0 auto 42px
    }

    .section-label {
      font-size: 12px;
      color: var(--primary);
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

    .filter {
      display: flex;
      justify-content: center;
      gap: 9px;
      flex-wrap: wrap;
      margin-bottom: 35px
    }

    .filter button {
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text);
      padding: 10px 16px;
      border-radius: 999px;
      cursor: pointer;
      font-weight: 700
    }

    .filter button.active,
    .filter button:hover {
      background: var(--primary);
      border-color: var(--primary);
      color: #fff
    }

    .certificate-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px
    }

    .certificate-card {
      border: 1px solid var(--border);
      border-radius: 20px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 12px 35px rgba(18, 55, 42, .07);
      transition: .3s;
      display: flex;
      flex-direction: column
    }

    .certificate-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow)
    }

    .cert-head {
      height: 185px;
      background: linear-gradient(135deg, #e7f7f1, #f7faf9);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative
    }

    .cert-icon {
      width: 85px;
      height: 105px;
      border-radius: 8px;
      background: #fff;
      border: 1px solid #dce8e4;
      box-shadow: 0 10px 25px rgba(18, 55, 42, .1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 13px;
      font-weight: 800
    }

    .status {
      position: absolute;
      top: 14px;
      left: 14px;
      padding: 5px 10px;
      border-radius: 999px;
      background: var(--secondary);
      color: var(--dark);
      font-size: 10px;
      font-weight: 800
    }

    .cert-body {
      padding: 22px;
      display: flex;
      flex-direction: column;
      flex: 1
    }

    .cert-category {
      font-size: 10px;
      color: var(--primary);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .8px
    }

    .cert-body h3 {
      font-size: 19px;
      margin: 7px 0
    }

    .cert-body p {
      font-size: 12px;
      color: #6e7d77;
      margin-bottom: 16px
    }

    .meta {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 18px
    }

    .meta div {
      padding: 10px 12px;
      border-radius: 10px;
      background: var(--light);
      border: 1px solid var(--border)
    }

    .meta small {
      display: block;
      font-size: 9px;
      color: #83918c;
      text-transform: uppercase;
      font-weight: 800
    }

    .meta strong {
      font-size: 11px;
      color: var(--dark)
    }

    .cert-actions {
      display: flex;
      gap: 9px;
      margin-top: auto
    }

    .btn {
      flex: 1;
      padding: 11px 12px;
      border-radius: 10px;
      text-align: center;
      font-size: 11px;
      font-weight: 800;
      border: 1px solid var(--border)
    }

    .btn-primary {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary)
    }

    .btn:hover {
      transform: translateY(-1px)
    }

    .empty {
      text-align: center;
      padding: 40px;
      border: 1px dashed var(--border);
      border-radius: 18px
    }

    .info-section {
      background: var(--light)
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px
    }

    .info-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 25px
    }

    .info-icon {
      font-size: 28px;
      margin-bottom: 12px
    }

    .info-card h3 {
      font-size: 17px;
      margin-bottom: 8px
    }

    .info-card p {
      font-size: 13px
    }

    .cta-section {
      padding: 30px 0 90px;
      background: var(--light)
    }

    .cta {
      background: linear-gradient(120deg, var(--dark), #087f5b);
      border-radius: 24px;
      padding: 50px;
      color: #fff;
      text-align: center
    }

    .cta h2 {
      color: #fff;
      font-size: 30px
    }

    .cta p {
      max-width: 650px;
      margin: 12px auto 25px;
      color: rgba(255, 255, 255, .78);
      font-size: 13px
    }

    .cta a {
      display: inline-block;
      background: #fff;
      color: var(--dark);
      padding: 12px 18px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 800
    }

    footer {
      background: #0d2b21;
      color: #b7cec5
    }

    .footer-main {
      padding: 70px 0 45px;
      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr 1fr;
      gap: 45px
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

    .footer-links a:hover {
      color: var(--secondary)
    }

    .footer-location {
      grid-column: 1/-1
    }

    .location-header {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-bottom: 18px
    }

    .location-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: var(--secondary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--dark)
    }

    .location-header h3 {
      color: #fff;
      font-size: 13px
    }

    .location-header p {
      font-size: 9px;
      color: rgba(255, 255, 255, .5)
    }

    .map-card {
      position: relative;
      width: 100%;
      height: 210px;
      overflow: hidden;
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, .1);
      margin-bottom: 20px
    }

    .map-card iframe {
      width: 100%;
      height: 100%;
      border: 0
    }

    .map-overlay {
      position: absolute;
      left: 12px;
      right: 12px;
      bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px
    }

    .map-info {
      padding: 9px 11px;
      border-radius: 10px;
      background: rgba(5, 35, 26, .78);
      color: #fff;
      font-size: 10px
    }

    .map-direction {
      background: #fff;
      color: var(--dark);
      padding: 10px 12px;
      border-radius: 9px;
      font-size: 9px;
      font-weight: 800
    }

    .footer-contact {
      display: flex;
      gap: 9px;
      align-items: center;
      font-size: 11px;
      margin-top: 10px
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, .08);
      padding: 20px 0;
      display: flex;
      justify-content: space-between;
      font-size: 11px
    }

    @media(max-width:1000px) {
      .nav-menu {
        display: none
      }

      .menu-toggle {
        display: block;
        border: 0;
        background: var(--primary);
        color: #fff;
        border-radius: 10px;
        padding: 10px 13px
      }

      .certificate-grid {
        grid-template-columns: repeat(2, 1fr)
      }
    }

    @media(max-width:650px) {
      .topbar {
        display: none
      }

      .nav-cta {
        display: none
      }

      .certificate-grid,
      .info-grid,
      .footer-main {
        grid-template-columns: 1fr
      }

      .nav-inner {
        min-height: 72px
      }

      .footer-location {
        grid-column: auto
      }

      .footer-bottom {
        flex-direction: column;
        gap: 8px
      }

      .map-overlay {
        flex-direction: column;
        align-items: stretch
      }

      .map-direction {
        width: 100%;
        text-align: center
      }
    }
    </style>
  </head>

  <body>
    <?php require_once __DIR__ . '/../menu/topbar.php'; ?>

    <!-- NAVBAR REUSABLE -->
    <?php require_once __DIR__ . '/../menu/navbar.php'; ?>
    <main>
      <section class="page-hero">
        <div class="container">
          <div class="breadcrumb"><a href="../../index.php">Beranda</a><span>›</span><span>Tentang
              FIKES</span><span>›</span><span>Sertifikat Akreditasi</span></div>
          <div class="hero-label">MUTU & AKREDITASI</div>
          <h1>Sertifikat <span>Akreditasi</span></h1>
          <p>Informasi dan dokumen sertifikat akreditasi yang terhubung langsung dengan data Program Studi FIKES.</p>
        </div>
      </section>
      <section class="section">
        <div class="container">
          <div class="section-header"><span class="section-label">DOKUMEN AKREDITASI</span>
            <h2 class="section-title">Sertifikat Berdasarkan Program Studi</h2>
            <p class="section-description">Setiap kartu di bawah mengambil nama, jenjang, dan gelar dari tabel
              <strong>program_studi</strong>.
            </p>
          </div>
          <div class="filter"><button class="active"
              data-filter="all">Semua</button><?php foreach ($prodi as $p): ?><button
              data-filter="prodi-<?= e(prodi_key($p['nama'])) ?>"><?= e($p['nama']) ?></button><?php endforeach; ?>
          </div>
          <div class="certificate-grid" id="certificateGrid"><?php if (!$sertifikat): ?><div class="empty"
              style="grid-column:1/-1">
              <h3>Belum ada sertifikat aktif</h3>
              <p>Tambahkan sertifikat dari halaman admin.</p>
            </div><?php else: foreach ($sertifikat as $s): ?><article class="certificate-card"
              data-prodi="prodi-<?= e(prodi_key($s['nama_prodi'] ?? '')) ?>">
              <div class="cert-head"><span class="status">TERAKREDITASI</span>
                <div class="cert-icon"><?= e(file_icon($s['file_sertifikat'] ?? '')) ?></div>
              </div>
              <div class="cert-body">
                <div class="cert-category"><?= e($s['jenjang'] ?? 'Program Studi') ?></div>
                <h3><?= e($s['nama_prodi'] ?? 'Program Studi tidak ditemukan') ?></h3>
                <p>
                  <?= e(($s['gelar'] ? 'Gelar ' . $s['gelar'] . '. ' : '') . 'Dokumen sertifikat akreditasi Program Studi.') ?>
                </p>
                <div class="meta">
                  <div><small>Peringkat</small><strong><?= e($s['peringkat'] ?? '-') ?></strong></div>
                  <div><small>Status</small><strong>Aktif</strong></div>
                  <div><small>Nomor SK</small><strong><?= e($s['nomor_sk'] ?? '-') ?></strong></div>
                  <div><small>Berlaku</small><strong><?= e(tanggal_id($s['tanggal_kadaluarsa'] ?? '')) ?></strong></div>
                </div>
                <div class="cert-actions"><?php if (!empty($s['file_sertifikat'])): ?><a class="btn btn-primary"
                    href="../../admin/uploads/upload-sertifikat/<?= rawurlencode(basename($s['file_sertifikat'])) ?>"
                    target="_blank">Lihat Dokumen</a><a class="btn"
                    href="../../admin/uploads/upload-sertifikat/<?= rawurlencode(basename($s['file_sertifikat'])) ?>"
                    download>Download</a><?php endif; ?></div>
              </div>
            </article><?php endforeach;
                                                            endif; ?></div>
        </div>
      </section>
      <section class="section info-section">
        <div class="container">
          <div class="section-header"><span class="section-label">INFORMASI</span>
            <h2 class="section-title">Tentang Akreditasi</h2>
            <p class="section-description">Data sertifikat sekarang dapat mengikuti perubahan Program Studi dari
              database tanpa mengubah HTML kartu satu per satu.</p>
          </div>
          <div class="info-grid">
            <div class="info-card">
              <div class="info-icon">🎓</div>
              <h3>Terhubung Program Studi</h3>
              <p>Nama program, kode, jenjang, dan gelar diambil dari tabel program_studi.</p>
            </div>
            <div class="info-card">
              <div class="info-icon">📄</div>
              <h3>Dokumen Resmi</h3>
              <p>Dokumen yang diunggah admin dapat dibuka dan diunduh langsung dari halaman publik.</p>
            </div>
            <div class="info-card">
              <div class="info-icon">🔎</div>
              <h3>Data Lebih Konsisten</h3>
              <p>Perubahan nama Program Studi di database otomatis tercermin pada sertifikat.</p>
            </div>
          </div>
        </div>
      </section>
    </main>
    <footer>
      <div class="container footer-main">
        <div>
          <h4 class="footer-title">FIKES</h4>
          <p style="font-size:12px;max-width:320px">Membangun generasi kesehatan yang profesional, berintegritas,
            inovatif, dan berorientasi kepada masyarakat.</p>
        </div>
        <div>
          <h4 class="footer-title">Tentang FIKES</h4>
          <div class="footer-links"><a href="visi-misi.php">Visi Misi</a><a href="struktur-organisasi.php">Struktur
              Organisasi</a><a href="sertifikat-akreditasi.php">Akreditasi</a></div>
        </div>
        <div>
          <h4 class="footer-title">Program Studi</h4>
          <div class="footer-links"><a href="../program-studi/program-studi.php">Daftar Program Studi</a></div>
        </div>
        <div>
          <h4 class="footer-title">Informasi</h4>
          <div class="footer-links"><a href="#">Akademik</a><a href="#">Kemahasiswaan</a><a href="#">Survey</a></div>
        </div>
        <div class="footer-location">
          <div class="location-header">
            <div class="location-icon">📍</div>
            <div>
              <h3>Lokasi Kampus</h3>
              <p>Fakultas Ilmu Kesehatan</p>
            </div>
          </div>
          <div class="map-card"><iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626"
              loading="lazy" title="Lokasi Fakultas Ilmu Kesehatan"></iframe>
            <div class="map-overlay">
              <div class="map-info"><strong>Fakultas Ilmu Kesehatan</strong><br><span>Lihat lokasi kampus</span></div><a
                class="map-direction" href="https://www.google.com/maps" target="_blank">Petunjuk Arah</a>
            </div>
          </div>
          <div class="footer-contact">📍 Alamat Fakultas Ilmu Kesehatan</div>
          <div class="footer-contact">☎ (021) 1234567</div>
          <div class="footer-contact">✉ info@fikes.ac.id</div>
        </div>
      </div>
      <div class="container footer-bottom"><span>© <span id="year"></span> Fakultas Ilmu Kesehatan. All Rights
          Reserved.</span><span>Website FIKES</span></div>
    </footer>
    <script>
    (function() {
      const buttons = document.querySelectorAll('.filter button');
      const cards = document.querySelectorAll('.certificate-card');

      function applyFilter(filter) {
        let visible = 0;
        cards.forEach(card => {
          const show = filter === 'all' || card.dataset.prodi === filter;
          card.style.display = show ? 'flex' : 'none';
          if (show) visible++;
        });
        let empty = document.getElementById('filterEmpty');
        if (!empty) {
          empty = document.createElement('div');
          empty.id = 'filterEmpty';
          empty.className = 'empty';
          empty.style.gridColumn = '1/-1';
          empty.innerHTML =
            '<h3>Belum ada sertifikat</h3><p>Belum ada sertifikat aktif untuk program studi yang dipilih.</p>';
          document.getElementById('certificateGrid').appendChild(empty);
        }
        empty.style.display = visible ? 'none' : 'block';
      }
      buttons.forEach(button => button.addEventListener('click', function() {
        buttons.forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        applyFilter(this.dataset.filter);
      }));
      applyFilter('all');
    })();
    document.getElementById('year').textContent = new Date().getFullYear();
    </script>
  </body>

</html>
