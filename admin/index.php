<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/config/auth.php';


$stats = [];
foreach (
  [
    'dosen' => 'SELECT COUNT(*) FROM dosen WHERE status="aktif"',
    'program' => 'SELECT COUNT(*) FROM program_studi WHERE status="aktif"',
    'berita' => 'SELECT COUNT(*) FROM berita WHERE status="publish"',
    'kemahasiswaan' => 'SELECT COUNT(*) FROM kemahasiswaan WHERE status="publish"'
  ] as $key => $sql
) {
  $stats[$key] = (int)$pdo->query($sql)->fetchColumn();
}
require __DIR__ . '/includes/header.php';
?>
<div class="welcome">
  <div><span class="eyebrow">SELAMAT DATANG</span>
    <h1>Dashboard Admin FIKES 👋</h1>
    <p>Kelola seluruh informasi website Fakultas Ilmu Kesehatan dari satu tempat.</p>
  </div>
  <div class="date-box">📅 <span id="today"></span></div>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon blue">♙</div>
    <div><span>Dosen Aktif</span><strong><?= $stats['dosen'] ?></strong></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">▤</div>
    <div><span>Program Studi</span><strong><?= $stats['program'] ?></strong></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange">◫</div>
    <div><span>Berita Publish</span><strong><?= $stats['berita'] ?></strong></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple">♧</div>
    <div><span>Konten Mahasiswa</span><strong><?= $stats['kemahasiswaan'] ?></strong></div>
  </div>
</div>

<div class="grid-2">
  <div class="panel">
    <div class="panel-head">
      <div><span class="eyebrow">AKSES CEPAT</span>
        <h2>Kelola Konten</h2>
      </div>
    </div>
    <div class="quick-grid">
      <a href="modules/dosen/index.php"><span>♙</span><strong>Daftar Dosen</strong><small>Tambah & edit
          dosen</small></a>
      <a href="modules/program-studi/index.php"><span>▤</span><strong>Program Studi</strong><small>Kelola program
          akademik</small></a>
      <a href="modules/berita/index.php"><span>◫</span><strong>Berita</strong><small>Publikasi informasi</small></a>
      <a href="modules/tentang/visi-misi.php"><span>◉</span><strong>Visi & Misi</strong><small>Perbarui profil
          FIKES</small></a>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head">
      <div><span class="eyebrow">STRUKTUR SITUS</span>
        <h2>Menu Website</h2>
      </div>
    </div>
    <div class="site-menu-list">
      <div><span>01</span>Tentang FIKES <b>4</b></div>
      <div><span>02</span>Daftar Dosen <b><?= $stats['dosen'] ?></b></div>
      <div><span>03</span>Program Studi <b><?= $stats['program'] ?></b></div>
      <div><span>04</span>Kemahasiswaan <b><?= $stats['kemahasiswaan'] ?></b></div>
      <div><span>05</span>Informasi & Berita <b><?= $stats['berita'] ?></b></div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
