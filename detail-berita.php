<?php
require_once __DIR__ . '/admin/config/database.php';
function e($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
$slug = trim($_GET['slug'] ?? '');
$stmt = $pdo->prepare("SELECT * FROM berita WHERE slug=:slug AND status='terbit' LIMIT 1");
$stmt->execute(['slug' => $slug]);
$berita = $stmt->fetch();
if (!$berita) {
  http_response_code(404);
  exit('Berita tidak ditemukan.');
}
$relatedStmt = $pdo->prepare("SELECT id,slug,judul,gambar,kategori,tanggal_terbit,ringkasan FROM berita WHERE status='terbit' AND id<>:id ORDER BY tanggal_terbit DESC,id DESC LIMIT 3");
$relatedStmt->execute(['id' => $berita['id']]);
$related = $relatedStmt->fetchAll();
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= e($berita['judul']) ?> - FIKES</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
      rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>

  <body>
    <div class="topbar">
      <div class="container topbar-inner">
        <div class="topbar-info"><span>📍 Kampus FIKES</span><span>✉️ info@fikes.ac.id</span><span>📞 (021)
            1234567</span></div>
        <div class="topbar-social"><a href="#">Instagram</a><a href="#">Facebook</a><a href="#">YouTube</a></div>
      </div>
    </div>
    <header class="navbar" id="navbar">
      <div class="container nav-inner"><a href="index.php" class="logo">
          <div class="logo-icon">F</div>
          <div class="logo-text"><strong>FIKES</strong><small>FAKULTAS ILMU KESEHATAN</small></div>
        </a><button class="menu-toggle" id="menuToggle">☰</button>
        <nav class="nav-menu" id="navMenu">
          <div class="nav-item"><a href="index.php" class="nav-link">Beranda</a></div>
          <div class="nav-item"><a href="page/program-studi/program-studi.php" class="nav-link">Program Studi</a></div>
          <div class="nav-item"><a href="index.php#berita" class="nav-link">Berita</a></div>
          <div class="nav-item"><a href="index.php#tentang" class="nav-link">Tentang FIKES</a></div>
        </nav><a href="page/program-studi/program-studi.php" class="nav-cta">Jelajahi Program</a>
      </div>
    </header>
    <main>
      <section class="section article-section">
        <div class="container"><a href="index.php#berita" class="back-link">← Kembali ke Berita</a>
          <div class="article-wrap">
            <div class="article-category"><?= e($berita['kategori']) ?></div>
            <h1><?= e($berita['judul']) ?></h1>
            <div class="article-meta">📅 <?= date('d M Y', strtotime($berita['tanggal_terbit'])) ?> &nbsp; • &nbsp;
              <?= e($berita['penulis'] ?: 'FIKES') ?></div><?php if ($berita['gambar']): ?><img class="article-image"
              src="<?= e($berita['gambar']) ?>" alt="<?= e($berita['judul']) ?>"><?php endif; ?><div
              class="article-content"><?= $berita['isi'] ?></div>
          </div>
          <div class="related">
            <h2>Berita Lainnya</h2>
            <div class="news-grid"><?php foreach ($related as $b): ?><article class="news-card"><a
                  href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>"
                  class="news-image"><?php if ($b['gambar']): ?><img src="<?= e($b['gambar']) ?>"
                    alt="<?= e($b['judul']) ?>"><?php else: ?><div class="news-placeholder">FIKES</div>
                  <?php endif; ?></a>
                <div class="news-body">
                  <div class="news-date"><?= date('d M Y', strtotime($b['tanggal_terbit'])) ?></div>
                  <h3><a href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>"><?= e($b['judul']) ?></a></h3>
                  <p><?= e($b['ringkasan']) ?></p>
                </div>
              </article><?php endforeach; ?></div>
          </div>
        </div>
      </section>
    </main>
    <footer>
      <div class="container footer-main">
        <div class="footer-brand">
          <div class="logo footer-logo">
            <div class="logo-icon">F</div>
            <div class="logo-text"><strong>FIKES</strong><small>FAKULTAS ILMU KESEHATAN</small></div>
          </div>
          <p>Membangun generasi kesehatan yang profesional, berintegritas, inovatif, dan berorientasi kepada masyarakat.
          </p>
        </div>
        <div>
          <h4 class="footer-title">Tentang FIKES</h4>
          <div class="footer-links"><a href="index.php#tentang">Tentang</a><a href="#">Visi Misi</a><a
              href="#">Akreditasi</a></div>
        </div>
        <div>
          <h4 class="footer-title">Program Studi</h4>
          <div class="footer-links"><a href="page/program-studi/program-studi.php">Daftar Program Studi</a></div>
        </div>
        <div>
          <h4 class="footer-title">Informasi</h4>
          <div class="footer-links"><a href="index.php#berita">Berita</a><a href="index.php#pelayanan">Pelayanan</a>
          </div>
        </div>
      </div>
      <div class="container footer-bottom"><span>© <?= date('Y') ?> Fakultas Ilmu Kesehatan. All Rights
          Reserved.</span><span>Website FIKES</span></div>
    </footer><button class="back-top" id="backTop">↑</button>
    <script src="assets/js/main.js"></script>
  </body>

</html>
