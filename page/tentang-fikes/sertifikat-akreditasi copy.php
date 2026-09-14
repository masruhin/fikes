<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'fikes');
if (!$koneksi) die('Koneksi database gagal: ' . mysqli_connect_error());
mysqli_set_charset($koneksi, 'utf8mb4');
$folder_upload = '../../admin/uploads/upload-sertifikat/';
function aman($x)
{
  return htmlspecialchars($x ?? '', ENT_QUOTES, 'UTF-8');
}
function tanggal_id($x)
{
  if (!$x || $x === '0000-00-00') return '-';
  $b = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  $p = explode('-', $x);
  return count($p) == 3 ? (int)$p[2] . ' ' . $b[(int)$p[1]] . ' ' . $p[0] : $x;
}
function ext_file($x)
{
  return strtolower(pathinfo($x, PATHINFO_EXTENSION));
}
function file_label($x)
{
  $e = ext_file($x);
  return $e === 'pdf' ? 'PDF' : (in_array($e, ['jpg', 'jpeg']) ? 'JPG' : (in_array($e, ['xls', 'xlsx']) ? 'XLS' : (in_array($e, ['doc', 'docx']) ? 'DOC' : (in_array($e, ['ppt', 'pptx']) ? 'PPT' : 'FILE'))));
}
$sql = "SELECT s.*,p.nama nama_prodi,p.jenjang jenjang_prodi,p.gelar gelar_prodi FROM sertifikat_akreditasi s LEFT JOIN program_studi p ON p.id=s.id_prodi WHERE s.status_aktif=1 ORDER BY s.tanggal_kadaluarsa DESC,s.id_sertifikat DESC";
$q = mysqli_query($koneksi, $sql);
if (!$q) die('Query gagal: ' . mysqli_error($koneksi));
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Sertifikat Akreditasi | FIKES</title>
  <meta name="description" content="Sertifikat Akreditasi Fakultas Ilmu Kesehatan">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/visi-misi.css" />

  <style>
    :root {
      --primary: #087f5b;
      --primary-dark: #056044;
      --primary-light: #e7f7f1;
      --secondary: #f4b942;
      --dark: #12372a;
      --text: #52635d;
      --white: #fff;
      --light: #f7faf9;
      --border: #e5ece9;
      --shadow-hover: 0 25px 70px rgba(18, 55, 42, .16);
      --transition: .3s ease
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box
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

    .page-hero {
      position: relative;
      padding: 90px 0 80px;
      background: radial-gradient(circle at 90% 15%, rgba(8, 127, 91, .1), transparent 28%), linear-gradient(180deg, #f8fcfa, #fff);
      overflow: hidden
    }

    .hero-content {
      position: relative;
      z-index: 2
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 9px;
      margin-bottom: 25px;
      font-size: 13px
    }

    .breadcrumb a {
      color: var(--primary);
      font-weight: 600
    }

    .breadcrumb span {
      color: #9aa9a3
    }

    .hero-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 15px;
      border-radius: 50px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 12px;
      font-weight: 700;
      margin-bottom: 18px
    }

    .hero-label span {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--secondary)
    }

    .page-hero h1 {
      font-size: clamp(40px, 5vw, 62px);
      letter-spacing: -1.5px;
      margin-bottom: 18px
    }

    .page-hero h1 span {
      color: var(--primary)
    }

    .page-hero p {
      max-width: 720px;
      font-size: 17px
    }

    .section {
      padding: 95px 0
    }

    .section-header {
      max-width: 720px;
      margin: 0 auto 50px;
      text-align: center
    }

    .section-label {
      display: inline-flex;
      padding: 8px 15px;
      border-radius: 50px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 12px;
      font-weight: 700;
      margin-bottom: 15px
    }

    .section-title {
      font-size: clamp(30px, 4vw, 45px);
      margin-bottom: 15px
    }

    .section-description {
      font-size: 15px
    }

    .filter-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 40px
    }

    .filter-button {
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text);
      padding: 10px 18px;
      border-radius: 50px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 700;
      transition: var(--transition)
    }

    .filter-button:hover {
      border-color: var(--primary);
      color: var(--primary)
    }

    .filter-button.active {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
      box-shadow: 0 8px 20px rgba(8, 127, 91, .18)
    }

    .certificate-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px
    }

    .certificate-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 35px rgba(18, 55, 42, .05);
      transition: var(--transition)
    }

    .certificate-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
      border-color: transparent
    }

    .certificate-preview {
      position: relative;
      height: 270px;
      overflow: hidden;
      background: #f1f5f3;
      cursor: zoom-in
    }

    .certificate-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .5s ease
    }

    .certificate-card:hover .certificate-preview img {
      transform: scale(1.05)
    }

    .preview-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(8, 127, 91, .65);
      opacity: 0;
      transition: var(--transition)
    }

    .certificate-card:hover .preview-overlay {
      opacity: 1
    }

    .preview-button {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: #fff;
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, .15)
    }

    .certificate-status {
      position: absolute;
      top: 15px;
      left: 15px;
      z-index: 2;
      padding: 6px 11px;
      border-radius: 50px;
      background: rgba(255, 255, 255, .94);
      color: var(--primary);
      font-size: 10px;
      font-weight: 800;
      box-shadow: 0 5px 15px rgba(0, 0, 0, .08)
    }

    .document-placeholder {
      width: 100%;
      height: 100%;
      padding: 25px;
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      background: linear-gradient(135deg, #fdfefe, #eaf2ef);
      overflow: hidden
    }

    .document-header {
      font-size: 9px;
      font-weight: 800;
      letter-spacing: 1px;
      color: #60736d
    }

    .document-seal {
      width: 65px;
      height: 65px;
      margin: 15px 0;
      border: 3px solid var(--primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 13px;
      font-weight: 900
    }

    .document-main-title {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 17px;
      font-weight: 800;
      color: var(--dark);
      line-height: 1.3
    }

    .document-prodi {
      margin-top: 8px;
      color: #5d706a;
      font-size: 10px;
      font-weight: 600
    }

    .document-type {
      position: absolute;
      right: 13px;
      bottom: 12px;
      padding: 6px 9px;
      border-radius: 7px;
      background: var(--primary);
      color: #fff;
      font-size: 9px;
      font-weight: 800
    }

    .certificate-content {
      padding: 23px
    }

    .certificate-category {
      color: var(--primary);
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .8px;
      margin-bottom: 8px
    }

    .certificate-content h3 {
      font-size: 18px;
      margin-bottom: 8px
    }

    .certificate-content p {
      min-height: 42px;
      font-size: 12px;
      color: #75857f;
      margin-bottom: 18px
    }

    .certificate-meta {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 20px;
      padding: 14px;
      border-radius: 12px;
      background: var(--light)
    }

    .meta-item {
      display: flex;
      flex-direction: column;
      min-width: 0
    }

    .meta-full {
      grid-column: 1/-1
    }

    .meta-label {
      font-size: 9px;
      color: #91a099;
      text-transform: uppercase;
      font-weight: 700
    }

    .meta-value {
      font-size: 11px;
      color: var(--dark);
      font-weight: 700;
      line-height: 1.45;
      overflow-wrap: anywhere
    }

    .meta-value.accent {
      color: var(--primary)
    }

    .card-buttons {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 9px
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 42px;
      padding: 8px;
      border-radius: 10px;
      font-size: 11px;
      font-weight: 800;
      transition: var(--transition);
      text-align: center
    }

    .btn-preview {
      border: 1px solid var(--primary);
      color: var(--primary);
      background: #fff
    }

    .btn-preview:hover {
      background: var(--primary);
      color: #fff
    }

    .btn-download {
      background: var(--primary);
      color: #fff;
      border: 1px solid var(--primary)
    }

    .btn-download:hover {
      background: var(--primary-dark);
      transform: translateY(-2px)
    }

    .btn-disabled {
      grid-column: 1/-1;
      background: #f0f3f2;
      color: #8a9691
    }

    .no-result {
      display: none;
      text-align: center;
      padding: 50px 20px;
      border: 1px dashed var(--border);
      border-radius: 20px;
      margin-top: 20px
    }

    .no-result-icon {
      font-size: 35px;
      margin-bottom: 10px
    }

    .info-section {
      background: #f7faf9
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px
    }

    .info-card {
      padding: 30px;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 18px;
      box-shadow: 0 10px 30px rgba(18, 55, 42, .04)
    }

    .info-icon {
      width: 48px;
      height: 48px;
      margin-bottom: 18px;
      border-radius: 14px;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 21px
    }

    .info-card h3 {
      margin-bottom: 10px;
      font-size: 17px
    }

    .info-card p {
      font-size: 12px;
      color: #75857f
    }

    .back-top {
      position: fixed;
      right: 25px;
      bottom: 25px;
      width: 46px;
      height: 46px;
      border: 0;
      border-radius: 14px;
      background: var(--primary);
      color: #fff;
      font-size: 20px;
      cursor: pointer;
      box-shadow: 0 10px 25px rgba(8, 127, 91, .25);
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: var(--transition);
      z-index: 50
    }

    .back-top.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0)
    }

    @media(max-width:1000px) {
      .certificate-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .info-grid {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:650px) {
      .container {
        width: min(100% - 28px, 1180px)
      }

      .page-hero {
        padding: 60px 0 70px
      }

      .page-hero h1 {
        font-size: 40px;
        letter-spacing: -1px
      }

      .page-hero p {
        font-size: 15px
      }

      .section {
        padding: 70px 0
      }

      .certificate-grid {
        grid-template-columns: 1fr
      }

      .info-grid {
        grid-template-columns: 1fr
      }
    }

    @media(max-width:460px) {
      .certificate-meta {
        grid-template-columns: 1fr
      }

      .meta-full {
        grid-column: auto
      }

      .card-buttons {
        grid-template-columns: 1fr
      }

      .btn-disabled {
        grid-column: auto
      }

      .filter-wrapper {
        justify-content: flex-start
      }
    }
  </style>
</head>

<body>
  <!-- TOPBAR REUSABLE -->
  <?php require_once __DIR__ . '/../menu/topbar.php'; ?>

  <!-- NAVBAR REUSABLE -->
  <?php require_once __DIR__ . '/../menu/navbar.php'; ?>
  <main>
    <section class="page-hero">
      <div class="container">
        <div class="hero-content">
          <div class="breadcrumb">
            <a href="../index.php">Beranda</a>
            <span>›</span>
            <span>Tentang FIKES</span>
            <span>›</span><span>Sertifikat Akreditasi</span>
          </div>
          <div class="hero-label"><span></span>MUTU &amp;
            AKREDITASI</div>
          <h1>Sertifikat <span>Akreditasi</span></h1>
          <p>Informasi dan dokumen sertifikat akreditasi program studi Fakultas Ilmu Kesehatan sebagai bentuk komitmen
            terhadap mutu pendidikan dan penyelenggaraan akademik.</p>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="container">
        <div class="section-header"><span class="section-label">DOKUMEN AKREDITASI</span>
          <h2 class="section-title">Sertifikat Akreditasi FIKES</h2>
          <p class="section-description">Silakan pilih program studi untuk melihat sertifikat akreditasi dan mengunduh
            dokumen.</p>
        </div>
        <div class="filter-wrapper">
          <button class="filter-button active" data-filter="all">Semua</button>
          <button class="filter-button" data-filter="keperawatan">Keperawatan</button>
          <button class="filter-button" data-filter="kebidanan">Kebidanan</button>
          <button class="filter-button" data-filter="farmasi">Farmasi</button><button class="filter-button"
            data-filter="k3">K3</button>
        </div>
        <div class="certificate-grid" id="certificateGrid">
          <?php while ($r = mysqli_fetch_assoc($q)): $nama = $r['nama_prodi'] ?: 'Program Studi ' . $r['id_prodi'];
            $s = strtolower($nama);
            $cat = strpos($s, 'keperawatan') !== false ? 'keperawatan' : (strpos($s, 'kebidanan') !== false ? 'kebidanan' : (strpos($s, 'farmasi') !== false ? 'farmasi' : ((strpos($s, 'k3') !== false || strpos($s, 'keselamatan') !== false) ? 'k3' : 'lainnya')));
            $file = $r['file_sertifikat'];
            $url = $file ? $folder_upload . rawurlencode($file) : '';
            $ext = ext_file($file);
            $jenjang = $r['jenjang_prodi'] ?: 'Program Studi'; ?>
            <article class="certificate-card" data-category="<?= aman($cat) ?>">
              <div class="certificate-preview"><span class="certificate-status">✓
                  TERAKREDITASI</span>
                <?php if ($file && in_array($ext, ['jpg', 'jpeg'])): ?>
                  <img src="<?= aman($url) ?>" alt="Sertifikat Akreditasi <?= aman($nama) ?>"><?php else: ?><div
                    class="document-placeholder">
                    <div class="document-header">FAKULTAS ILMU KESEHATAN</div>
                    <div class="document-seal">FIKES</div>
                    <div class="document-main-title">SERTIFIKAT<br>AKREDITASI</div>
                    <div class="document-prodi"><?= aman($nama) ?></div>
                    <div class="document-type"><?= file_label($file) ?></div>
                  </div>
                <?php endif; ?>
                <?php if ($url): ?>
                  <a class="preview-overlay" href="<?= aman($url) ?>" target="_blank">
                    <div class="preview-button">🔍</div>
                  </a>
                <?php endif; ?>
              </div>
              <div class="certificate-content">
                <div class="certificate-category"><?= aman($jenjang) ?></div>
                <h3><?= aman($nama) ?></h3>
                <p>Sertifikat akreditasi <?= aman($nama) ?>.</p>
                <div class="certificate-meta">
                  <div class="meta-item">
                    <span class="meta-label">ID Sertifikat</span>
                    <span class="meta-value">#<?= aman($r['id_sertifikat']) ?></span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">ID Prodi</span>
                    <span class="meta-value"><?= aman($r['id_prodi']) ?></span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">ID Institusi</span><span class="meta-value"><?= aman($r['id_institusi']) ?>
                    </span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">ID Lembaga</span>
                    <span class="meta-value"><?= aman($r['id_lembaga']) ?></span>
                  </div>
                  <div class="meta-item meta-full">
                    <span class="meta-label">Nomor SK</span><span class="meta-value"><?= aman($r['nomor_sk']) ?></span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">Peringkat</span>
                    <span class="meta-value accent"><?= aman($r['peringkat']) ?></span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">Status Aktif</span>
                    <span class="meta-value accent">Aktif</span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">Tanggal SK</span><span
                      class="meta-value"><?= aman(tanggal_id($r['tanggal_sk'])) ?>
                    </span>
                  </div>
                  <div class="meta-item">
                    <span class="meta-label">Kadaluarsa</span>
                    <span class="meta-value"><?= aman(tanggal_id($r['tanggal_kadaluarsa'])) ?></span>
                  </div>
                </div>
                <div class="card-buttons">
                  <?php if ($url): ?>
                    <a href="<?= aman($url) ?>" target="_blank" class="btn btn-preview">👁 Lihat
                      <?= file_label($file) ?></a>
                    <a href="<?= aman($url) ?>" download class="btn btn-download">↓
                      Download</a>
                  <?php else: ?>
                    <span class="btn btn-disabled">File belum
                      tersedia</span>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endwhile; ?>
        </div>
        <div class="no-result" id="noResult">
          <div class="no-result-icon">📄</div>
          <h3>Dokumen belum tersedia</h3>
          <p>Belum terdapat dokumen untuk kategori yang dipilih.</p>
        </div>
      </div>
    </section>
    <section class="section info-section">
      <div class="container">
        <div class="section-header"><span class="section-label">INFORMASI</span>
          <h2 class="section-title">Tentang Akreditasi</h2>
          <p class="section-description">Akreditasi merupakan bagian penting dalam memastikan penyelenggaraan
            pendidikan memenuhi standar mutu yang telah ditetapkan.</p>
        </div>
        <div class="info-grid">
          <div class="info-card">
            <div class="info-icon">🏆</div>
            <h3>Penjaminan Mutu</h3>
            <p>Akreditasi menjadi salah satu instrumen penjaminan mutu penyelenggaraan pendidikan pada setiap program
              studi.</p>
          </div>
          <div class="info-card">
            <div class="info-icon">✓</div>
            <h3>Standar Pendidikan</h3>
            <p>Sertifikat menunjukkan program studi telah memenuhi standar penilaian lembaga akreditasi.</p>
          </div>
          <div class="info-card">
            <div class="info-icon">📑</div>
            <h3>Dokumen Resmi</h3>
            <p>Dokumen akreditasi dapat dilihat dan diunduh sebagai informasi publik FIKES.</p>
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
  <script src="../assets/js/visi-misi.js"></script>

  </script>

</body>

</html>
<?php mysqli_close($koneksi); ?>
