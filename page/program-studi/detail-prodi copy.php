<?php
require_once __DIR__ . '/../../admin/config/database.php';

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* =========================================================
   IDENTITAS PROGRAM STUDI
   Mendukung:
   detail-prodi.php?kode=D3-FAR
   dan URL lama:
   detail-prodi.php?id=3
   ========================================================= */

$kode_prodi = trim($_GET['kode'] ?? '');

if ($kode_prodi === '' && isset($_GET['id'])) {
  $id = (int) $_GET['id'];

  $stmt = $pdo->prepare("
        SELECT kode_prodi
        FROM program_studi
        WHERE id = :id
        LIMIT 1
    ");
  $stmt->execute(['id' => $id]);

  $kode_prodi = $stmt->fetchColumn() ?: '';
}

if ($kode_prodi === '') {
  die('Kode program studi tidak ditemukan.');
}

/* =========================================================
   DATA PROGRAM STUDI
   ========================================================= */

$stmt = $pdo->prepare("
    SELECT p.*,
           (
               SELECT COUNT(*)
               FROM dosen d
               WHERE d.program_studi = p.nama
           ) AS jumlah_dosen
    FROM program_studi p
    WHERE p.kode_prodi = :kode_prodi
      AND p.status = 'aktif'
    LIMIT 1
");
$stmt->execute(['kode_prodi' => $kode_prodi]);

$prodi = $stmt->fetch();

if (!$prodi) {
  die('Program studi tidak ditemukan.');
}

$foto = '';
if (!empty($prodi['gambar'])) {
  $foto = '../../admin/uploads/program-studi/' . rawurlencode($prodi['gambar']);
}

$brosur = '';
if (!empty($prodi['brosur'])) {
  $brosur = '../../admin/uploads/program-studi/' . rawurlencode($prodi['brosur']);
}

/* =========================================================
   DATA TURUNAN
   ========================================================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM prodi_misi
    WHERE prodi_id = :prodi_id
    ORDER BY nomor_urut ASC, id ASC
");
$stmt->execute(['prodi_id' => $prodi['id']]);
$misi = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT *
    FROM prodi_capaian_pembelajaran
    WHERE prodi_id = :prodi_id
    ORDER BY nomor_urut ASC, id ASC
");
$stmt->execute(['prodi_id' => $prodi['id']]);
$cpl = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT *
    FROM prodi_kurikulum
    WHERE prodi_id = :prodi_id
    ORDER BY nomor_urut ASC, id ASC
");
$stmt->execute(['prodi_id' => $prodi['id']]);
$kurikulum = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT *
    FROM prodi_fasilitas
    WHERE prodi_id = :prodi_id
    ORDER BY nomor_urut ASC, id ASC
");
$stmt->execute(['prodi_id' => $prodi['id']]);
$fasilitas = $stmt->fetchAll();

$page_title = $prodi['nama'];
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title) ?> | FIKES</title>

    <meta name="description" content="Informasi program studi <?= e($prodi['nama']) ?> - Fakultas Ilmu Kesehatan.">

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
      --white: #ffffff;

      --border: #e5ece9;
      --shadow: 0 20px 60px rgba(18, 55, 42, 0.1);

      --radius: 18px;
      --transition: 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: "Inter", sans-serif;
      color: var(--text);
      background: var(--white);
      line-height: 1.7;
      overflow-x: hidden;
    }

    h1,
    h2,
    h3,
    h4,
    h5 {
      font-family: "Plus Jakarta Sans", sans-serif;
      color: var(--dark);
      line-height: 1.3;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    ul {
      list-style: none;
    }

    img {
      max-width: 100%;
      display: block;
    }

    .container {
      width: min(1180px, calc(100% - 40px));
      margin: auto;
    }

    .section {
      padding: 100px 0;
    }

    .section-header {
      max-width: 700px;
      margin: 0 auto 55px;
      text-align: center;
    }

    .section-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 15px;
      border-radius: 50px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 15px;
    }

    .section-title {
      font-size: clamp(30px, 4vw, 45px);
      margin-bottom: 15px;
    }

    .section-description {
      color: var(--text);
    }

    /* =========================================================
           TOP BAR
        ========================================================= */

    .topbar {
      background: var(--dark);
      color: #d9e8e2;
      font-size: 13px;
    }

    .topbar-inner {
      min-height: 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
    }

    .topbar-info {
      display: flex;
      gap: 25px;
      align-items: center;
    }

    .topbar-info span {
      display: flex;
      align-items: center;
      gap: 7px;
    }

    .topbar-social {
      display: flex;
      gap: 15px;
    }

    .topbar-social a {
      transition: var(--transition);
    }

    .topbar-social a:hover {
      color: var(--secondary);
    }

    /* =========================================================
           NAVBAR
        ========================================================= */

    .navbar {
      position: sticky;
      top: 0;
      z-index: 999;
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(229, 236, 233, 0.8);
      transition: var(--transition);
    }

    .navbar.scrolled {
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
    }

    .nav-inner {
      min-height: 82px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 30px;
    }

    /* LOGO */

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
    }

    .logo-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), #13a878);

      color: white;

      display: flex;
      align-items: center;
      justify-content: center;

      font-weight: 800;
      font-size: 17px;

      box-shadow: 0 10px 25px rgba(8, 127, 91, 0.25);
    }

    .logo-text strong {
      display: block;
      color: var(--dark);
      font-size: 17px;
      line-height: 1.2;
    }

    .logo-text small {
      display: block;
      font-size: 10px;
      color: var(--primary);
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    /* NAV MENU */

    .nav-menu {
      display: flex;
      align-items: center;
      gap: 3px;
    }

    .nav-item {
      position: relative;
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

      transition: var(--transition);
      white-space: nowrap;
    }

    .nav-link:hover {
      color: var(--primary);
    }

    .arrow {
      font-size: 11px;
      transition: var(--transition);
    }

    .nav-item:hover>.nav-link .arrow {
      transform: rotate(180deg);
    }

    /* DROPDOWN */

    .dropdown {
      position: absolute;
      top: calc(100% + 5px);
      left: 0;

      width: 250px;

      padding: 10px;

      background: white;
      border: 1px solid var(--border);
      border-radius: 14px;

      box-shadow: var(--shadow);

      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);

      transition: var(--transition);
    }

    .nav-item:hover>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-item {
      position: relative;
    }

    .dropdown-link {
      display: flex;
      justify-content: space-between;
      align-items: center;

      padding: 10px 13px;
      border-radius: 9px;

      color: #40544d;
      font-size: 13px;
      font-weight: 500;

      transition: var(--transition);
    }

    .dropdown-link:hover {
      color: var(--primary);
      background: var(--primary-light);
    }

    /* SUB DROPDOWN */

    .dropdown-item>.dropdown {
      left: calc(100% + 5px);
      top: -10px;
    }

    .dropdown-item:hover>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    /* NAV BUTTON */

    .nav-cta {
      padding: 12px 19px;
      border-radius: 10px;
      background: var(--primary);
      color: white;

      font-size: 13px;
      font-weight: 700;

      transition: var(--transition);
      white-space: nowrap;
    }

    .nav-cta:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(8, 127, 91, 0.22);
    }

    /* MOBILE MENU */

    .menu-toggle {
      display: none;

      width: 44px;
      height: 44px;

      border: none;
      border-radius: 10px;

      background: var(--primary-light);
      color: var(--primary);

      font-size: 22px;
      cursor: pointer;
    }

    /* =========================================================
           HERO
        ========================================================= */

    .hero {
      position: relative;
      min-height: 680px;

      display: flex;
      align-items: center;

      overflow: hidden;

      background:
        radial-gradient(circle at 85% 20%,
          rgba(8, 127, 91, 0.14),
          transparent 30%),
        linear-gradient(135deg, #f7fcfa 0%, #ffffff 55%, #eef9f5 100%);
    }

    .hero::before {
      content: "";
      position: absolute;

      width: 500px;
      height: 500px;

      border-radius: 50%;

      right: -200px;
      bottom: -220px;

      background: rgba(244, 185, 66, 0.13);
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: 70px;
      align-items: center;

      position: relative;
      z-index: 1;
    }

    .hero-content {
      animation: heroUp 0.8s ease both;
    }

    @keyframes heroUp {
      from {
        opacity: 0;
        transform: translateY(25px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;

      padding: 9px 15px;
      border-radius: 50px;

      background: white;
      color: var(--primary);

      font-size: 12px;
      font-weight: 700;

      box-shadow: 0 8px 25px rgba(8, 127, 91, 0.08);

      margin-bottom: 22px;
    }

    .hero-badge span {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--secondary);
    }

    .hero h1 {
      font-size: clamp(42px, 5.5vw, 68px);
      letter-spacing: -2px;
      margin-bottom: 20px;
    }

    .hero h1 span {
      color: var(--primary);
    }

    .hero-description {
      max-width: 620px;
      font-size: 17px;
      margin-bottom: 30px;
    }

    .hero-buttons {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      gap: 8px;

      padding: 14px 22px;
      border-radius: 11px;

      font-size: 14px;
      font-weight: 700;

      transition: var(--transition);
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(8, 127, 91, 0.22);
    }

    .btn-outline {
      border: 1px solid var(--border);
      background: white;
      color: var(--dark);
    }

    .btn-outline:hover {
      border-color: var(--primary);
      color: var(--primary);
      transform: translateY(-3px);
    }

    /* HERO VISUAL */

    .hero-visual {
      position: relative;
      min-height: 480px;

      display: flex;
      justify-content: center;
      align-items: center;
    }

    .hero-card-main {
      width: min(390px, 85%);
      height: 430px;

      border-radius: 30px;

      background: linear-gradient(160deg,
          rgba(8, 127, 91, 0.95),
          rgba(3, 83, 61, 0.95));

      position: relative;
      overflow: hidden;

      box-shadow: 0 35px 70px rgba(8, 127, 91, 0.25);

      transform: rotate(3deg);
    }

    .hero-card-main::before {
      content: "FIKES";

      position: absolute;

      font-size: 100px;
      font-weight: 800;

      color: rgba(255, 255, 255, 0.06);

      transform: rotate(-20deg);

      bottom: 30px;
      left: -20px;
    }

    .hero-card-content {
      position: absolute;
      inset: 0;

      padding: 40px;

      color: white;

      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }

    .hero-card-icon {
      position: absolute;
      top: 35px;
      left: 35px;

      width: 70px;
      height: 70px;

      border-radius: 20px;

      background: rgba(255, 255, 255, 0.15);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 32px;
    }

    .hero-card-content h3 {
      color: white;
      font-size: 29px;
      margin-bottom: 8px;
    }

    .hero-card-content p {
      color: #d6eee5;
      font-size: 14px;
    }

    .floating-card {
      position: absolute;

      background: white;

      border-radius: 17px;

      padding: 17px 20px;

      box-shadow: var(--shadow);

      display: flex;
      align-items: center;
      gap: 13px;

      animation: floating 4s ease-in-out infinite;
    }

    .floating-card.one {
      top: 65px;
      right: 0;
    }

    .floating-card.two {
      bottom: 60px;
      left: 0;

      animation-delay: 1.3s;
    }

    @keyframes floating {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    .floating-icon {
      width: 43px;
      height: 43px;

      border-radius: 12px;

      background: var(--primary-light);
      color: var(--primary);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 20px;
    }

    .floating-card strong {
      display: block;
      color: var(--dark);
      font-size: 17px;
    }

    .floating-card span {
      display: block;
      font-size: 11px;
    }

    /* =========================================================
           STATS
        ========================================================= */

    .stats {
      margin-top: -50px;
      position: relative;
      z-index: 10;
    }

    .stats-grid {
      background: white;
      border-radius: 20px;
      box-shadow: var(--shadow);

      display: grid;
      grid-template-columns: repeat(4, 1fr);

      overflow: hidden;
    }

    .stat {
      text-align: center;
      padding: 28px 20px;

      border-right: 1px solid var(--border);
    }

    .stat:last-child {
      border-right: 0;
    }

    .stat-number {
      font-family: "Plus Jakarta Sans";
      color: var(--primary);
      font-size: 30px;
      font-weight: 800;
    }

    .stat-label {
      font-size: 12px;
      font-weight: 600;
    }

    /* =========================================================
           ABOUT
        ========================================================= */

    .about {
      background: var(--light);
    }

    .about-grid {
      display: grid;
      grid-template-columns: 0.9fr 1.1fr;
      gap: 70px;
      align-items: center;
    }

    .about-image {
      min-height: 480px;

      border-radius: 30px;

      background: linear-gradient(135deg,
          rgba(8, 127, 91, 0.9),
          rgba(18, 55, 42, 0.9));

      position: relative;
      overflow: hidden;
    }

    .about-image::before {
      content: "";

      width: 330px;
      height: 330px;

      position: absolute;

      border-radius: 50%;

      border: 50px solid rgba(255, 255, 255, 0.06);

      top: -70px;
      right: -80px;
    }

    .about-image-content {
      position: absolute;
      inset: 0;

      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;

      color: white;
      text-align: center;
      padding: 40px;
    }

    .about-logo {
      width: 110px;
      height: 110px;

      border-radius: 30px;

      background: rgba(255, 255, 255, 0.12);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 30px;
      font-weight: 800;

      margin-bottom: 25px;
    }

    .about-image-content h3 {
      color: white;
      font-size: 27px;
      margin-bottom: 10px;
    }

    .about-image-content p {
      color: #d6eee5;
    }

    .about-content h2 {
      font-size: clamp(32px, 4vw, 45px);
      margin-bottom: 20px;
    }

    .about-content>p {
      margin-bottom: 25px;
    }

    .feature-list {
      display: grid;
      gap: 14px;
    }

    .feature {
      display: flex;
      gap: 13px;
      align-items: flex-start;
    }

    .feature-check {
      width: 25px;
      height: 25px;

      flex-shrink: 0;

      border-radius: 50%;

      background: var(--primary-light);
      color: var(--primary);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 12px;
      font-weight: 800;
    }

    .feature strong {
      display: block;
      color: var(--dark);
      font-size: 14px;
      margin-bottom: 2px;
    }

    .feature span {
      font-size: 13px;
    }

    /* =========================================================
           PROGRAM
        ========================================================= */

    .program-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }

    .program-card {
      padding: 28px;

      border: 1px solid var(--border);
      border-radius: var(--radius);

      background: white;

      transition: var(--transition);

      position: relative;
      overflow: hidden;
    }

    .program-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow);
      border-color: transparent;
    }

    .program-card::after {
      content: "";

      position: absolute;

      width: 100px;
      height: 100px;

      border-radius: 50%;

      right: -50px;
      bottom: -50px;

      background: var(--primary-light);
    }

    .program-icon {
      width: 56px;
      height: 56px;

      border-radius: 16px;

      background: var(--primary-light);
      color: var(--primary);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 25px;

      margin-bottom: 20px;
    }

    .program-card h3 {
      font-size: 19px;
      margin-bottom: 10px;
    }

    .program-card p {
      font-size: 13px;
      margin-bottom: 15px;
    }

    .program-link {
      color: var(--primary);
      font-size: 13px;
      font-weight: 700;
    }

    /* =========================================================
           SERVICES
        ========================================================= */

    .services {
      background: var(--light);
    }

    .service-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
    }

    .service-card {
      padding: 25px;

      border-radius: 16px;

      background: white;
      border: 1px solid var(--border);

      transition: var(--transition);
    }

    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow);
    }

    .service-card-icon {
      width: 48px;
      height: 48px;

      border-radius: 13px;

      background: var(--primary-light);
      color: var(--primary);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 20px;

      margin-bottom: 18px;
    }

    .service-card h3 {
      font-size: 16px;
      margin-bottom: 7px;
    }

    .service-card p {
      font-size: 12px;
    }

    /* =========================================================
           STUDENT
        ========================================================= */

    .student-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
    }

    .student-card {
      padding: 35px;

      border-radius: 22px;

      color: white;

      background: linear-gradient(135deg,
          var(--primary),
          var(--primary-dark));

      position: relative;
      overflow: hidden;
    }

    .student-card.secondary {
      background: linear-gradient(135deg, #12372a, #1e5945);
    }

    .student-card::after {
      content: "";

      position: absolute;

      width: 200px;
      height: 200px;

      border-radius: 50%;

      background: rgba(255, 255, 255, 0.05);

      right: -80px;
      bottom: -100px;
    }

    .student-card h3 {
      color: white;
      font-size: 24px;
      margin-bottom: 10px;
    }

    .student-card p {
      color: #d7eee6;
      font-size: 13px;
      margin-bottom: 20px;
    }

    .student-links {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .student-links a {
      padding: 8px 12px;

      border-radius: 8px;

      background: rgba(255, 255, 255, 0.1);

      font-size: 11px;
      font-weight: 600;

      transition: var(--transition);
    }

    .student-links a:hover {
      background: white;
      color: var(--primary);
    }

    /*INI AWAL CSS UNTUK VISI MISI*/
    .page-hero {
      position: relative;

      padding: 100px 0 105px;

      overflow: hidden;

      background:
        radial-gradient(circle at 85% 20%,
          rgba(8, 127, 91, 0.14),
          transparent 30%),
        linear-gradient(135deg, #f5fcf9, #ffffff 60%, #edf8f4);
    }

    .page-hero::before {
      content: "";

      position: absolute;

      width: 400px;
      height: 400px;

      border-radius: 50%;

      border: 70px solid rgba(8, 127, 91, 0.04);

      right: -130px;
      top: -130px;
    }

    .hero-content {
      position: relative;
      z-index: 2;

      max-width: 800px;
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 9px;

      margin-bottom: 25px;

      font-size: 13px;
    }

    .breadcrumb a {
      color: var(--primary);
      font-weight: 600;
    }

    .breadcrumb span {
      color: #9aa9a3;
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

      margin-bottom: 18px;
    }

    .hero-label span {
      width: 7px;
      height: 7px;

      border-radius: 50%;

      background: var(--secondary);
    }

    .page-hero h1 {
      font-size: clamp(40px, 5vw, 62px);

      letter-spacing: -1.5px;

      margin-bottom: 18px;
    }

    .page-hero h1 span {
      color: var(--primary);
    }

    .page-hero p {
      max-width: 690px;

      font-size: 17px;
    }

    /* =====================================================
           MAIN CONTENT
        ===================================================== */

    .section {
      padding: 95px 0;
    }

    /* =====================================================
           VISI
        ===================================================== */

    .visi-section {
      background: white;
    }

    .visi-grid {
      display: grid;

      grid-template-columns: 0.8fr 1.2fr;

      gap: 70px;

      align-items: center;
    }

    .visi-visual {
      min-height: 420px;

      border-radius: 30px;

      background: linear-gradient(145deg,
          var(--primary),
          var(--primary-dark));

      position: relative;

      overflow: hidden;

      box-shadow: 0 30px 60px rgba(8, 127, 91, 0.18);
    }

    .visi-visual::before {
      content: "";

      position: absolute;

      width: 340px;
      height: 340px;

      border: 55px solid rgba(255, 255, 255, 0.05);

      border-radius: 50%;

      right: -150px;
      top: -100px;
    }

    .visi-visual::after {
      content: "";

      position: absolute;

      width: 250px;
      height: 250px;

      border: 40px solid rgba(244, 185, 66, 0.08);

      border-radius: 50%;

      left: -120px;
      bottom: -100px;
    }

    .visi-icon {
      position: absolute;

      width: 90px;
      height: 90px;

      left: 45px;
      top: 45px;

      border-radius: 25px;

      background: rgba(255, 255, 255, 0.12);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 40px;

      color: white;
    }

    .visi-visual-content {
      position: absolute;

      left: 45px;
      right: 45px;
      bottom: 45px;

      z-index: 2;
    }

    .visi-visual-content small {
      color: #bde2d5;

      font-size: 12px;
      font-weight: 700;

      text-transform: uppercase;
      letter-spacing: 1.5px;
    }

    .visi-visual-content h3 {
      color: white;

      font-size: 31px;

      margin-top: 8px;
    }

    .visi-content .section-label {
      display: inline-flex;

      padding: 8px 14px;

      border-radius: 50px;

      background: var(--primary-light);
      color: var(--primary);

      font-size: 12px;
      font-weight: 700;

      margin-bottom: 17px;
    }

    .visi-content h2 {
      font-size: clamp(30px, 4vw, 44px);

      margin-bottom: 20px;
    }

    .visi-text {
      padding: 28px 30px;

      border-left: 4px solid var(--primary);

      border-radius: 0 16px 16px 0;

      background: var(--light);

      font-size: 18px;

      color: var(--dark);

      font-weight: 600;
    }

    /* =====================================================
           MISI
        ===================================================== */

    .misi-section {
      background: var(--light);
    }

    .section-header {
      max-width: 720px;

      text-align: center;

      margin: 0 auto 55px;
    }

    .section-label {
      display: inline-flex;

      padding: 8px 15px;

      border-radius: 50px;

      background: var(--primary-light);
      color: var(--primary);

      font-size: 12px;
      font-weight: 700;

      margin-bottom: 15px;
    }

    .section-header h2 {
      font-size: clamp(32px, 4vw, 45px);

      margin-bottom: 15px;
    }

    .section-header p {
      font-size: 15px;
    }

    .misi-grid {
      display: grid;

      grid-template-columns: repeat(2, 1fr);

      gap: 22px;
    }

    .misi-card {
      position: relative;

      padding: 30px;

      border: 1px solid var(--border);

      border-radius: 20px;

      background: white;

      display: flex;

      gap: 22px;

      transition: var(--transition);
    }

    .misi-card:hover {
      transform: translateY(-6px);

      border-color: transparent;

      box-shadow: var(--shadow);
    }

    .misi-number {
      width: 52px;
      height: 52px;

      flex-shrink: 0;

      border-radius: 15px;

      background: var(--primary);

      color: white;

      display: flex;
      align-items: center;
      justify-content: center;

      font-family: "Plus Jakarta Sans";

      font-size: 18px;
      font-weight: 800;
    }

    .misi-card h3 {
      font-size: 17px;

      margin-bottom: 8px;
    }

    .misi-card p {
      font-size: 13px;
    }

    /* =====================================================
           NILAI
        ===================================================== */

    .nilai-section {
      background: white;
    }

    .nilai-grid {
      display: grid;

      grid-template-columns: repeat(4, 1fr);

      gap: 18px;
    }

    .nilai-card {
      text-align: center;

      padding: 32px 22px;

      border: 1px solid var(--border);

      border-radius: 18px;

      transition: var(--transition);
    }

    .nilai-card:hover {
      transform: translateY(-7px);

      box-shadow: var(--shadow);

      border-color: transparent;
    }

    .nilai-icon {
      width: 60px;
      height: 60px;

      margin: 0 auto 18px;

      border-radius: 17px;

      background: var(--primary-light);
      color: var(--primary);

      display: flex;
      align-items: center;
      justify-content: center;

      font-size: 25px;
    }

    .nilai-card h3 {
      font-size: 16px;

      margin-bottom: 7px;
    }

    .nilai-card p {
      font-size: 12px;
    }

    /* =====================================================
           CTA
        ===================================================== */

    .cta-section {
      padding: 80px 0;
    }

    .cta {
      position: relative;

      overflow: hidden;

      padding: 65px;

      border-radius: 30px;

      background: linear-gradient(135deg, var(--dark), var(--primary-dark));

      color: white;

      text-align: center;
    }

    .cta::before {
      content: "";

      position: absolute;

      width: 350px;
      height: 350px;

      border: 70px solid rgba(255, 255, 255, 0.03);

      border-radius: 50%;

      left: -130px;
      bottom: -190px;
    }

    .cta::after {
      content: "";

      position: absolute;

      width: 220px;
      height: 220px;

      border: 45px solid rgba(244, 185, 66, 0.05);

      border-radius: 50%;

      right: -80px;
      top: -100px;
    }

    .cta-content {
      position: relative;
      z-index: 2;
    }

    .cta h2 {
      color: white;

      font-size: clamp(30px, 4vw, 42px);

      margin-bottom: 12px;
    }

    .cta p {
      max-width: 620px;

      margin: 0 auto 25px;

      color: #d6e9e2;
    }

    .btn {
      display: inline-flex;

      align-items: center;
      justify-content: center;

      padding: 14px 23px;

      border-radius: 11px;

      background: var(--secondary);

      color: var(--dark);

      font-size: 13px;
      font-weight: 800;

      transition: var(--transition);
    }

    .btn:hover {
      transform: translateY(-3px);

      box-shadow: 0 12px 30px rgba(244, 185, 66, 0.2);
    }

    .hero {
      position: relative;
      min-height: 680px;

      display: flex;
      align-items: center;

      overflow: hidden;

      background:
        radial-gradient(circle at 85% 20%,
          rgba(8, 127, 91, 0.14),
          transparent 30%),
        linear-gradient(135deg, #f7fcfa 0%, #ffffff 55%, #eef9f5 100%);
    }

    .hero::before {
      content: "";
      position: absolute;

      width: 500px;
      height: 500px;

      border-radius: 50%;

      right: -200px;
      bottom: -220px;

      background: rgba(244, 185, 66, 0.13);
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: 70px;
      align-items: center;

      position: relative;
      z-index: 1;
    }

    /*INI AKHIR CSS UNTUK VISI MISI*/

    /*INI AKHIR CSS UNTUK STRUKTUR ORGANISASI*/
    .page-hero {
      position: relative;

      padding: 100px 0 105px;

      overflow: hidden;

      background:
        radial-gradient(circle at 85% 20%,
          rgba(8, 127, 91, 0.14),
          transparent 30%),
        linear-gradient(135deg, #f5fcf9, #ffffff 60%, #edf8f4);
    }

    .page-hero::before {
      content: "";

      position: absolute;

      width: 400px;
      height: 400px;

      border-radius: 50%;

      border: 70px solid rgba(8, 127, 91, 0.04);

      right: -130px;

      top: -130px;
    }

    .hero-content {
      position: relative;

      z-index: 2;

      max-width: 800px;
    }

    .breadcrumb {
      display: flex;

      align-items: center;

      gap: 9px;

      margin-bottom: 25px;

      font-size: 13px;
    }

    .breadcrumb a {
      color: var(--primary);

      font-weight: 600;
    }

    .breadcrumb span {
      color: #9aa9a3;
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

      margin-bottom: 18px;
    }

    .hero-label span {
      width: 7px;
      height: 7px;

      border-radius: 50%;

      background: var(--secondary);
    }

    .page-hero h1 {
      font-size: clamp(40px, 5vw, 62px);

      letter-spacing: -1.5px;

      margin-bottom: 18px;
    }

    .page-hero h1 span {
      color: var(--primary);
    }

    .page-hero p {
      max-width: 700px;

      font-size: 17px;
    }

    /* =========================================================
           GENERAL SECTION
        ========================================================= */

    .section {
      padding: 95px 0;
    }

    .section-header {
      max-width: 720px;

      margin: 0 auto 50px;

      text-align: center;
    }

    .section-label {
      display: inline-flex;

      align-items: center;

      padding: 8px 15px;

      border-radius: 50px;

      background: var(--primary-light);

      color: var(--primary);

      font-size: 12px;

      font-weight: 700;

      margin-bottom: 15px;
    }

    .section-title {
      font-size: clamp(30px, 4vw, 45px);

      margin-bottom: 15px;
    }

    .section-description {
      font-size: 15px;
    }

    /* =========================================================
           ORGANIZATION IMAGE SECTION
        ========================================================= */

    .organization-section {
      background: white;
    }

    .organization-wrapper {
      max-width: 1100px;

      margin: auto;
    }

    /* IMAGE CARD */

    .organization-card {
      position: relative;

      background: white;

      border: 1px solid var(--border);

      border-radius: 24px;

      padding: 18px;

      box-shadow: var(--shadow);

      overflow: hidden;

      transition: var(--transition);
    }

    .organization-card:hover {
      box-shadow: var(--shadow-hover);
    }

    /* IMAGE HEADER */

    .organization-card-header {
      display: flex;

      align-items: center;

      justify-content: space-between;

      gap: 20px;

      padding: 12px 12px 20px;
    }

    .organization-card-header-left {
      display: flex;

      align-items: center;

      gap: 13px;
    }

    .organization-card-icon {
      width: 48px;
      height: 48px;

      flex-shrink: 0;

      border-radius: 14px;

      background: var(--primary-light);

      color: var(--primary);

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 21px;
    }

    .organization-card-header h3 {
      font-size: 17px;
    }

    .organization-card-header p {
      font-size: 11px;

      margin-top: 2px;
    }

    /* ZOOM BUTTON */

    .zoom-btn {
      display: inline-flex;

      align-items: center;

      gap: 7px;

      padding: 10px 14px;

      border: 1px solid var(--border);

      border-radius: 10px;

      background: white;

      color: var(--primary);

      font-size: 12px;

      font-weight: 700;

      cursor: pointer;

      transition: var(--transition);
    }

    .zoom-btn:hover {
      background: var(--primary);

      color: white;

      border-color: var(--primary);
    }

    /* IMAGE */

    .organization-image-container {
      position: relative;

      width: 100%;

      overflow: hidden;

      border-radius: 16px;

      background: #f3f7f5;

      border: 1px solid var(--border);

      cursor: zoom-in;
    }

    .organization-image {
      width: 100%;

      height: auto;

      min-height: 350px;

      object-fit: contain;

      transition: transform 0.5s ease;
    }

    .organization-image-container:hover .organization-image {
      transform: scale(1.01);
    }

    /* IMAGE OVERLAY */

    .image-overlay {
      position: absolute;

      inset: 0;

      display: flex;

      align-items: center;

      justify-content: center;

      background: rgba(8, 127, 91, 0);

      opacity: 0;

      transition: var(--transition);

      pointer-events: none;
    }

    .organization-image-container:hover .image-overlay {
      opacity: 1;

      background: rgba(8, 127, 91, 0.08);
    }

    .image-overlay-icon {
      width: 55px;
      height: 55px;

      border-radius: 50%;

      background: white;

      color: var(--primary);

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 20px;

      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    /* CAPTION */

    .image-caption {
      padding: 18px 12px 7px;

      text-align: center;

      font-size: 12px;

      color: #7a8a84;
    }

    .image-caption strong {
      color: var(--dark);
    }

    /* =========================================================
           INFORMATION SECTION
        ========================================================= */

    .information-section {
      background: var(--light);
    }

    .information-grid {
      display: grid;

      grid-template-columns: repeat(3, 1fr);

      gap: 20px;
    }

    .information-card {
      padding: 30px;

      background: white;

      border: 1px solid var(--border);

      border-radius: 20px;

      transition: var(--transition);
    }

    .information-card:hover {
      transform: translateY(-7px);

      border-color: transparent;

      box-shadow: var(--shadow);
    }

    .information-icon {
      width: 55px;
      height: 55px;

      border-radius: 16px;

      background: var(--primary-light);

      color: var(--primary);

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 23px;

      margin-bottom: 18px;
    }

    .information-card h3 {
      font-size: 17px;

      margin-bottom: 9px;
    }

    .information-card p {
      font-size: 13px;
    }

    /* =========================================================
           ORGANIZATION DESCRIPTION
        ========================================================= */

    .description-section {
      background: white;
    }

    .description-grid {
      display: grid;

      grid-template-columns: 1fr 1fr;

      gap: 60px;

      align-items: center;
    }

    .description-content h2 {
      font-size: clamp(30px, 4vw, 43px);

      margin-bottom: 18px;
    }

    .description-content>p {
      margin-bottom: 22px;

      font-size: 14px;
    }

    .description-list {
      display: grid;

      gap: 14px;
    }

    .description-list li {
      display: flex;

      gap: 12px;

      align-items: flex-start;

      font-size: 13px;
    }

    .description-check {
      width: 24px;
      height: 24px;

      flex-shrink: 0;

      border-radius: 50%;

      background: var(--primary-light);

      color: var(--primary);

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 11px;

      font-weight: 800;
    }

    /* SIDE BOX */

    .description-side {
      position: relative;

      padding: 40px;

      min-height: 350px;

      border-radius: 25px;

      overflow: hidden;

      background: linear-gradient(145deg,
          var(--primary),
          var(--primary-dark));

      box-shadow: 0 25px 60px rgba(8, 127, 91, 0.18);
    }

    .description-side::before {
      content: "";

      position: absolute;

      width: 300px;
      height: 300px;

      border-radius: 50%;

      border: 55px solid rgba(255, 255, 255, 0.05);

      right: -130px;
      top: -100px;
    }

    .description-side::after {
      content: "";

      position: absolute;

      width: 200px;
      height: 200px;

      border-radius: 50%;

      border: 35px solid rgba(244, 185, 66, 0.06);

      left: -90px;
      bottom: -100px;
    }

    .description-side-content {
      position: relative;

      z-index: 2;
    }

    .description-side-icon {
      width: 65px;
      height: 65px;

      border-radius: 18px;

      background: rgba(255, 255, 255, 0.12);

      color: white;

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 28px;

      margin-bottom: 25px;
    }

    .description-side h3 {
      color: white;

      font-size: 25px;

      margin-bottom: 12px;
    }

    .description-side p {
      color: #d5e9e2;

      font-size: 13px;
    }

    /*INI AKHIR CSS UNTUK STRUKTUR ORGANISASI*/


    /* =========================================================
           CTA
        ========================================================= */

    .cta {
      padding: 80px 0;
    }

    .cta-box {
      position: relative;
      overflow: hidden;

      padding: 65px;

      border-radius: 30px;

      background: linear-gradient(135deg, var(--dark), var(--primary-dark));

      text-align: center;

      color: white;
    }

    .cta-box::before {
      content: "";

      position: absolute;

      width: 350px;
      height: 350px;

      border-radius: 50%;

      border: 70px solid rgba(255, 255, 255, 0.03);

      left: -130px;
      bottom: -200px;
    }

    .cta-box h2 {
      color: white;
      font-size: clamp(30px, 4vw, 43px);
      margin-bottom: 12px;
    }

    .cta-box p {
      max-width: 620px;
      margin: 0 auto 25px;
      color: #d5e9e2;
    }

    /* =========================================================
           FOOTER
        ========================================================= */

    footer {
      background: #0d2b21;
      color: #b7cec5;
    }

    .footer-main {
      padding: 70px 0 45px;

      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr 1fr;
      gap: 45px;
    }

    .footer-brand p {
      max-width: 320px;
      font-size: 13px;
      margin: 17px 0;
    }

    .footer-logo strong {
      color: white;
    }

    .footer-title {
      color: white;
      font-size: 14px;
      margin-bottom: 17px;
    }

    .footer-links {
      display: grid;
      gap: 10px;
    }

    .footer-links a {
      font-size: 12px;
      transition: var(--transition);
    }

    .footer-links a:hover {
      color: var(--secondary);
      transform: translateX(3px);
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      padding: 20px 0;

      display: flex;
      justify-content: space-between;
      align-items: center;

      font-size: 11px;
    }

    /* =========================================================
           BACK TO TOP
        ========================================================= */

    .back-top {
      position: fixed;

      right: 25px;
      bottom: 25px;

      width: 45px;
      height: 45px;

      border: none;
      border-radius: 12px;

      background: var(--primary);
      color: white;

      cursor: pointer;

      opacity: 0;
      visibility: hidden;

      transform: translateY(10px);

      transition: var(--transition);

      z-index: 900;
    }

    .back-top.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    /* =========================================================
           RESPONSIVE
        ========================================================= */

    @media (max-width: 1100px) {
      .nav-link {
        padding: 0 8px;
        font-size: 12px;
      }

      .nav-cta {
        display: none;
      }

      .hero-grid {
        gap: 40px;
      }

      .service-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 900px) {
      .topbar {
        display: none;
      }

      .nav-inner {
        min-height: 72px;
      }

      .menu-toggle {
        display: block;
      }

      .nav-menu {
        position: absolute;

        top: 100%;
        left: 0;
        right: 0;

        display: none;

        background: white;

        border-top: 1px solid var(--border);

        padding: 10px 20px 25px;

        max-height: calc(100vh - 72px);
        overflow-y: auto;

        box-shadow: 0 20px 30px rgba(0, 0, 0, 0.08);
      }

      .nav-menu.active {
        display: block;
      }

      .nav-item {
        border-bottom: 1px solid #edf2f0;
      }

      .nav-link {
        min-height: 48px;
        padding: 0;
        justify-content: space-between;
      }

      .dropdown {
        position: static;

        width: 100%;

        display: none;

        opacity: 1;
        visibility: visible;
        transform: none;

        box-shadow: none;
        border: 0;

        padding: 0 0 8px 15px;
      }

      .nav-item.open>.dropdown {
        display: block;
      }

      .dropdown-item.open>.dropdown {
        display: block;
      }

      .dropdown-link {
        min-height: 42px;
      }

      .hero {
        min-height: auto;
        padding: 80px 0 100px;
      }

      .hero-grid {
        grid-template-columns: 1fr;
      }

      .hero-visual {
        min-height: 430px;
      }

      .about-grid {
        grid-template-columns: 1fr;
      }

      .program-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .footer-main {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 650px) {
      .container {
        width: min(100% - 28px, 1180px);
      }

      .section {
        padding: 70px 0;
      }

      .hero {
        padding: 60px 0 80px;
      }

      .hero h1 {
        font-size: 40px;
        letter-spacing: -1px;
      }

      .hero-description {
        font-size: 15px;
      }

      .hero-visual {
        min-height: 390px;
      }

      .hero-card-main {
        height: 360px;
      }

      .floating-card.one {
        right: -5px;
        top: 35px;
      }

      .floating-card.two {
        left: -5px;
        bottom: 30px;
      }

      .stats {
        margin-top: -30px;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .stat {
        border-bottom: 1px solid var(--border);
      }

      .stat:nth-child(2) {
        border-right: 0;
      }

      .stat:nth-child(3),
      .stat:nth-child(4) {
        border-bottom: 0;
      }

      .program-grid {
        grid-template-columns: 1fr;
      }

      .service-grid {
        grid-template-columns: 1fr;
      }

      .student-grid {
        grid-template-columns: 1fr;
      }

      .cta-box {
        padding: 45px 25px;
      }

      .footer-main {
        grid-template-columns: 1fr;
        gap: 30px;
      }

      .footer-bottom {
        flex-direction: column;
        gap: 8px;
        text-align: center;
      }
    }

    /* ==========================================
   PREMIUM HERO SLIDER
========================================== */

    .hero-slider {
      position: relative;

      width: 100%;

      overflow: hidden;

      background: #071d16;
    }

    /* ==========================================
   CONTAINER
========================================== */

    .slider-container {
      position: relative;

      width: 100%;

      height: min(720px, calc(100vh - 78px));

      min-height: 600px;

      overflow: hidden;
    }

    /* ==========================================
   SLIDE
========================================== */

    .slide {
      position: absolute;

      inset: 0;

      width: 100%;

      height: 100%;

      opacity: 0;

      visibility: hidden;

      transition:
        opacity 1s ease,
        visibility 1s ease;
    }

    .slide.active {
      opacity: 1;

      visibility: visible;
    }

    /* ==========================================
   IMAGE
========================================== */

    .slide img {
      position: absolute;

      inset: 0;

      width: 100%;

      height: 100%;

      object-fit: cover;

      object-position: center;

      transform: scale(1.08);

      transition: transform 7s cubic-bezier(0.2, 0.7, 0.2, 1);
    }

    .slide.active img {
      transform: scale(1);
    }

    /* ==========================================
   PREMIUM OVERLAY
========================================== */

    .slide-overlay {
      position: absolute;

      inset: 0;

      background: linear-gradient(180deg,
          rgba(3, 20, 14, 0.08) 0%,
          rgba(3, 20, 14, 0.15) 25%,
          rgba(3, 20, 14, 0.48) 55%,
          rgba(3, 20, 14, 0.92) 100%);
    }

    /* ==========================================
   EXTRA SIDE GRADIENT
========================================== */

    .slide-overlay::after {
      content: "";

      position: absolute;

      inset: 0;

      background: linear-gradient(90deg,
          rgba(0, 0, 0, 0.25),
          transparent 40%,
          transparent 70%,
          rgba(0, 0, 0, 0.18));
    }

    /* ==========================================
   CONTENT
========================================== */

    .slide-content {
      position: absolute;

      z-index: 3;

      left: 50%;

      bottom: 105px;

      width: min(920px, calc(100% - 40px));

      text-align: center;

      color: white;

      transform: translateX(-50%);
    }

    /* ==========================================
   LABEL
========================================== */

    .slide-label {
      display: inline-flex;

      align-items: center;

      gap: 8px;

      margin-bottom: 17px;

      padding: 8px 15px;

      border: 1px solid rgba(255, 255, 255, 0.28);

      border-radius: 50px;

      color: rgba(255, 255, 255, 0.95);

      background: rgba(255, 255, 255, 0.1);

      backdrop-filter: blur(12px);

      font-size: 9px;

      font-weight: 800;

      letter-spacing: 1.5px;
    }

    .slide-label::before {
      content: "";

      width: 6px;

      height: 6px;

      border-radius: 50%;

      background: #7de0bd;

      box-shadow: 0 0 12px #7de0bd;
    }

    /* ==========================================
   TITLE
========================================== */

    .slide-content h1 {
      max-width: 900px;

      margin: 0 auto 15px;

      color: white;

      font-family: "Plus Jakarta Sans", sans-serif;

      font-size: clamp(34px, 5vw, 64px);

      font-weight: 800;

      line-height: 1.08;

      letter-spacing: -1.8px;

      text-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
    }

    .slide-content h1 span {
      display: block;

      color: #a7ead2;
    }

    /* ==========================================
   DESCRIPTION
========================================== */

    .slide-content p {
      max-width: 670px;

      margin: 0 auto;

      color: rgba(255, 255, 255, 0.86);

      font-size: 13px;

      line-height: 1.8;

      text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    }

    /* ==========================================
   ACTION BUTTON
========================================== */

    .slide-actions {
      display: flex;

      justify-content: center;

      align-items: center;

      gap: 11px;

      margin-top: 25px;
    }

    .slide-btn {
      min-height: 48px;

      padding: 0 19px;

      display: inline-flex;

      align-items: center;

      justify-content: center;

      gap: 15px;

      border-radius: 11px;

      font-size: 11px;

      font-weight: 800;

      transition: all 0.3s ease;
    }

    .slide-btn i {
      font-size: 16px;

      font-style: normal;

      transition: transform 0.3s ease;
    }

    /* PRIMARY */

    .slide-btn-primary {
      color: #073d2d;

      background: white;

      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .slide-btn-primary:hover {
      transform: translateY(-3px);

      box-shadow: 0 18px 35px rgba(0, 0, 0, 0.22);
    }

    .slide-btn-primary:hover i {
      transform: translateX(4px);
    }

    /* OUTLINE */

    .slide-btn-outline {
      color: white;

      border: 1px solid rgba(255, 255, 255, 0.45);

      background: rgba(255, 255, 255, 0.08);

      backdrop-filter: blur(10px);
    }

    .slide-btn-outline:hover {
      color: #073d2d;

      background: white;

      border-color: white;

      transform: translateY(-3px);
    }

    /* ==========================================
   ANIMATION CONTENT
========================================== */

    .slide.active .slide-label {
      animation: slideLabel 0.8s both;
    }

    .slide.active h1 {
      animation: slideTitle 0.9s 0.1s both;
    }

    .slide.active p {
      animation: slideText 0.9s 0.2s both;
    }

    .slide.active .slide-actions {
      animation: slideButtons 0.9s 0.3s both;
    }

    @keyframes slideLabel {
      from {
        opacity: 0;

        transform: translateY(20px);
      }

      to {
        opacity: 1;

        transform: translateY(0);
      }
    }

    @keyframes slideTitle {
      from {
        opacity: 0;

        transform: translateY(35px);
      }

      to {
        opacity: 1;

        transform: translateY(0);
      }
    }

    @keyframes slideText {
      from {
        opacity: 0;

        transform: translateY(25px);
      }

      to {
        opacity: 1;

        transform: translateY(0);
      }
    }

    @keyframes slideButtons {
      from {
        opacity: 0;

        transform: translateY(20px);
      }

      to {
        opacity: 1;

        transform: translateY(0);
      }
    }

    /* ==========================================
   NAVIGATION ARROWS
========================================== */

    .slider-btn {
      position: absolute;

      top: 50%;

      z-index: 10;

      width: 50px;

      height: 50px;

      display: flex;

      align-items: center;

      justify-content: center;

      border: 1px solid rgba(255, 255, 255, 0.25);

      border-radius: 50%;

      color: white;

      background: rgba(0, 0, 0, 0.15);

      backdrop-filter: blur(12px);

      font-size: 17px;

      cursor: pointer;

      transform: translateY(-50%);

      transition: 0.3s ease;
    }

    .slider-btn:hover {
      color: #073d2d;

      background: white;

      border-color: white;

      transform: translateY(-50%) scale(1.08);
    }

    .slider-prev {
      left: 28px;
    }

    .slider-next {
      right: 28px;
    }

    /* ==========================================
   BOTTOM
========================================== */

    .slider-bottom {
      position: absolute;

      left: 0;

      right: 0;

      bottom: 25px;

      z-index: 10;

      display: flex;

      align-items: center;

      justify-content: space-between;

      padding: 0 35px;
    }

    /* ==========================================
   DOTS
========================================== */

    .slider-dots {
      display: flex;

      align-items: center;

      gap: 7px;
    }

    .slider-dot {
      width: 7px;

      height: 7px;

      padding: 0;

      border: 0;

      border-radius: 20px;

      background: rgba(255, 255, 255, 0.35);

      cursor: pointer;

      transition: 0.4s ease;
    }

    .slider-dot.active {
      width: 30px;

      background: white;
    }

    /* ==========================================
   SCROLL
========================================== */

    .scroll-indicator {
      display: flex;

      align-items: center;

      gap: 12px;

      color: rgba(255, 255, 255, 0.65);

      font-size: 7px;

      font-weight: 700;

      letter-spacing: 1.3px;
    }

    .scroll-line {
      width: 45px;

      height: 1px;

      background: rgba(255, 255, 255, 0.5);
    }

    /* ==========================================
   TABLET
========================================== */

    @media (max-width: 900px) {
      .slider-container {
        height: 620px;

        min-height: 620px;
      }

      .slide-content {
        bottom: 95px;
      }

      .slide-content h1 {
        font-size: clamp(32px, 6vw, 48px);
      }

      .slider-btn {
        width: 42px;

        height: 42px;
      }

      .slider-prev {
        left: 15px;
      }

      .slider-next {
        right: 15px;
      }

      .scroll-indicator {
        display: none;
      }
    }

    /* ==========================================
   MOBILE
========================================== */

    @media (max-width: 600px) {
      .slider-container {
        height: 620px;

        min-height: 620px;
      }

      .slide img {
        object-position: center center;
      }

      .slide-content {
        bottom: 90px;

        width: calc(100% - 35px);
      }

      .slide-label {
        margin-bottom: 13px;

        padding: 7px 12px;

        font-size: 8px;
      }

      .slide-content h1 {
        margin-bottom: 12px;

        font-size: 29px;

        line-height: 1.16;

        letter-spacing: -0.8px;
      }

      .slide-content p {
        font-size: 11px;

        line-height: 1.65;
      }

      .slide-actions {
        flex-direction: column;

        width: 100%;

        margin-top: 20px;
      }

      .slide-btn {
        width: 100%;

        max-width: 240px;

        min-height: 45px;
      }

      .slider-btn {
        top: 45%;

        width: 36px;

        height: 36px;

        font-size: 13px;
      }

      .slider-prev {
        left: 8px;
      }

      .slider-next {
        right: 8px;
      }

      .slider-bottom {
        justify-content: center;

        padding: 0;

        bottom: 25px;
      }
    }

    /*MAP PETA*/
    /* =========================================================
   FOOTER LOCATION
========================================================== */

    .footer-location {
      grid-column: 1 / -1;
    }

    /* =========================================================
   LOCATION HEADER
========================================================== */

    .location-header {
      display: flex;

      align-items: center;

      gap: 12px;

      margin-bottom: 18px;
    }

    .location-icon {
      width: 42px;

      height: 42px;

      display: flex;

      align-items: center;

      justify-content: center;

      flex-shrink: 0;

      border-radius: 12px;

      color: var(--dark);

      background: var(--secondary);

      font-size: 16px;
    }

    .location-header h3 {
      margin: 0 0 3px;

      color: white;

      font-size: 13px;

      font-weight: 800;
    }

    .location-header p {
      margin: 0;

      color: rgba(255, 255, 255, 0.5);

      font-size: 9px;
    }

    /* =========================================================
   MAP CARD
========================================================== */

    .map-card {
      position: relative;

      width: 100%;

      height: 210px;

      overflow: hidden;

      margin-bottom: 20px;

      border: 1px solid rgba(255, 255, 255, 0.1);

      border-radius: 16px;

      background: rgba(255, 255, 255, 0.05);

      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    /* =========================================================
   GOOGLE MAP
========================================================== */

    .map-card iframe {
      position: absolute;

      inset: 0;

      width: 100%;

      height: 100%;

      border: 0;

      filter: saturate(0.85) contrast(1.02);
    }

    /* =========================================================
   MAP GRADIENT
========================================================== */

    .map-card::after {
      content: "";

      position: absolute;

      inset: 0;

      pointer-events: none;

      background: linear-gradient(180deg,
          rgba(3, 25, 18, 0.05) 35%,
          rgba(3, 25, 18, 0.75) 100%);
    }

    /* =========================================================
   MAP INFO
========================================================== */

    .map-overlay {
      position: absolute;

      left: 12px;

      right: 12px;

      bottom: 12px;

      z-index: 5;

      display: flex;

      align-items: center;

      justify-content: space-between;

      gap: 10px;
    }

    /* =========================================================
   MAP INFO CARD
========================================================== */

    .map-info {
      display: flex;

      align-items: center;

      gap: 9px;

      min-width: 0;

      padding: 9px 11px;

      border: 1px solid rgba(255, 255, 255, 0.16);

      border-radius: 10px;

      background: rgba(5, 35, 26, 0.78);

      backdrop-filter: blur(10px);
    }

    .map-info-icon {
      width: 30px;

      height: 30px;

      display: flex;

      align-items: center;

      justify-content: center;

      flex-shrink: 0;

      border-radius: 8px;

      color: var(--dark);

      background: var(--secondary);

      font-size: 11px;
    }

    .map-info strong {
      display: block;

      max-width: 150px;

      overflow: hidden;

      color: white;

      font-size: 9px;

      font-weight: 800;

      white-space: nowrap;

      text-overflow: ellipsis;
    }

    .map-info span {
      display: block;

      margin-top: 2px;

      color: rgba(255, 255, 255, 0.55);

      font-size: 7px;
    }

    /* =========================================================
   MAP DIRECTION BUTTON
========================================================== */

    .map-direction {
      display: inline-flex;

      align-items: center;

      justify-content: center;

      gap: 6px;

      flex-shrink: 0;

      padding: 10px 12px;

      border-radius: 9px;

      color: var(--dark);

      background: white;

      font-size: 8px;

      font-weight: 800;

      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);

      transition: all 0.3s ease;
    }

    .map-direction:hover {
      color: white;

      background: var(--primary);

      transform: translateY(-2px);
    }

    .map-direction i {
      font-size: 9px;
    }

    /* =========================================================
   LOCATION CONTACT
========================================================== */

    .location-contact {
      margin-top: 5px;
    }

    .footer-container {
      position: relative;
      z-index: 2;

      max-width: 1250px;
      margin: auto;

      padding: 60px 30px 50px;

      display: grid;

      grid-template-columns:
        1.6fr 1fr 1fr 1fr 1.6fr;

      gap: 30px;

      align-items: start;
    }

    @media (max-width: 600px) {
      .footer-container {
        grid-template-columns:
          1.5fr 1fr 1fr;

        gap: 30px;
      }

      .map-card {
        height: 240px;
      }

      .map-overlay {
        flex-direction: column;

        align-items: stretch;
      }

      .map-info {
        width: 100%;
      }

      .map-direction {
        width: 100%;
      }
    }

    /*MAP PETA*/



    /* =========================================================
   DETAIL PROGRAM STUDI
   Mengikuti template global Visi-Misi
   ========================================================= */

    .detail-prodi-page {
      background: #fff;
    }

    .prodi-page-hero {
      position: relative;
      overflow: hidden;
      background:
        radial-gradient(circle at 90% 15%, rgba(8, 127, 91, .16), transparent 30%),
        linear-gradient(135deg, #f5fbf8 0%, #ffffff 58%, #edf8f4 100%);
      padding: 58px 0 72px;
    }

    .prodi-page-hero::after {
      content: "";
      position: absolute;
      width: 360px;
      height: 360px;
      right: -150px;
      bottom: -190px;
      border-radius: 50%;
      background: rgba(244, 185, 66, .12);
    }

    .prodi-page-hero .container {
      position: relative;
      z-index: 1;
    }

    .prodi-breadcrumb {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 28px;
      font-size: 13px;
      color: #64766f;
    }

    .prodi-breadcrumb a:hover {
      color: var(--primary);
    }

    .prodi-hero-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 13px;
      padding: 8px 14px;
      border-radius: 50px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .5px;
    }

    .prodi-hero-label span {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--secondary);
    }

    .prodi-page-hero h1 {
      max-width: 850px;
      margin: 0 0 14px;
      font-size: clamp(38px, 5vw, 62px);
      letter-spacing: -1.5px;
    }

    .prodi-page-hero h1 span {
      color: var(--primary);
    }

    .prodi-page-hero p {
      max-width: 720px;
      margin: 0;
      color: var(--text);
      font-size: 16px;
    }

    /* Main profile */
    .prodi-detail-main {
      padding: 75px 0 25px;
    }

    .prodi-profile {
      display: grid;
      grid-template-columns: 285px minmax(0, 1fr) 275px;
      gap: 28px;
      align-items: stretch;
      padding: 25px;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 22px;
      box-shadow: var(--shadow);
    }

    .prodi-photo-frame {
      position: relative;
      min-height: 350px;
      overflow: hidden;
      border-radius: 22px;
      background: linear-gradient(145deg, #e9f8f4, #d9eee9);
      border: 9px solid #eff9f6;
      box-shadow: inset 0 0 0 1px #d6ebe5;
    }

    .prodi-photo-frame::before {
      content: "";
      position: absolute;
      width: 115px;
      height: 115px;
      top: -48px;
      left: -48px;
      border-radius: 50%;
      background: var(--primary);
      z-index: 1;
    }

    .prodi-photo-frame::after {
      content: "";
      position: absolute;
      width: 105px;
      height: 105px;
      right: -48px;
      bottom: -48px;
      border-radius: 50%;
      background: #13a878;
      z-index: 1;
    }

    .prodi-photo-frame img {
      position: relative;
      z-index: 2;
      width: 100%;
      height: 100%;
      min-height: 332px;
      object-fit: cover;
    }

    .prodi-placeholder {
      position: relative;
      z-index: 2;
      min-height: 332px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: var(--primary);
    }

    .prodi-placeholder strong {
      font-family: "Plus Jakarta Sans", sans-serif;
      font-size: 78px;
      line-height: 1;
      font-weight: 800;
    }

    .prodi-placeholder span {
      margin-top: 12px;
      color: var(--dark);
      font-size: 13px;
      font-weight: 600;
    }

    .prodi-status {
      position: absolute;
      z-index: 4;
      left: 18px;
      bottom: 17px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 13px;
      border-radius: 50px;
      background: var(--primary);
      color: #fff;
      font-size: 11px;
      font-weight: 800;
      box-shadow: 0 8px 20px rgba(8, 127, 91, .25);
    }

    .prodi-profile-content {
      padding: 22px 3px;
    }

    .prodi-eyebrow {
      display: inline-block;
      margin-bottom: 7px;
      color: var(--primary);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 1.5px;
    }

    .prodi-profile-content h2 {
      margin: 0;
      font-size: 31px;
    }

    .prodi-degree {
      margin: 5px 0 0;
      color: var(--primary);
      font-size: 14px;
      font-weight: 700;
    }

    .prodi-summary {
      margin-top: 17px;
      color: var(--text);
      font-size: 13px;
    }

    .prodi-info-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 11px;
      margin-top: 22px;
    }

    .prodi-info-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 12px;
      border: 1px solid #e0eee9;
      border-radius: 13px;
      background: var(--primary-light);
    }

    .prodi-info-icon {
      flex: 0 0 34px;
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      background: #fff;
      color: var(--primary);
      font-size: 14px;
      font-weight: 800;
    }

    .prodi-info-item small {
      display: block;
      margin-bottom: 2px;
      color: #70837c;
      font-size: 10px;
    }

    .prodi-info-item strong {
      display: block;
      color: var(--dark);
      font-size: 12px;
    }

    .prodi-button-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 22px;
    }

    .prodi-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: 10px 15px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 700;
      transition: var(--transition);
    }

    .prodi-btn-primary {
      background: var(--primary);
      color: #fff;
    }

    .prodi-btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .prodi-btn-outline {
      border: 1px solid var(--border);
      color: var(--dark);
      background: #fff;
    }

    .prodi-btn-outline:hover {
      border-color: var(--primary);
      color: var(--primary);
      transform: translateY(-2px);
    }

    /* Short information */
    .prodi-short-info {
      padding: 22px 20px;
      border-radius: 18px;
      background: var(--primary-light);
      border: 1px solid #dceee8;
    }

    .prodi-short-info h3 {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
      font-size: 15px;
    }

    .prodi-short-info h3 span {
      color: var(--primary);
    }

    .prodi-short-row {
      padding: 11px 0;
      border-bottom: 1px solid #d4e8e1;
    }

    .prodi-short-row:last-child {
      border-bottom: 0;
    }

    .prodi-short-row small {
      display: block;
      margin-bottom: 2px;
      color: #748a83;
      font-size: 10px;
    }

    .prodi-short-row strong {
      display: block;
      color: var(--dark);
      font-size: 12px;
      line-height: 1.45;
    }

    /* Sections */
    .prodi-section {
      padding: 75px 0 0;
    }

    .prodi-section-head {
      max-width: 720px;
      margin-bottom: 28px;
    }

    .prodi-section-label {
      display: inline-flex;
      padding: 8px 14px;
      margin-bottom: 12px;
      border-radius: 50px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
    }

    .prodi-section-head h2 {
      margin: 0 0 9px;
      font-size: clamp(27px, 3vw, 38px);
    }

    .prodi-section-head p {
      margin: 0;
      color: var(--text);
      font-size: 14px;
    }

    /* Visi */
    .prodi-visi {
      position: relative;
      overflow: hidden;
      padding: 35px 40px;
      border-radius: 22px;
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      color: #fff;
      box-shadow: 0 18px 45px rgba(8, 127, 91, .17);
    }

    .prodi-visi::after {
      content: "";
      position: absolute;
      width: 240px;
      height: 240px;
      right: -80px;
      top: -100px;
      border-radius: 50%;
      border: 35px solid rgba(255, 255, 255, .06);
    }

    .prodi-visi-inner {
      position: relative;
      z-index: 2;
      max-width: 900px;
    }

    .prodi-visi .prodi-section-label {
      background: rgba(255, 255, 255, .12);
      color: #fff;
    }

    .prodi-visi p {
      margin: 0;
      font-size: 17px;
      line-height: 1.85;
      color: #e5f5ef;
    }

    /* Misi */
    .prodi-misi-grid {
      display: grid;
      gap: 13px;
    }

    .prodi-misi-card {
      display: grid;
      grid-template-columns: 48px minmax(0, 1fr);
      gap: 15px;
      padding: 19px;
      border: 1px solid var(--border);
      border-radius: 15px;
      background: #fff;
      transition: var(--transition);
    }

    .prodi-misi-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 35px rgba(18, 55, 42, .07);
    }

    .prodi-misi-number {
      width: 43px;
      height: 43px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 13px;
      background: var(--primary-light);
      color: var(--primary);
      font-weight: 800;
    }

    .prodi-misi-card p {
      margin: 2px 0 0;
      color: var(--text);
      font-size: 13px;
    }

    /* CPL */
    .prodi-cpl-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 17px;
    }

    .prodi-cpl-card {
      padding: 22px;
      border: 1px solid var(--border);
      border-radius: 16px;
      background: #fff;
      transition: var(--transition);
    }

    .prodi-cpl-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 14px 35px rgba(18, 55, 42, .07);
    }

    .prodi-cpl-category {
      display: inline-block;
      margin-bottom: 11px;
      padding: 6px 10px;
      border-radius: 50px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .prodi-cpl-card p {
      margin: 0;
      color: var(--text);
      font-size: 13px;
    }

    /* Kurikulum */
    .prodi-table-wrap {
      overflow-x: auto;
      border: 1px solid var(--border);
      border-radius: 17px;
      background: #fff;
      box-shadow: 0 10px 30px rgba(18, 55, 42, .04);
    }

    .prodi-table {
      width: 100%;
      min-width: 760px;
      border-collapse: collapse;
    }

    .prodi-table th {
      padding: 14px 15px;
      background: var(--dark);
      color: #fff;
      text-align: left;
      font-size: 11px;
      font-weight: 700;
    }

    .prodi-table td {
      padding: 13px 15px;
      border-bottom: 1px solid #edf2f0;
      color: var(--text);
      font-size: 12px;
    }

    .prodi-table tbody tr:hover {
      background: #f5fbf8;
    }

    .prodi-table tbody tr:last-child td {
      border-bottom: 0;
    }

    .prodi-table .sks {
      color: var(--primary);
      font-weight: 800;
    }

    /* Fasilitas */
    .prodi-facility-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .prodi-facility-card {
      overflow: hidden;
      border: 1px solid var(--border);
      border-radius: 17px;
      background: #fff;
      transition: var(--transition);
    }

    .prodi-facility-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(18, 55, 42, .08);
    }

    .prodi-facility-image {
      height: 175px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 32px;
      font-weight: 800;
    }

    .prodi-facility-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .prodi-facility-content {
      padding: 18px;
    }

    .prodi-facility-content h3 {
      margin-bottom: 6px;
      font-size: 16px;
    }

    .prodi-facility-content p {
      margin: 0;
      color: var(--text);
      font-size: 12px;
    }

    /* Description / stats */
    .prodi-description-card {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 120px 120px;
      gap: 15px;
      align-items: center;
      margin-top: 75px;
      padding: 30px;
      border-radius: 20px;
      background: var(--light);
      border: 1px solid var(--border);
    }

    .prodi-description-card h2 {
      margin: 0 0 10px;
      font-size: 25px;
    }

    .prodi-description-card p {
      margin: 0;
      color: var(--text);
      font-size: 13px;
    }

    .prodi-stat {
      min-height: 105px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      border-radius: 15px;
      background: #fff;
      border: 1px solid var(--border);
      text-align: center;
    }

    .prodi-stat strong {
      color: var(--primary);
      font-family: "Plus Jakarta Sans", sans-serif;
      font-size: 27px;
      font-weight: 800;
    }

    .prodi-stat span {
      font-size: 11px;
      color: var(--text);
    }

    /* CTA */
    .prodi-cta-section {
      padding: 75px 0;
    }

    .prodi-cta-box {
      position: relative;
      overflow: hidden;
      padding: 45px 55px;
      border-radius: 24px;
      background: linear-gradient(135deg, var(--dark), var(--primary-dark));
      color: #fff;
    }

    .prodi-cta-box::after {
      content: "";
      position: absolute;
      width: 280px;
      height: 280px;
      right: -110px;
      top: -140px;
      border-radius: 50%;
      border: 45px solid rgba(255, 255, 255, .05);
    }

    .prodi-cta-content {
      position: relative;
      z-index: 2;
      max-width: 760px;
    }

    .prodi-cta-content .prodi-section-label {
      background: rgba(255, 255, 255, .10);
      color: #fff;
    }

    .prodi-cta-content h2 {
      margin: 0 0 9px;
      color: #fff;
      font-size: clamp(25px, 3vw, 35px);
    }

    .prodi-cta-content p {
      margin: 0 0 20px;
      color: #d6eee5;
      font-size: 13px;
    }

    .prodi-cta-button {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 18px;
      border-radius: 10px;
      background: #fff;
      color: var(--primary-dark);
      font-size: 12px;
      font-weight: 800;
      transition: var(--transition);
    }

    .prodi-cta-button:hover {
      transform: translateY(-2px);
    }

    /* Empty */
    .prodi-empty {
      padding: 25px;
      border: 1px dashed #cfe2dc;
      border-radius: 14px;
      color: var(--text);
      background: #fbfefd;
      text-align: center;
      font-size: 13px;
    }

    /* Responsive */
    @media (max-width: 1050px) {
      .prodi-profile {
        grid-template-columns: 235px minmax(0, 1fr);
      }

      .prodi-short-info {
        grid-column: 1 / -1;
      }

      .prodi-photo-frame {
        min-height: 310px;
      }

      .prodi-photo-frame img,
      .prodi-placeholder {
        min-height: 292px;
      }

      .prodi-facility-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .prodi-description-card {
        grid-template-columns: minmax(0, 1fr) 120px 120px;
      }
    }

    @media (max-width: 900px) {
      .prodi-page-hero {
        padding: 45px 0 58px;
      }

      .prodi-detail-main {
        padding-top: 55px;
      }

      .prodi-profile {
        grid-template-columns: 1fr;
      }

      .prodi-photo-frame {
        max-width: 360px;
        width: 100%;
        margin: auto;
      }

      .prodi-short-info {
        grid-column: auto;
      }

      .prodi-cpl-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 650px) {
      .prodi-page-hero h1 {
        font-size: 39px;
      }

      .prodi-page-hero p {
        font-size: 13px;
      }

      .prodi-profile {
        padding: 15px;
        border-radius: 18px;
      }

      .prodi-profile-content {
        padding: 8px 4px;
      }

      .prodi-profile-content h2 {
        font-size: 27px;
      }

      .prodi-info-grid {
        grid-template-columns: 1fr;
      }

      .prodi-visi {
        padding: 28px 24px;
      }

      .prodi-visi p {
        font-size: 14px;
      }

      .prodi-facility-grid {
        grid-template-columns: 1fr;
      }

      .prodi-description-card {
        grid-template-columns: 1fr 1fr;
      }

      .prodi-description-card>div:first-child {
        grid-column: 1 / -1;
      }

      .prodi-cta-box {
        padding: 35px 25px;
      }
    }

    @media (max-width: 430px) {
      .prodi-description-card {
        grid-template-columns: 1fr;
      }

      .prodi-description-card>div:first-child {
        grid-column: auto;
      }
    }
    </style>
  </head>

  <body class="detail-prodi-page">

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

        <a href="/tentang-fikes/index.php" class="logo">
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
            <a href="#" class="dropdown-link">
              Tentang FIKES
              <span class="arrow">▾</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="/tentang-fikes/visi-misi.php" class="dropdown-link"> Visi Misi </a>
              </div>

              <div class="dropdown-item">
                <a href="/tentang-fikes/struktur-organisasi.php" class="dropdown-link">
                  Struktur Organisasi
                </a>
              </div>

              <div class="dropdown-item">
                <a href="/tentang-fikes/sertifikat-akreditasi.php" class="dropdown-link">
                  Sertifikat Akreditasi
                </a>
              </div>

              <div class="dropdown-item">
                <a href="/tentang-fikes/unduh-logo.php" class="dropdown-link"> Unduh Logo </a>
              </div>

              <div class="dropdown-item">
                <a href="/dosen/dosen.php" class="dropdown-link"> Daftar Dosen </a>
              </div>
              <!-- DAFTAR DOSEN -->

              <!-- <div class="dropdown-item has-dropdown">
            <a href="#" class="dropdown-link">
              Daftar Dosen
              <span>›</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="/dosen/dosen.php" class="dropdown-link"> Keperawatan </a>
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

          <!-- KEMAHASISWAAN -->

          <div class="nav-item has-dropdown">
            <a href="#" class="nav-link">
              Kemahasiswaan
              <span class="arrow">▾</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="/page/tentang-fikes/himpunan-mahasiswa.php" class="dropdown-link">
                  Himpunan Mahasiswa
                </a>
              </div>

              <div class="dropdown-item">
                <a href="/page/tentang-fikes/unit-kegiatan-mahasiswa.php" class="dropdown-link">
                  Unit Kegiatan Mahasiswa
                </a>
              </div>
            </div>
          </div>

          <!-- PROGRAM VOKASI -->

          <div class="nav-item has-dropdown">
            <a href="#" class="nav-link">
              Program Vokasi
              <span class="arrow">▾</span>
            </a>

            <div class="dropdown">
              <!-- PROGRAM PROFESI -->

              <div class="dropdown-item has-dropdown">
                <a href="#" class="dropdown-link">
                  Program Profesi
                  <span>›</span>
                </a>

                <div class="dropdown">
                  <div class="dropdown-item">
                    <a href="/program-studi/program-studi.php" class="dropdown-link">
                      Profesi Ners
                    </a>
                  </div>
                </div>
              </div>

              <!-- PROGRAM SARJANA -->

              <div class="dropdown-item has-dropdown">
                <a href="#" class="dropdown-link">
                  Program Sarjana
                  <span>›</span>
                </a>

                <div class="dropdown">
                  <div class="dropdown-item">
                    <a href="/page/tentang-fikes/programsarjana-keperawatan.php" class="dropdown-link">
                      Ilmu Keperawatan (S.Kep)
                    </a>
                  </div>

                  <div class="dropdown-item">
                    <a href="page/tentang-fikes/programsarjana-farmasi.php" class="dropdown-link">
                      Farmasi (S.Farm)
                    </a>
                  </div>
                </div>
              </div>

              <!-- PROGRAM DIPLOMA -->

              <div class="dropdown-item has-dropdown">
                <a href="#" class="dropdown-link">
                  Program Diploma
                  <span>›</span>
                </a>

                <div class="dropdown">
                  <div class="dropdown-item">
                    <a href="#" class="dropdown-link">
                      Keperawatan (A.Md.Kep.)
                    </a>
                  </div>

                  <div class="dropdown-item">
                    <a href="#" class="dropdown-link">
                      Kebidanan (A.Md.Keb.)
                    </a>
                  </div>

                  <div class="dropdown-item">
                    <a href="#" class="dropdown-link">
                      Keselamatan dan Kesehatan Kerja (S.Tr.KKK.)
                    </a>
                  </div>
                </div>
              </div>
            </div>
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

    <main>

      <!-- =====================================================
       HERO PROGRAM STUDI
       ===================================================== -->
      <section class="prodi-page-hero">
        <div class="container">

          <div class="prodi-breadcrumb">
            <a href="../../index.php">Beranda</a>
            <span>›</span>
            <a href="program-studi.php">Program Studi</a>
            <span>›</span>
            <span><?= e($prodi['nama']) ?></span>
          </div>

          <div class="prodi-hero-label">
            <span></span>
            <?= e($prodi['jenjang']) ?> · <?= e($prodi['kode_prodi']) ?>
          </div>

          <h1>
            <?= e($prodi['nama']) ?>
          </h1>

          <p>
            Informasi lengkap mengenai profil, kurikulum, capaian pembelajaran,
            fasilitas, dan informasi akademik program studi.
          </p>

        </div>
      </section>


      <!-- =====================================================
       PROFIL PROGRAM STUDI
       ===================================================== -->
      <section class="prodi-detail-main">
        <div class="container">

          <div class="prodi-profile">

            <!-- FOTO -->
            <div class="prodi-photo-frame">

              <?php if ($foto): ?>
              <img src="<?= e($foto) ?>" alt="<?= e($prodi['nama']) ?>">
              <?php else: ?>
              <div class="prodi-placeholder">
                <strong><?= e(strtoupper(substr($prodi['nama'], 0, 1))) ?></strong>
                <span>FIKES</span>
              </div>
              <?php endif; ?>

              <span class="prodi-status">● Aktif</span>

            </div>


            <!-- INFORMASI UTAMA -->
            <div class="prodi-profile-content">

              <span class="prodi-eyebrow">PROGRAM STUDI</span>

              <h2><?= e($prodi['nama']) ?></h2>

              <p class="prodi-degree">
                <?= e($prodi['jenjang']) ?>
                <?php if (!empty($prodi['gelar'])): ?>
                · <?= e($prodi['gelar']) ?>
                <?php endif; ?>
              </p>

              <?php if (!empty($prodi['deskripsi'])): ?>
              <p class="prodi-summary">
                <?= nl2br(e($prodi['deskripsi'])) ?>
              </p>
              <?php endif; ?>

              <div class="prodi-info-grid">

                <div class="prodi-info-item">
                  <span class="prodi-info-icon">🎓</span>
                  <div>
                    <small>Jenjang</small>
                    <strong><?= e($prodi['jenjang']) ?></strong>
                  </div>
                </div>

                <div class="prodi-info-item">
                  <span class="prodi-info-icon">✓</span>
                  <div>
                    <small>Akreditasi</small>
                    <strong><?= e($prodi['akreditasi'] ?: 'Belum tersedia') ?></strong>
                  </div>
                </div>

                <div class="prodi-info-item">
                  <span class="prodi-info-icon">◷</span>
                  <div>
                    <small>Durasi Studi</small>
                    <strong><?= e($prodi['durasi_studi'] ?: 'Belum tersedia') ?></strong>
                  </div>
                </div>

                <div class="prodi-info-item">
                  <span class="prodi-info-icon">▣</span>
                  <div>
                    <small>SKS Lulus</small>
                    <strong>
                      <?= $prodi['sks_lulus'] !== null ? e($prodi['sks_lulus']) . ' SKS' : 'Belum tersedia' ?>
                    </strong>
                  </div>
                </div>

              </div>

              <div class="prodi-button-row">

                <?php if (!empty($prodi['kontak_email'])): ?>
                <a class="prodi-btn prodi-btn-primary" href="mailto:<?= e($prodi['kontak_email']) ?>">
                  ✉ Hubungi Prodi
                </a>
                <?php endif; ?>

                <?php if ($brosur): ?>
                <a class="prodi-btn prodi-btn-outline" href="<?= e($brosur) ?>" target="_blank" rel="noopener">
                  ↓ Unduh Brosur
                </a>
                <?php endif; ?>

                <a class="prodi-btn prodi-btn-outline" href="program-studi.php">
                  ← Kembali
                </a>

              </div>

            </div>


            <!-- INFORMASI SINGKAT -->
            <aside class="prodi-short-info">

              <h3>
                <span>♙</span>
                Informasi Singkat
              </h3>

              <div class="prodi-short-row">
                <small>Sekretaris Prodi</small>
                <strong><?= e($prodi['sekretaris_nama'] ?: '-') ?></strong>
              </div>

              <div class="prodi-short-row">
                <small>Program Studi</small>
                <strong><?= e($prodi['nama']) ?></strong>
              </div>

              <div class="prodi-short-row">
                <small>Kontak</small>
                <strong><?= e($prodi['kontak_telepon'] ?: '-') ?></strong>
              </div>

              <div class="prodi-short-row">
                <small>Email</small>
                <strong><?= e($prodi['kontak_email'] ?: '-') ?></strong>
              </div>

              <div class="prodi-short-row">
                <small>Dosen</small>
                <strong><?= (int)$prodi['jumlah_dosen'] ?> Orang</strong>
              </div>

              <div class="prodi-short-row">
                <small>Tenaga Kependidikan</small>
                <strong><?= (int)$prodi['jumlah_tenaga_kependidikan'] ?> Orang</strong>
              </div>

            </aside>

          </div>

        </div>
      </section>


      <!-- =====================================================
       VISI
       ===================================================== -->
      <section class="prodi-section">
        <div class="container">

          <div class="prodi-section-head">
            <span class="prodi-section-label">VISI</span>
            <h2>Arah Pengembangan Program Studi</h2>
          </div>

          <div class="prodi-visi">
            <div class="prodi-visi-inner">

              <p>
                <?= $prodi['visi']
                ? nl2br(e($prodi['visi']))
                : 'Visi program studi belum tersedia.' ?>
              </p>

            </div>
          </div>

        </div>
      </section>


      <!-- =====================================================
       MISI
       ===================================================== -->
      <section class="prodi-section">
        <div class="container">

          <div class="prodi-section-head">
            <span class="prodi-section-label">MISI</span>
            <h2>Misi Program Studi</h2>
            <p>
              Komitmen program studi dalam mendukung pencapaian visi dan
              pengembangan pendidikan kesehatan.
            </p>
          </div>

          <?php if ($misi): ?>

          <div class="prodi-misi-grid">

            <?php foreach ($misi as $i => $item): ?>

            <article class="prodi-misi-card">

              <div class="prodi-misi-number">
                <?= sprintf('%02d', $i + 1) ?>
              </div>

              <p><?= nl2br(e($item['isi'])) ?></p>

            </article>

            <?php endforeach; ?>

          </div>

          <?php else: ?>

          <div class="prodi-empty">
            Data misi program studi belum tersedia.
          </div>

          <?php endif; ?>

        </div>
      </section>


      <!-- =====================================================
       CAPAIAN PEMBELAJARAN
       ===================================================== -->
      <section class="prodi-section">
        <div class="container">

          <div class="prodi-section-head">
            <span class="prodi-section-label">CAPAIAN PEMBELAJARAN</span>
            <h2>Capaian Pembelajaran Lulusan</h2>
            <p>
              Capaian pembelajaran yang menjadi dasar pengembangan kompetensi
              mahasiswa program studi.
            </p>
          </div>

          <?php if ($cpl): ?>

          <div class="prodi-cpl-grid">

            <?php foreach ($cpl as $item): ?>

            <article class="prodi-cpl-card">

              <span class="prodi-cpl-category">
                <?= e($item['kategori']) ?>
              </span>

              <p>
                <?= nl2br(e($item['isi'])) ?>
              </p>

            </article>

            <?php endforeach; ?>

          </div>

          <?php else: ?>

          <div class="prodi-empty">
            Data capaian pembelajaran belum tersedia.
          </div>

          <?php endif; ?>

        </div>
      </section>


      <!-- =====================================================
       KURIKULUM
       ===================================================== -->
      <section class="prodi-section">
        <div class="container">

          <div class="prodi-section-head">
            <span class="prodi-section-label">KURIKULUM</span>
            <h2>Struktur Kurikulum</h2>
            <p>
              Daftar mata kuliah yang menjadi bagian dari struktur kurikulum
              program studi.
            </p>
          </div>

          <?php if ($kurikulum): ?>

          <div class="prodi-table-wrap">

            <table class="prodi-table">

              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode</th>
                  <th>Mata Kuliah</th>
                  <th>Semester</th>
                  <th>SKS</th>
                  <th>Jenis</th>
                </tr>
              </thead>

              <tbody>

                <?php foreach ($kurikulum as $i => $item): ?>

                <tr>
                  <td><?= $i + 1 ?></td>

                  <td>
                    <?= e($item['kode_mk'] ?: '-') ?>
                  </td>

                  <td>
                    <strong><?= e($item['nama_mk']) ?></strong>
                  </td>

                  <td>
                    <?= e($item['semester'] ?: '-') ?>
                  </td>

                  <td class="sks">
                    <?= e($item['sks']) ?>
                  </td>

                  <td>
                    <?= e($item['jenis'] ?: '-') ?>
                  </td>
                </tr>

                <?php endforeach; ?>

              </tbody>

            </table>

          </div>

          <?php else: ?>

          <div class="prodi-empty">
            Data kurikulum belum tersedia.
          </div>

          <?php endif; ?>

        </div>
      </section>


      <!-- =====================================================
       FASILITAS
       ===================================================== -->
      <section class="prodi-section">
        <div class="container">

          <div class="prodi-section-head">
            <span class="prodi-section-label">FASILITAS</span>
            <h2>Fasilitas Pendukung Pembelajaran</h2>
            <p>
              Fasilitas yang mendukung kegiatan akademik dan pembelajaran
              mahasiswa.
            </p>
          </div>

          <?php if ($fasilitas): ?>

          <div class="prodi-facility-grid">

            <?php foreach ($fasilitas as $i => $item): ?>

            <?php
              $ffoto = '';

              if (!empty($item['gambar'])) {
                $ffoto = '../../admin/uploads/program-studi/' .
                  rawurlencode($item['gambar']);
              }
              ?>

            <article class="prodi-facility-card">

              <div class="prodi-facility-image">

                <?php if ($ffoto): ?>

                <img src="<?= e($ffoto) ?>" alt="<?= e($item['nama_fasilitas']) ?>">

                <?php else: ?>

                <?= sprintf('%02d', $i + 1) ?>

                <?php endif; ?>

              </div>

              <div class="prodi-facility-content">

                <h3><?= e($item['nama_fasilitas']) ?></h3>

                <?php if (!empty($item['deskripsi'])): ?>
                <p><?= nl2br(e($item['deskripsi'])) ?></p>
                <?php endif; ?>

              </div>

            </article>

            <?php endforeach; ?>

          </div>

          <?php else: ?>

          <div class="prodi-empty">
            Data fasilitas belum tersedia.
          </div>

          <?php endif; ?>

        </div>
      </section>


      <!-- =====================================================
       DESKRIPSI + STATISTIK
       ===================================================== -->
      <section class="container">

        <div class="prodi-description-card">

          <div>

            <span class="prodi-eyebrow">
              TENTANG PROGRAM STUDI
            </span>

            <h2>Profil Program Studi</h2>

            <p>
              <?= $prodi['deskripsi']
              ? nl2br(e($prodi['deskripsi']))
              : 'Deskripsi program studi belum tersedia.' ?>
            </p>

          </div>

          <div class="prodi-stat">
            <strong><?= (int)$prodi['jumlah_dosen'] ?></strong>
            <span>Dosen</span>
          </div>

          <div class="prodi-stat">
            <strong>
              <?= $prodi['sks_lulus'] !== null ? e($prodi['sks_lulus']) : '0' ?>
            </strong>
            <span>Total SKS</span>
          </div>

        </div>

      </section>


      <!-- =====================================================
       CTA
       ===================================================== -->
      <section class="prodi-cta-section">

        <div class="container">

          <div class="prodi-cta-box">

            <div class="prodi-cta-content">

              <span class="prodi-section-label">
                BERSAMA FIKES
              </span>

              <h2>
                Kenali Program Studi <?= e($prodi['nama']) ?> Lebih Dekat
              </h2>

              <p>
                Hubungi program studi untuk mendapatkan informasi akademik
                lebih lanjut.
              </p>

              <?php if (!empty($prodi['kontak_email'])): ?>

              <a class="prodi-cta-button" href="mailto:<?= e($prodi['kontak_email']) ?>">
                Hubungi Prodi →
              </a>

              <?php else: ?>

              <a class="prodi-cta-button" href="program-studi.php">
                Kembali ke Program Studi →
              </a>

              <?php endif; ?>

            </div>

          </div>

        </div>

      </section>

    </main>

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

    <script>
    /* =========================================================
   NAVBAR SCROLL EFFECT
========================================================= */

    /* NAVBAR SCROLL */

    const navbar = document.getElementById("navbar");

    window.addEventListener("scroll", () => {
      if (window.scrollY > 20) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });

    /* MOBILE MENU */

    const menuToggle = document.getElementById("menuToggle");

    const navMenu = document.getElementById("navMenu");

    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active");

      menuToggle.innerHTML = navMenu.classList.contains("active") ?
        "✕" :
        "☰";
    });

    /* MOBILE DROPDOWN */

    document
      .querySelectorAll(
        ".has-dropdown > .nav-link, .has-dropdown > .dropdown-link",
      )
      .forEach((link) => {
        link.addEventListener("click", function(e) {
          if (window.innerWidth <= 900) {
            e.preventDefault();

            const parent = this.parentElement;

            parent.classList.toggle("open");
          }
        });
      });

    /* CLOSE MOBILE MENU */

    document.querySelectorAll(".nav-menu a").forEach((link) => {
      link.addEventListener("click", function() {
        if (
          window.innerWidth <= 900 &&
          !this.parentElement.classList.contains("has-dropdown")
        ) {
          navMenu.classList.remove("active");

          menuToggle.innerHTML = "☰";
        }
      });
    });

    /* BACK TO TOP */

    const backTop = document.getElementById("backTop");

    window.addEventListener("scroll", () => {
      if (window.scrollY > 500) {
        backTop.classList.add("show");
      } else {
        backTop.classList.remove("show");
      }
    });

    backTop.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });

    /* YEAR */

    document.getElementById("year").textContent = new Date().getFullYear();

    /* CLOSE DROPDOWN */

    document.addEventListener("click", (event) => {
      if (!event.target.closest(".navbar")) {
        document.querySelectorAll(".nav-item.open").forEach((item) => {
          item.classList.remove("open");
        });
      }
    });
    </script>

  </body>

</html>
