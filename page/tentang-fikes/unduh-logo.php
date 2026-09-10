<?php
$dir = __DIR__ . "/../../admin/uploads/upload-logo/";
$url = "../../admin/uploads/upload-logo/";
$allowed = ["jpg", "jpeg", "png", "gif", "svg", "webp", "ico", "bmp"];
$logos = [];

if (is_dir($dir)) {
  foreach (scandir($dir) as $f) {
    if ($f != "." && $f != ".." && is_file($dir . $f)) {
      $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
      if (in_array($ext, $allowed)) $logos[] = $f;
    }
  }
}
rsort($logos);

function e($s)
{
  return htmlspecialchars($s ?? "", ENT_QUOTES, "UTF-8");
}
function fmt($f)
{
  $x = strtolower(pathinfo($f, PATHINFO_EXTENSION));
  return $x == "jpeg" ? "JPEG" : strtoupper($x);
}
function desc($x)
{
  if ($x == "svg") return "Format vector yang dapat diperbesar tanpa kehilangan kualitas.";
  if ($x == "png") return "Format PNG berkualitas tinggi untuk website dan publikasi.";
  if ($x == "jpg" || $x == "jpeg") return "Format gambar praktis untuk website dan publikasi.";
  if ($x == "webp") return "Format gambar modern dengan ukuran file lebih ringan.";
  if ($x == "ico") return "Format ikon untuk favicon dan kebutuhan identitas website.";
  return "File logo resmi FIKES untuk kebutuhan publikasi.";
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Unduh Logo | FIKES</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/unduh.css" />
  <!-- <link rel="stylesheet" href="../assets/css/unduh.css" /> -->


</head>

<body>

  <?php
  if (file_exists(__DIR__ . "/../menu/topbar.php")) require_once __DIR__ . "/../menu/topbar.php";
  if (file_exists(__DIR__ . "/../menu/navbar.php")) require_once __DIR__ . "/../menu/navbar.php";
  ?>

  <main>
    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <div class="breadcrumb">
            <a href="../index.php">Beranda</a><span>›</span><span>Tentang FIKES</span><span>›</span><span>Unduh
              Logo</span>
          </div>
          <div class="hero-label"><span class="hero-dot"></span> IDENTITAS VISUAL</div>
          <h1>Unduh <span>Logo FIKES</span></h1>
          <p>Download logo resmi Fakultas Ilmu Kesehatan dalam berbagai format untuk kebutuhan website, dokumen,
            desain, dan publikasi.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-label">LOGO RESMI</span>
          <h2 class="section-title">Logo FIKES</h2>
          <p class="section-description">File logo pada halaman ini berasal langsung dari folder upload logo pada
            sistem administrasi FIKES.</p>
        </div>

        <?php if ($logos): ?>
          <div class="logo-grid">
            <?php foreach ($logos as $logo):
              $ext = strtolower(pathinfo($logo, PATHINFO_EXTENSION));
              $fileUrl = $url . rawurlencode($logo);
            ?>
              <article class="logo-card">
                <span class="format-badge"><?= e(fmt($logo)) ?></span>
                <div class="logo-preview" onclick="previewLogo('<?= e($fileUrl) ?>','<?= e($logo) ?>')">
                  <img src="<?= e($fileUrl) ?>" alt="<?= e($logo) ?>" loading="lazy">
                </div>
                <div class="logo-content">
                  <h3>Logo FIKES <?= e(fmt($logo)) ?></h3>
                  <p><?= e(desc($ext)) ?></p>
                  <div class="logo-meta">
                    <span class="meta"><?= e(fmt($logo)) ?></span>
                    <span class="meta">Logo Resmi</span>
                    <span class="meta">FIKES</span>
                  </div>
                  <div class="card-buttons">
                    <button type="button" class="btn btn-preview"
                      onclick="previewLogo('<?= e($fileUrl) ?>','<?= e($logo) ?>')">👁 Preview</button>
                    <a href="<?= e($fileUrl) ?>" download="<?= e($logo) ?>" class="btn btn-download">↓ Download</a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <div class="empty-icon">▧</div>
            <h3>Logo belum tersedia</h3>
            <p>Belum ada file logo yang diupload melalui halaman administrasi FIKES.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="section download-section">
      <div class="container">
        <div class="download-box">
          <div class="download-box-content">
            <div class="download-icon">📦</div>
            <h2>Butuh Semua File Logo?</h2>
            <p>Download seluruh logo FIKES yang tersedia dalam satu file ZIP.</p>
            <?php if ($logos): ?><a href="download-logo-all.php" class="download-all">📦 Download Semua Logo</a>
            <?php else: ?><span class="download-disabled">Belum ada file logo</span><?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="section info-section">
      <div class="container">
        <div class="section-header">
          <span class="section-label">INFORMASI</span>
          <h2 class="section-title">Penggunaan Logo</h2>
          <p class="section-description">Gunakan logo FIKES secara proporsional dan tetap menjaga identitas visual
            institusi.</p>
        </div>
        <div class="info-grid">
          <div class="info-card">
            <div class="info-icon">✓</div>
            <h3>Logo Resmi</h3>
            <p>Gunakan file yang tersedia sebagai sumber logo resmi FIKES.</p>
          </div>
          <div class="info-card">
            <div class="info-icon">▣</div>
            <h3>Pilih Format</h3>
            <p>PNG cocok untuk website, sedangkan SVG cocok untuk desain.</p>
          </div>
          <div class="info-card">
            <div class="info-icon">↗</div>
            <h3>Kualitas</h3>
            <p>Hindari mengubah logo secara tidak proporsional.</p>
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
  <!-- =========================================================
     LIGHTBOX
========================================================= -->

  <div class="lightbox" id="lightbox">
    <div class="lightbox-content">
      <button type="button" class="lightbox-close" id="lightboxClose">
        ✕
      </button>

      <img src="" alt="" id="lightboxImage" class="lightbox-image" />
    </div>
  </div>

  <!-- <script src="../assets/js/unduh.js"></script> -->
  <script>
    /* =========================================================
   NAVBAR SCROLL
========================================================= */

    const navbar = document.getElementById("navbar");

    window.addEventListener("scroll", () => {
      if (window.scrollY > 20) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });

    /* =========================================================
   MOBILE MENU
========================================================= */

    const menuToggle = document.getElementById("menuToggle");

    const navMenu = document.getElementById("navMenu");

    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active");

      menuToggle.innerHTML = navMenu.classList.contains("active") ?
        "✕" :
        "☰";
    });

    /* =========================================================
   MOBILE DROPDOWN
========================================================= */

    document
      .querySelectorAll(
        ".has-dropdown > .nav-link, " + ".has-dropdown > .dropdown-link",
      )
      .forEach((link) => {
        link.addEventListener("click", function(event) {
          if (window.innerWidth <= 900) {
            event.preventDefault();

            this.parentElement.classList.toggle("open");
          }
        });
      });

    /* =========================================================
   LIGHTBOX
========================================================= */

    const lightbox = document.getElementById("lightbox");

    const lightboxImage = document.getElementById("lightboxImage");

    const lightboxClose = document.getElementById("lightboxClose");

    function previewLogo(image, title) {
      lightboxImage.src = image;

      lightboxImage.alt = title;

      lightbox.classList.add("active");

      document.body.style.overflow = "hidden";
    }

    function closeLightbox() {
      lightbox.classList.remove("active");

      lightboxImage.src = "";

      document.body.style.overflow = "";
    }

    lightboxClose.addEventListener("click", closeLightbox);

    lightbox.addEventListener("click", (event) => {
      if (event.target === lightbox) {
        closeLightbox();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeLightbox();
      }
    });

    /* =========================================================
   YEAR
========================================================= */

    document.getElementById("year").textContent = new Date().getFullYear();
  </script>

</body>

</html>
