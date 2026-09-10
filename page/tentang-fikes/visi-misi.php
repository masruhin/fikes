<?php
$host = 'localhost';
$db   = 'fikes';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  die('Koneksi database gagal: ' . $e->getMessage());
}

$page_title = 'Visi & Misi';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $visi = trim($_POST['visi']);
  $s = $pdo->prepare("UPDATE visi_misi SET visi=? WHERE id=1");
  $s->execute([$visi]);
  $pdo->exec("TRUNCATE TABLE misi");
  $s = $pdo->prepare("INSERT INTO misi(nomor,judul,isi) VALUES(?,?,?)");
  foreach ($_POST['judul'] ?? [] as $i => $judul) if (trim($judul)) $s->execute([$i + 1, trim($judul), trim($_POST['isi'][$i])]);
  header('Location:visi-misi.php?saved=1');
  exit;
}
$visi = $pdo->query("SELECT * FROM visi_misi WHERE id=1")->fetch();
$misi = $pdo->query("SELECT * FROM misi ORDER BY nomor")->fetchAll();
function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= e($page_title ?? "FIKES - Fakultas Ilmu Kesehatan") ?> | FIKES</title>

    <meta name="description"
      content="Website resmi Fakultas Ilmu Kesehatan - Informasi akademik, program studi, kemahasiswaan, pelayanan dan informasi FIKES." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/visi-misi.css" />
  </head>

  <body>
    <!-- TOPBAR REUSABLE -->
    <?php require_once __DIR__ . '/../menu/topbar.php'; ?>

    <!-- NAVBAR REUSABLE -->
    <?php require_once __DIR__ . '/../menu/navbar.php'; ?>

    <!-- =========================================================
     HERO
========================================================= -->

    <main>
      <!-- ==========================================
     PREMIUM HERO SLIDER
========================================== -->
      <section class="page-hero">
        <div class="container">
          <div class="hero-content">
            <div class="breadcrumb">
              <a href="index.html"> Beranda </a>

              <span>›</span>

              <span> Tentang FIKES </span>

              <span>›</span>

              <span> Visi & Misi </span>
            </div>

            <div class="hero-label">
              <span></span>

              Tentang FIKES
            </div>

            <h1>
              Visi &

              <span>Misi FIKES</span>
            </h1>

            <p>
              <?= e($visi['visi']) ?>
            </p>
          </div>
        </div>
      </section>

      <!-- =====================================================
     VISI
===================================================== -->

      <section class="section visi-section">
        <div class="container visi-grid">
          <div class="visi-visual">
            <div class="visi-icon">👁</div>

            <div class="visi-visual-content">
              <small> Visi FIKES </small>

              <h3>Arah Masa Depan Fakultas Ilmu Kesehatan</h3>
            </div>
          </div>

          <div class="visi-content">
            <span class="section-label"> VISI </span>

            <h2>
              Menjadi Fakultas Ilmu Kesehatan yang unggul dan berdaya saing.
            </h2>

            <div class="visi-text">
              "Menjadi institusi pendidikan tinggi kesehatan yang unggul,
              profesional, inovatif, berintegritas, dan mampu memberikan
              kontribusi nyata bagi peningkatan derajat kesehatan masyarakat."
            </div>
          </div>
        </div>
      </section>

      <!-- =====================================================
     MISI
===================================================== -->

      <section class="section misi-section">
        <div class="container">

          <div class="misi-grid">
            <?php foreach ($misi as $i => $m): ?>
            <div class="misi-card">
              <div class="misi-number"><?= sprintf('%02d', $i + 1) ?></div>

              <div>
                <h3><?= $m['judul'] ?></h3>

                <p>
                  <?= e($m['isi']) ?>
                </p>
              </div>
            </div>
            <?php endforeach; ?>

          </div>
        </div>
      </section>

      <!-- =====================================================
     NILAI-NILAI
===================================================== -->

      <section class="section nilai-section">
        <div class="container">
          <div class="section-header">
            <span class="section-label"> NILAI UTAMA </span>

            <h2>Nilai yang Kami Junjung</h2>

            <p>
              Nilai-nilai berikut menjadi bagian penting dalam membangun budaya
              akademik dan profesional di lingkungan FIKES.
            </p>
          </div>

          <div class="nilai-grid">
            <div class="nilai-card">
              <div class="nilai-icon">⭐</div>

              <h3>Unggul</h3>

              <p>
                Berkomitmen menghasilkan kualitas terbaik dalam setiap bidang.
              </p>
            </div>

            <div class="nilai-card">
              <div class="nilai-icon">🤝</div>

              <h3>Integritas</h3>

              <p>Menjunjung tinggi kejujuran, etika, dan tanggung jawab.</p>
            </div>

            <div class="nilai-card">
              <div class="nilai-icon">💡</div>

              <h3>Inovatif</h3>

              <p>
                Mendorong kreativitas dan inovasi dalam menghadapi perubahan.
              </p>
            </div>

            <div class="nilai-card">
              <div class="nilai-icon">❤️</div>

              <h3>Peduli</h3>

              <p>Memberikan kontribusi nyata bagi masyarakat dan lingkungan.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- =====================================================
     CTA
===================================================== -->

      <section class="cta-section">
        <div class="container">
          <div class="cta">
            <div class="cta-content">
              <h2>Kenali FIKES Lebih Dekat</h2>

              <p>
                Jelajahi informasi mengenai struktur organisasi, program studi,
                tenaga pengajar, kemahasiswaan, dan berbagai layanan FIKES.
              </p>

              <a href="index.html#program" class="btn">
                Lihat Program Studi →
              </a>
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
