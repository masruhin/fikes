<?php
require_once __DIR__ . '/../config/auth.php';
wajib_login();
$page_title = $page_title ?? 'Dashboard';
$pos_admin = strpos($_SERVER['SCRIPT_NAME'], '/admin/');
$project_url = ($pos_admin !== false) ? substr($_SERVER['SCRIPT_NAME'], 0, $pos_admin) : '';
$current_url = $_SERVER['REQUEST_URI'];
function menu_active($path)
{
  global $current_url;
  return strpos($current_url, $path) !== false ? 'active' : '';
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($page_title) ?> | Admin FIKES</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="<?= $project_url ?>/admin/assets/css/admin.css">
</head>

<body>
  <div class="app">
    <aside class="sidebar" id="sidebar">
      <div class="brand">
        <div class="brand-mark">F</div>
        <div><strong>FIKES</strong><small>ADMIN PANEL</small></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= $project_url ?>/admin/index.php"
          class="menu-link <?= menu_active('/admin/index.php') ?>"><span>▦</span><b>Dashboard</b></a>
        <button type="button" class="menu-parent" data-menu="kemahasiswaan"><span>♧</span><b>Konten
            Website</b><i>⌄</i></button>
        <div class="submenu" id="kemahasiswaan">
          <a href="<?= $project_url ?>/admin/modules/slider/index.php"
            class=" submenu-link <?= menu_active('kategori=Unit%20Himpunan') ?>">Slider Beranda</a>
          <a href="<?= $project_url ?>/admin/modules/berita/index.php"
            class="submenu-link <?= menu_active('kategori=UKM%20Kemahasiswaan') ?>">Berita</a>
        </div>

        <button type="button" class="menu-parent" data-menu="tentang"><span>◉</span><b>Tentang
            FIKES</b><i>⌄</i></button>
        <div class="submenu" id="tentang">
          <a href="<?= $project_url ?>/admin/modules/tentang/visi-misi.php"
            class="submenu-link <?= menu_active('/tentang/visi-misi.php') ?>">Visi-Misi</a>
          <a href="<?= $project_url ?>/admin/modules/tentang/struktur.php"
            class="submenu-link <?= menu_active('/tentang/struktur.php') ?>">Struktur Organisasi</a>
          <!-- <a href="<?= $project_url ?>/admin/modules/tentang/akreditasi.php"
            class="submenu-link <?= menu_active('/tentang/akreditasi.php') ?>">Sertifikat Akreditasi</a> -->
          <a href="<?= $project_url ?>/admin/modules/sertifikat/index.php"
            class="submenu-link <?= menu_active('/tentang/akreditasi.php') ?>">Sertifikat Akreditasi</a>
          <a href="<?= $project_url ?>/admin/modules/tentang/logo.php"
            class="submenu-link <?= menu_active('/tentang/logo.php') ?>">Unduh Logo</a>
        </div>
        <button type="button" class="menu-parent" data-menu="dosen"><span>♙</span><b>Daftar Dosen</b><i>⌄</i></button>
        <div class="submenu" id="dosen">
          <a href="<?= $project_url ?>/admin/modules/dosen/index.php?prodi=Keperawatan"
            class="submenu-link <?= menu_active('prodi=Keperawatan') ?>">Dosen Keperawatan</a>
          <a href="<?= $project_url ?>/admin/modules/dosen/index.php?prodi=Kebidanan"
            class="submenu-link <?= menu_active('prodi=Kebidanan') ?>">Dosen Kebidanan</a>
          <a href="<?= $project_url ?>/admin/modules/dosen/index.php?prodi=Farmasi"
            class="submenu-link <?= menu_active('prodi=Farmasi') ?>">Dosen Farmasi</a>
          <a href="<?= $project_url ?>/admin/modules/dosen/index.php?prodi=K3"
            class="submenu-link <?= menu_active('prodi=K3') ?>">Dosen K3</a>
        </div>
        <button type="button" class="menu-parent"
          data-menu="kemahasiswaan"><span>♧</span><b>Kemahasiswaan</b><i>⌄</i></button>
        <div class="submenu" id="kemahasiswaan">
          <a href="<?= $project_url ?>/admin/modules/kemahasiswaan/index.php?kategori=Unit%20Himpunan%20Mahasiswa"
            class="submenu-link <?= menu_active('kategori=Unit%20Himpunan') ?>">Unit Himpunan Mahasiswa</a>
          <a href="<?= $project_url ?>/admin/modules/kemahasiswaan/index.php?kategori=UKM%20Kemahasiswaan"
            class="submenu-link <?= menu_active('kategori=UKM%20Kemahasiswaan') ?>">UKM Kemahasiswaan</a>
        </div>
        <a href="<?= $project_url ?>/admin/modules/program-studi/index.php"
          class="menu-link <?= menu_active('/modules/program-studi/') ?>"><span>▤</span><b>Program Studi</b>
        </a>
        <!-- <button type="button" class="menu-parent" data-menu="vokasi"><span>◇</span><b>Program
              Vokasi</b><i>⌄</i></button>
          <div class="submenu" id="vokasi"><a href="<?= $project_url ?>/admin/modules/program/index.php?jenjang=Profesi"
              class="submenu-link <?= menu_active('jenjang=Profesi') ?>">Program Profesi Ners</a></div>
          <button type="button" class="menu-parent" data-menu="sarjana"><span>▤</span><b>Program
              Sarjana</b><i>⌄</i></button>
          <div class="submenu" id="sarjana">
            <a href="<?= $project_url ?>/admin/modules/program/index.php?jenjang=Sarjana&nama=Keperawatan"
              class="submenu-link <?= menu_active('jenjang=Sarjana&nama=Keperawatan') ?>">Keperawatan</a>
            <a href="<?= $project_url ?>/admin/modules/program/index.php?jenjang=Sarjana&nama=Farmasi"
              class="submenu-link <?= menu_active('jenjang=Sarjana&nama=Farmasi') ?>">Farmasi</a>
          </div>
          <button type="button" class="menu-parent" data-menu="d3"><span>▥</span><b>Program D3</b><i>⌄</i></button>
          <div class="submenu" id="d3">
            <a href="<?= $project_url ?>/admin/modules/program/index.php?jenjang=Diploma&nama=Keperawatan"
              class="submenu-link <?= menu_active('jenjang=Diploma&nama=Keperawatan') ?>">Keperawatan</a>
            <a href="<?= $project_url ?>/admin/modules/program/index.php?jenjang=Diploma&nama=Kebidanan"
              class="submenu-link <?= menu_active('jenjang=Diploma&nama=Kebidanan') ?>">Kebidanan</a>
            <a href="<?= $project_url ?>/admin/modules/program/index.php?jenjang=Diploma&nama=K3"
              class="submenu-link <?= menu_active('jenjang=Diploma&nama=K3') ?>">K3</a>
          </div> -->
        <a href="<?= $project_url ?>/admin/modules/akademik/index.php"
          class="menu-link <?= menu_active('/modules/akademik/') ?>"><span>▣</span><b>Akademik</b>
        </a>
        <a href="<?= $project_url ?>/admin/modules/survey/index.php"
          class="menu-link <?= menu_active('/modules/survey/') ?>"><span>◎</span><b>Survey</b></a>
        <button type="button" class="menu-parent" data-menu="akun"><span>⚙</span><b>Pengaturan
          </b><i>⌄</i></button>
        <div class="submenu" id="akun">
          <a href="<?= $project_url ?>/admin/modules/pengaturan/index.php"
            class="submenu-link <?= menu_active('/modules/pengaturan/') ?>">Pengaturan Website</a>
          <a href="<?= $project_url ?>/admin/modules/akun/index.php"
            class="submenu-link <?= menu_active('/modules/akun/') ?>">Profil Admin</a>
        </div>
      </nav><a href="<?= $project_url ?>/admin/logout.php" class="logout"><span>↪</span> Keluar</a>
    </aside>
    <div class="overlay" id="overlay"></div>
    <main class="main">
      <header class="topbar"><button type="button" id="sidebarToggle" class="icon-btn"
          aria-label="Buka menu">☰</button>
        <div class="top-title"><span>Panel Administrasi</span><strong><?= e($page_title) ?></strong></div>
        <div class="user">
          <div class="avatar"><?= strtoupper(substr($_SESSION['admin_nama'], 0, 1)) ?></div>
          <div><strong><?= e($_SESSION['admin_nama']) ?></strong><small><?= e($_SESSION['admin_role']) ?></small>
          </div>
        </div>
      </header>
      <section class="content">
