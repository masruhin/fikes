<?php
require_once __DIR__ . '/../../admin/config/database.php';

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* =========================================================
   FILTER & SEARCH
   ========================================================= */
$filter = trim($_GET['jenjang'] ?? '');
$search = trim($_GET['search'] ?? '');

$where = ["status = 'aktif'"];
$params = [];

if ($filter !== '') {
  $where[] = "jenjang = :jenjang";
  $params[':jenjang'] = $filter;
}

if ($search !== '') {
  $where[] = "(nama LIKE :search_nama OR kode_prodi LIKE :search_kode)";
  $params[':search_nama'] = '%' . $search . '%';
  $params[':search_kode'] = '%' . $search . '%';
}

/* =========================================================
   QUERY PROGRAM STUDI
   ========================================================= */
$sql = "
    SELECT
        id,
        kode_prodi,
        nama,
        jenjang,
        gelar,
        deskripsi,
        akreditasi,
        foto,
        brosur
    FROM program_studi
    WHERE " . implode(' AND ', $where) . "
    ORDER BY nama ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programs = $stmt->fetchAll();

/* =========================================================
   DAFTAR JENJANG
   ========================================================= */
$jenjangList = $pdo->query("
    SELECT DISTINCT jenjang
    FROM program_studi
    WHERE status = 'aktif'
      AND jenjang IS NOT NULL
      AND jenjang <> ''
    ORDER BY jenjang
")->fetchAll(PDO::FETCH_COLUMN);

/* Jumlah program */
$totalProgram = count($programs);
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Program Studi | FIKES</title>

  <meta name="description"
    content="Daftar Program Studi Fakultas Ilmu Kesehatan. Temukan program pendidikan yang sesuai dengan minat dan tujuan karier Anda.">

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

    button,
    input {
      font: inherit;
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
      background: transparent;
      border: 0;
      cursor: pointer;
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

    .dropdown-item>.dropdown {
      left: calc(100% + 5px);
      top: -10px;
    }

    .dropdown-item:hover>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

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
           PAGE HERO
        ========================================================= */
    .page-hero {
      position: relative;
      padding: 100px 0 105px;
      overflow: hidden;
      background:
        radial-gradient(circle at 85% 20%, rgba(8, 127, 91, 0.14), transparent 30%),
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

    .hero-label,
    .eyebrow {
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

    .hero-label::before,
    .eyebrow::before {
      content: "";
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

    /* =========================================================
           PROGRAM SECTION
        ========================================================= */
    .program-section {
      padding: 95px 0;
      background: white;
    }

    .section-heading {
      max-width: 720px;
      margin: 0 auto 40px;
      text-align: center;
    }

    .section-heading h2 {
      font-size: clamp(32px, 4vw, 45px);
      margin-bottom: 15px;
    }

    .section-heading p {
      font-size: 15px;
    }

    /* =========================================================
           FILTER
        ========================================================= */
    .filter-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      margin-bottom: 35px;
      padding: 14px;
      border: 1px solid var(--border);
      border-radius: 18px;
      background: var(--light);
    }

    .filter-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .filter-tabs a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      padding: 0 15px;
      border-radius: 10px;
      color: var(--text);
      font-size: 12px;
      font-weight: 700;
      transition: var(--transition);
    }

    .filter-tabs a:hover {
      color: var(--primary);
      background: var(--primary-light);
    }

    .filter-tabs a.active {
      color: white;
      background: var(--primary);
      box-shadow: 0 8px 20px rgba(8, 127, 91, 0.18);
    }

    .search-box {
      width: 280px;
      flex-shrink: 0;
      position: relative;
    }

    .search-box span {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
      font-size: 18px;
      pointer-events: none;
    }

    .search-box input {
      width: 100%;
      height: 42px;
      padding: 0 14px 0 42px;
      border: 1px solid var(--border);
      border-radius: 10px;
      outline: none;
      background: white;
      color: var(--dark);
      font-size: 12px;
      transition: var(--transition);
    }

    .search-box input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(8, 127, 91, 0.08);
    }

    /* =========================================================
           PROGRAM GRID / CARD
        ========================================================= */
    .program-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }

    /* =========================================================
           PROGRAM CARD - MODERN / LEBIH HIDUP
        ========================================================= */
    .program-card {
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(229, 236, 233, .95);
      border-radius: 24px;
      background: #fff;
      box-shadow: 0 12px 35px rgba(18, 55, 42, .055);
      transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
    }

    .program-card::before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), #21b887, var(--secondary));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform .35s ease;
      z-index: 8;
    }

    .program-card:hover {
      transform: translateY(-10px);
      border-color: rgba(8, 127, 91, .18);
      box-shadow: 0 25px 60px rgba(18, 55, 42, .13);
    }

    .program-card:hover::before {
      transform: scaleX(1);
    }

    .program-image {
      position: relative;
      height: 235px;
      overflow: hidden;
      background:
        radial-gradient(circle at 15% 15%, rgba(244, 185, 66, .25), transparent 25%),
        radial-gradient(circle at 85% 90%, rgba(255, 255, 255, .13), transparent 30%),
        linear-gradient(135deg, #087f5b, #056044);
    }

    .program-image::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg,
          rgba(18, 55, 42, .02) 30%,
          rgba(18, 55, 42, .72) 100%);
      pointer-events: none;
    }

    .program-image::before {
      content: "";
      position: absolute;
      width: 170px;
      height: 170px;
      border: 35px solid rgba(255, 255, 255, .055);
      border-radius: 50%;
      right: -70px;
      top: -85px;
      z-index: 2;
      transition: transform .5s ease;
    }

    .program-card:hover .program-image::before {
      transform: scale(1.15) rotate(12deg);
    }

    .program-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .6s ease, filter .4s ease;
    }

    .program-card:hover .program-image img {
      transform: scale(1.07);
      filter: saturate(1.08);
    }

    .image-placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(255, 255, 255, .96);
      font-family: "Plus Jakarta Sans", sans-serif;
      font-size: 74px;
      font-weight: 800;
      text-shadow: 0 10px 30px rgba(0, 0, 0, .15);
    }

    .jenjang-badge {
      position: absolute;
      top: 16px;
      right: 16px;
      z-index: 5;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 12px;
      border: 1px solid rgba(255, 255, 255, .2);
      border-radius: 999px;
      background: rgba(18, 55, 42, .84);
      color: white;
      font-size: 10px;
      font-weight: 800;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    }

    .jenjang-badge::before {
      content: "●";
      color: var(--secondary);
      font-size: 7px;
    }

    .program-body {
      position: relative;
      padding: 26px 25px 23px;
    }

    .program-body .code {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 9px;
      color: var(--primary);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 1.35px;
      text-transform: uppercase;
    }

    .program-body .code::before {
      content: "";
      width: 20px;
      height: 2px;
      border-radius: 99px;
      background: var(--secondary);
    }

    .program-body h3 {
      min-height: 50px;
      font-size: 19px;
      margin-bottom: 9px;
      letter-spacing: -.25px;
    }

    .program-body>p {
      min-height: 68px;
      margin-bottom: 17px;
      color: var(--text);
      font-size: 12px;
      line-height: 1.75;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .meta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
      min-height: 30px;
      margin-bottom: 20px;
    }

    .meta-row span {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 7px 10px;
      border: 1px solid #dcefe9;
      border-radius: 9px;
      background: linear-gradient(135deg, #f0faf7, #e7f7f1);
      color: var(--primary);
      font-size: 10px;
      font-weight: 800;
    }

    .detail-btn {
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 12px 14px;
      border-radius: 11px;
      background: var(--dark);
      color: white;
      font-size: 12px;
      font-weight: 800;
      transition: var(--transition);
    }

    .detail-btn::before {
      content: "";
      position: absolute;
      width: 0;
      height: 100%;
      left: 0;
      top: 0;
      background: linear-gradient(90deg, var(--primary), #13a878);
      transition: width .35s ease;
      z-index: 0;
    }

    .detail-btn:hover::before {
      width: 100%;
    }

    .detail-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(8, 127, 91, .18);
    }

    .detail-btn span {
      position: relative;
      z-index: 1;
      font-size: 16px;
    }

    .detail-btn span:first-child {
      font-size: 12px;
    }


    .program-card {
      animation: cardReveal .55s ease both;
    }

    .program-card:nth-child(2) {
      animation-delay: .06s;
    }

    .program-card:nth-child(3) {
      animation-delay: .12s;
    }

    .program-card:nth-child(4) {
      animation-delay: .18s;
    }

    .program-card:nth-child(5) {
      animation-delay: .24s;
    }

    .program-card:nth-child(6) {
      animation-delay: .30s;
    }

    @keyframes cardReveal {
      from {
        opacity: 0;
        transform: translateY(18px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .result-info {
      margin: 0 0 18px;
      color: var(--text);
      font-size: 12px;
    }

    .empty-state {
      padding: 55px 25px;
      border: 1px dashed var(--border);
      border-radius: 18px;
      background: var(--light);
      text-align: center;
      color: var(--text);
      font-size: 14px;
    }

    /* =========================================================
           CTA
        ========================================================= */
    .cta-section {
      padding: 0 0 95px;
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

    .cta .eyebrow {
      background: rgba(255, 255, 255, .1);
      color: #d9eee5;
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

    .cta .btn {
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

    .cta .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(244, 185, 66, 0.2);
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

    .footer-logo .logo-text small {
      color: var(--secondary);
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

    .footer-contact {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      margin-top: 10px;
      font-size: 11px;
    }

    .footer-contact i {
      color: var(--secondary);
      min-width: 14px;
    }

    /* =========================================================
           FOOTER LOCATION / MAP
        ========================================================= */
    .footer-location {
      grid-column: 1 / -1;
    }

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
      color: rgba(255, 255, 255, .5);
      font-size: 9px;
    }

    .map-card {
      position: relative;
      width: 100%;
      height: 210px;
      overflow: hidden;
      margin-bottom: 20px;
      border: 1px solid rgba(255, 255, 255, .1);
      border-radius: 16px;
      background: rgba(255, 255, 255, .05);
      box-shadow: 0 15px 40px rgba(0, 0, 0, .2);
    }

    .map-card iframe {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      border: 0;
      filter: saturate(.85) contrast(1.02);
    }

    .map-card::after {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      background: linear-gradient(180deg,
          rgba(3, 25, 18, .05) 35%,
          rgba(3, 25, 18, .75) 100%);
    }

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

    .map-info {
      display: flex;
      align-items: center;
      gap: 9px;
      min-width: 0;
      padding: 9px 11px;
      border: 1px solid rgba(255, 255, 255, .16);
      border-radius: 10px;
      background: rgba(5, 35, 26, .78);
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
      color: rgba(255, 255, 255, .55);
      font-size: 7px;
    }

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
      box-shadow: 0 8px 20px rgba(0, 0, 0, .2);
      transition: all .3s ease;
    }

    .map-direction:hover {
      color: white;
      background: var(--primary);
      transform: translateY(-2px);
    }

    .map-direction i {
      font-size: 9px;
    }

    .location-contact {
      margin-top: 5px;
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, .08);
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

      .program-grid {
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
        box-shadow: 0 20px 30px rgba(0, 0, 0, .08);
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
        width: 100%;
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

      .page-hero {
        padding: 80px 0 90px;
      }

      .filter-bar {
        align-items: stretch;
        flex-direction: column;
      }

      .search-box {
        width: 100%;
      }

      .footer-main {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 650px) {
      .container {
        width: min(100% - 28px, 1180px);
      }

      .page-hero {
        padding: 60px 0 75px;
      }

      .page-hero h1 {
        font-size: 40px;
        letter-spacing: -1px;
      }

      .page-hero p {
        font-size: 15px;
      }

      .program-section {
        padding: 70px 0;
      }

      .program-grid {
        grid-template-columns: 1fr;
      }

      .filter-tabs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
      }

      .filter-tabs a {
        width: 100%;
      }

      .program-image {
        height: 210px;
      }

      .cta-section {
        padding-bottom: 70px;
      }

      .cta {
        padding: 45px 25px;
      }

      .footer-main {
        grid-template-columns: 1fr;
        gap: 30px;
      }

      .footer-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
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
  </style>
</head>

<body>

  <!-- TOPBAR REUSABLE -->
  <?php require_once __DIR__ . '/../menu/topbar.php'; ?>

  <!-- NAVBAR REUSABLE -->
  <?php require_once __DIR__ . '/../menu/navbar.php'; ?>
  <!-- =========================================================
     HERO
========================================================= -->
  <section class="page-hero">
    <div class="container hero-content">

      <div class="breadcrumb">
        <a href="../../index.php">⌂ Beranda</a>
        <span>›</span>
        <span>Program Studi</span>
      </div>

      <span class="hero-label">PENDIDIKAN FIKES</span>

      <h1>
        Program <span>Studi</span>
      </h1>

      <p>
        Kenali program pendidikan FIKES dan temukan program studi
        yang sesuai dengan minat serta tujuan karier Anda.
      </p>

    </div>
  </section>

  <!-- =========================================================
     DAFTAR PROGRAM STUDI
========================================================= -->
  <main class="program-section" id="daftar-prodi">
    <div class="container">

      <div class="section-heading">
        <span class="eyebrow">PILIH PROGRAM ANDA</span>

        <h2>Program Studi</h2>

        <p>
          Pilih program studi untuk melihat informasi lengkap mengenai
          profil, kurikulum, capaian pembelajaran, fasilitas,
          dan informasi akademik lainnya.
        </p>
      </div>

      <form class="filter-bar" method="get" action="program-studi.php">

        <div class="filter-tabs">
          <a class="<?= $filter === '' ? 'active' : '' ?>" href="program-studi.php#daftar-prodi">
            Semua
          </a>

          <?php foreach ($jenjangList as $j): ?>
            <a class="<?= $filter === $j ? 'active' : '' ?>" href="?jenjang=<?= urlencode($j) ?>#daftar-prodi">
              <?= e($j) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <div class="search-box">
          <span>⌕</span>

          <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari program studi..."
            autocomplete="off">

          <?php if ($filter !== ''): ?>
            <input type="hidden" name="jenjang" value="<?= e($filter) ?>">
          <?php endif; ?>
        </div>

      </form>

      <p class="result-info">
        Menampilkan <strong><?= $totalProgram ?></strong> program studi
        <?= $filter !== '' ? 'pada jenjang <strong>' . e($filter) . '</strong>' : '' ?>
        <?= $search !== '' ? ' untuk pencarian <strong>"' . e($search) . '"</strong>' : '' ?>.
      </p>

      <?php if (!$programs): ?>

        <div class="empty-state">
          Program studi yang Anda cari belum tersedia.
        </div>

      <?php else: ?>

        <div class="program-grid">

          <?php foreach ($programs as $p): ?>

            <?php
            $foto = !empty($p['foto'])
              ? '../../admin/uploads/program-studi/' . rawurlencode($p['foto'])
              : '';
            ?>

            <article class="program-card">

              <div class="program-image">

                <?php if ($foto): ?>

                  <img src="<?= e($foto) ?>" alt="Foto <?= e($p['nama']) ?>" loading="lazy">

                <?php else: ?>

                  <div class="image-placeholder">
                    F
                  </div>

                <?php endif; ?>

                <span class="jenjang-badge">
                  <?= e($p['jenjang']) ?>
                </span>

              </div>

              <div class="program-body">

                <div class="code">
                  <?= e($p['kode_prodi']) ?>
                </div>

                <h3>
                  <?= e($p['nama']) ?>
                </h3>

                <p>
                  <?= e($p['deskripsi']) ?>
                </p>

                <div class="meta-row">

                  <?php if (!empty($p['gelar'])): ?>
                    <span>🎓 <?= e($p['gelar']) ?></span>
                  <?php endif; ?>

                  <?php if (!empty($p['akreditasi'])): ?>
                    <span>✓ <?= e($p['akreditasi']) ?></span>
                  <?php endif; ?>

                </div>

                <a class="detail-btn" href="detail-prodi.php?kode=<?= urlencode($p['kode_prodi']) ?>">
                  <span>Lihat Detail</span>
                  <span>→</span>
                </a>

              </div>

            </article>

          <?php endforeach; ?>

        </div>

      <?php endif; ?>

    </div>
  </main>

  <!-- =========================================================
     CTA
========================================================= -->
  <section class="cta-section">
    <div class="container">

      <div class="cta">
        <div class="cta-content">

          <span class="eyebrow">BERSAMA FIKES</span>

          <h2>
            Bangun Masa Depan Bersama Kami
          </h2>

          <p>
            Temukan program studi yang mendukung perjalanan
            akademik dan profesional Anda.
          </p>

          <a href="#daftar-prodi" class="btn">
            Lihat Program Studi →
          </a>

        </div>
      </div>

    </div>
  </section>

  <!-- =========================================================
     FOOTER
========================================================= -->
  <footer>

    <div class="container footer-main">

      <div class="footer-brand">

        <div class="logo footer-logo">

          <div class="logo-icon">F</div>

          <div class="logo-text">
            <strong>FIKES</strong>
            <small>FAKULTAS ILMU KESEHATAN</small>
          </div>

        </div>

        <p>
          Membangun generasi kesehatan yang profesional,
          berintegritas, inovatif, dan berorientasi kepada masyarakat.
        </p>

      </div>

      <div>
        <h4 class="footer-title">Tentang FIKES</h4>

        <div class="footer-links">
          <a href="../tentang-fikes/visi-misi.php">Visi Misi</a>
          <a href="../tentang-fikes/struktur-organisasi.php">Struktur Organisasi</a>
          <a href="../tentang-fikes/sertifikat-akreditasi.php">Akreditasi</a>
          <a href="../dosen/dosen.php">Daftar Dosen</a>
        </div>
      </div>

      <div>
        <h4 class="footer-title">Program Studi</h4>

        <div class="footer-links">
          <?php foreach (array_slice($programs, 0, 5) as $p): ?>
            <a href="detail-prodi.php?kode=<?= urlencode($p['kode_prodi']) ?>">
              <?= e($p['nama']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div>
        <h4 class="footer-title">Informasi</h4>

        <div class="footer-links">
          <a href="../akademik.php">Akademik</a>
          <a href="../kemahasiswaan/hima.php">Kemahasiswaan</a>
          <a href="../pelayanan-fikes.php">Pelayanan FIKES</a>
          <a href="../survey.php">Survey</a>
        </div>
      </div>

      <!-- =====================================================
             LOKASI & PETA
        ====================================================== -->
      <div class="footer-location">

        <div class="location-header">

          <div class="location-icon">
            📍
          </div>

          <div>
            <h3>Lokasi Kampus</h3>
            <p>Fakultas Ilmu Kesehatan</p>
          </div>

        </div>

        <div class="map-card">

          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbef42471658d%3A0x883656d1325ef066!2sUniversitas%20Bhamada%20Slawi!5e0!3m2!1sid!2sid!4v1787544396003!5m2!1sid!2sid"
            width="600" height="450" style="border:0" allowfullscreen loading="lazy"
            referrerpolicy="strict-origin-when-cross-origin" title="Lokasi Fakultas Ilmu Kesehatan"></iframe>

          <div class="map-overlay">

            <div class="map-info">

              <div class="map-info-icon">
                📍
              </div>

              <div>
                <strong>Fakultas Ilmu Kesehatan</strong>
                <span>Lihat lokasi kampus</span>
              </div>

            </div>

            <a href="https://www.google.com/maps/dir/?api=1&destination=-6.991421893009626,109.11806027499709"
              target="_blank" rel="noopener" class="map-direction">
              ↗ Petunjuk Arah
            </a>

          </div>

        </div>

        <div class="footer-contact location-contact">
          <span>📍</span>
          <span>Alamat Fakultas Ilmu Kesehatan, silakan sesuaikan dengan alamat kampus.</span>
        </div>

        <div class="footer-contact">
          <span>☎</span>
          <span>Nomor Telepon FIKES</span>
        </div>

        <div class="footer-contact">
          <span>✉</span>
          <span>email@fikes.ac.id</span>
        </div>

      </div>

    </div>

    <div class="container footer-bottom">
      <span>
        © <span id="year"></span>
        Fakultas Ilmu Kesehatan. All Rights Reserved.
      </span>

      <span>Website FIKES</span>
    </div>

  </footer>

  <!-- BACK TO TOP -->
  <button class="back-top" id="backTop" type="button" aria-label="Kembali ke atas">
    ↑
  </button>

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

      menuToggle.innerHTML =
        navMenu.classList.contains("active") ? "✕" : "☰";
    });

    /* =========================================================
       MOBILE DROPDOWN
    ========================================================= */
    document
      .querySelectorAll(".has-dropdown > .nav-link, .has-dropdown > .dropdown-link")
      .forEach((link) => {

        link.addEventListener("click", function(e) {

          if (window.innerWidth <= 900) {

            e.preventDefault();

            const parent = this.parentElement;

            parent.classList.toggle("open");
          }

        });

      });

    /* =========================================================
       CLOSE MOBILE MENU
    ========================================================= */
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

    /* =========================================================
       BACK TO TOP
    ========================================================= */
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
        behavior: "smooth"
      });

    });

    /* =========================================================
       YEAR
    ========================================================= */
    document.getElementById("year").textContent =
      new Date().getFullYear();

    /* =========================================================
       CLOSE DROPDOWN
    ========================================================= */
    document.addEventListener("click", (event) => {

      if (!event.target.closest(".navbar")) {

        document
          .querySelectorAll(".nav-item.open")
          .forEach((item) => {
            item.classList.remove("open");
          });

      }

    });
  </script>

</body>

</html>
