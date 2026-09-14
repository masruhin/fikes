<?php
require_once __DIR__ . '/admin/config/database.php';

function e($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

function beritaImageUrl($gambar)
{
  $gambar = trim((string)$gambar);
  if ($gambar === '') return '';

  if (preg_match('~^(https?:)?//~i', $gambar)) return $gambar;

  $gambar = str_replace('\\', '/', $gambar);
  $gambar = ltrim($gambar, '/');

  // Database may contain only the filename.
  if (strpos($gambar, 'admin/uploads/berita/') === 0) {
    return $gambar;
  }
  if (strpos($gambar, 'uploads/berita/') === 0) {
    return 'admin/' . $gambar;
  }
  if (strpos($gambar, 'admin/') === 0 || strpos($gambar, 'assets/') === 0) {
    return $gambar;
  }

  return 'admin/uploads/berita/' . rawurlencode(basename($gambar));
}

function beritaImageExists($gambar)
{
  $gambar = trim((string)$gambar);
  if ($gambar === '' || preg_match('~^(https?:)?//~i', $gambar)) return false;

  $gambar = str_replace('\\', '/', ltrim($gambar, '/'));
  if (strpos($gambar, 'admin/uploads/berita/') === 0) {
    $relative = $gambar;
  } elseif (strpos($gambar, 'uploads/berita/') === 0) {
    $relative = 'admin/' . $gambar;
  } elseif (strpos($gambar, 'admin/') === 0 || strpos($gambar, 'assets/') === 0) {
    $relative = $gambar;
  } else {
    $relative = 'admin/uploads/berita/' . basename($gambar);
  }

  return is_file(__DIR__ . '/' . $relative);
}

$slug = trim($_GET['slug'] ?? '');
$stmt = $pdo->prepare("SELECT * FROM berita WHERE slug = :slug AND status = 'terbit' LIMIT 1");
$stmt->execute(['slug' => $slug]);
$berita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$berita) {
  http_response_code(404);
  exit('Berita tidak ditemukan.');
}

$relatedStmt = $pdo->prepare("SELECT id,slug,judul,gambar,kategori,tanggal_terbit,ringkasan FROM berita WHERE status='terbit' AND id<>:id ORDER BY tanggal_terbit DESC,id DESC LIMIT 3");
$relatedStmt->execute(['id' => $berita['id']]);
$related = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

$mainImage = beritaImageUrl($berita['gambar'] ?? '');
$mainImageExists = beritaImageExists($berita['gambar'] ?? '');
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= e($berita['judul']) ?> - FIKES</title>
    <meta name="description" content="<?= e($berita['ringkasan'] ?: $berita['judul']) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
      --muted: #788983;
      --light: #f5faf8;
      --white: #fff;
      --border: #e3ece8;
      --shadow: 0 24px 70px rgba(18, 55, 42, .12)
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    html {
      scroll-behavior: smooth
    }

    body {
      font-family: Inter, sans-serif;
      color: var(--text);
      background: var(--light);
      line-height: 1.7
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
      color: #dcebe5;
      font-size: 13px
    }

    .topbar-inner {
      min-height: 38px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px
    }

    .topbar-info,
    .topbar-social {
      display: flex;
      gap: 22px;
      align-items: center
    }

    .topbar-social a:hover {
      color: #fff
    }

    .navbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: rgba(255, 255, 255, .96);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid var(--border)
    }

    .nav-inner {
      min-height: 80px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px
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
      box-shadow: 0 8px 20px rgba(8, 127, 91, .2)
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
      font-weight: 800;
      letter-spacing: .4px
    }

    .nav-menu {
      display: flex;
      gap: 4px
    }

    .nav-link {
      min-height: 80px;
      padding: 0 13px;
      display: flex;
      align-items: center;
      font-size: 13px;
      font-weight: 700;
      color: #344b43
    }

    .nav-link:hover {
      color: var(--primary)
    }

    .nav-cta {
      padding: 13px 20px;
      border-radius: 11px;
      background: var(--primary);
      color: #fff;
      font-size: 13px;
      font-weight: 800
    }

    .menu-toggle {
      display: none;
      border: 0;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 10px;
      width: 44px;
      height: 44px;
      font-size: 22px
    }

    .article-hero {
      padding: 64px 0 36px;
      background: linear-gradient(180deg, #fff 0%, #f5faf8 100%)
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--muted);
      font-size: 12px;
      margin-bottom: 25px
    }

    .breadcrumb a {
      color: var(--primary);
      font-weight: 700
    }

    .breadcrumb span {
      opacity: .5
    }

    .hero-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 320px;
      gap: 50px;
      align-items: end
    }

    .category {
      display: inline-flex;
      align-items: center;
      padding: 7px 13px;
      border-radius: 30px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .4px;
      margin-bottom: 16px
    }

    .article-hero h1 {
      font-family: "Plus Jakarta Sans", sans-serif;
      color: var(--dark);
      font-size: clamp(34px, 5vw, 58px);
      line-height: 1.12;
      letter-spacing: -1.5px;
      max-width: 900px
    }

    .lead {
      font-size: 16px;
      color: #687a73;
      max-width: 780px;
      margin-top: 18px
    }

    .meta {
      display: flex;
      flex-wrap: wrap;
      gap: 9px 18px;
      margin-top: 22px;
      color: var(--muted);
      font-size: 12px
    }

    .meta strong {
      color: var(--dark)
    }

    .hero-side {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 20px;
      box-shadow: 0 12px 35px rgba(18, 55, 42, .06)
    }

    .hero-side-label {
      font-size: 10px;
      letter-spacing: 1px;
      font-weight: 800;
      color: var(--primary);
      margin-bottom: 7px
    }

    .hero-side p {
      font-size: 13px;
      color: var(--text)
    }

    .article-area {
      padding: 25px 0 80px
    }

    .article-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 26px;
      overflow: hidden;
      box-shadow: var(--shadow)
    }

    .featured-image {
      width: 100%;
      height: min(560px, 52vw);
      min-height: 300px;
      object-fit: cover;
      display: block;
      background: linear-gradient(135deg, var(--primary-light), #d7eee6)
    }

    .image-placeholder {
      height: 380px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary-light), #d8eee7);
      color: var(--primary);
      font-family: "Plus Jakarta Sans";
      font-size: 44px;
      font-weight: 800
    }

    .article-body {
      max-width: 900px;
      margin: auto;
      padding: 45px 55px 55px
    }

    .article-content {
      font-size: 16px;
      line-height: 1.95;
      color: #40544d
    }

    .article-content p {
      margin: 0 0 20px
    }

    .article-content h2,
    .article-content h3 {
      font-family: "Plus Jakarta Sans";
      color: var(--dark);
      margin: 30px 0 13px
    }

    .article-content img {
      max-width: 100%;
      height: auto;
      border-radius: 14px
    }

    .article-content a {
      color: var(--primary);
      font-weight: 700
    }

    .article-content ul,
    .article-content ol {
      padding-left: 25px;
      margin: 0 0 20px
    }

    .article-content blockquote {
      margin: 25px 0;
      padding: 18px 22px;
      border-left: 4px solid var(--primary);
      background: var(--light);
      border-radius: 0 12px 12px 0;
      color: #486058
    }

    .related-section {
      padding: 0 0 90px
    }

    .section-head {
      display: flex;
      justify-content: space-between;
      align-items: end;
      gap: 20px;
      margin-bottom: 25px
    }

    .eyebrow {
      font-size: 11px;
      letter-spacing: 1px;
      font-weight: 800;
      color: var(--primary)
    }

    .section-head h2 {
      font-family: "Plus Jakarta Sans";
      font-size: 28px;
      color: var(--dark);
      margin-top: 5px
    }

    .all-link {
      font-size: 12px;
      color: var(--primary);
      font-weight: 800
    }

    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px
    }

    .news-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 12px 35px rgba(18, 55, 42, .06);
      transition: .3s
    }

    .news-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 18px 45px rgba(18, 55, 42, .11)
    }

    .news-image {
      display: block;
      height: 205px;
      background: var(--primary-light);
      overflow: hidden
    }

    .news-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: .4s
    }

    .news-card:hover .news-image img {
      transform: scale(1.04)
    }

    .news-placeholder {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-weight: 800;
      font-size: 28px
    }

    .news-body {
      padding: 19px
    }

    .news-category {
      display: inline-block;
      padding: 5px 9px;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 20px;
      font-size: 10px;
      font-weight: 800;
      margin-bottom: 9px
    }

    .news-date {
      font-size: 11px;
      color: var(--muted);
      margin-bottom: 7px
    }

    .news-body h3 {
      font-family: "Plus Jakarta Sans";
      font-size: 17px;
      color: var(--dark);
      line-height: 1.4;
      margin-bottom: 8px
    }

    .news-body p {
      font-size: 12px;
      color: var(--text);
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden
    }

    .read-more {
      display: inline-block;
      margin-top: 13px;
      color: var(--primary);
      font-size: 11px;
      font-weight: 800
    }

    footer {
      background: var(--dark);
      color: rgba(255, 255, 255, .58)
    }

    .footer-main {
      padding: 60px 0 40px;
      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr 1fr;
      gap: 40px
    }

    .footer-brand p {
      max-width: 320px;
      font-size: 13px;
      margin-top: 15px
    }

    .footer-logo strong {
      color: #fff
    }

    .footer-title {
      color: #fff;
      font-size: 14px;
      margin-bottom: 15px
    }

    .footer-links {
      display: grid;
      gap: 9px
    }

    .footer-links a {
      font-size: 12px
    }

    .footer-links a:hover {
      color: #fff
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, .09);
      padding: 18px 0;
      display: flex;
      justify-content: space-between;
      font-size: 11px
    }

    .back-top {
      position: fixed;
      right: 25px;
      bottom: 25px;
      width: 42px;
      height: 42px;
      border: 0;
      border-radius: 11px;
      background: var(--primary);
      color: #fff;
      display: none;
      z-index: 1100;
      cursor: pointer
    }

    .back-top.show {
      display: block
    }

    @media(max-width:900px) {
      .topbar {
        display: none
      }

      .nav-inner {
        min-height: 72px
      }

      .menu-toggle {
        display: block
      }

      .nav-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        display: none;
        background: #fff;
        padding: 10px 20px 20px;
        border-bottom: 1px solid var(--border)
      }

      .nav-menu.active {
        display: block
      }

      .nav-link {
        min-height: 46px
      }

      .nav-cta {
        display: none
      }

      .hero-grid {
        grid-template-columns: 1fr
      }

      .hero-side {
        display: none
      }

      .news-grid {
        grid-template-columns: 1fr 1fr
      }

      .footer-main {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:600px) {
      .container {
        width: calc(100% - 28px)
      }

      .article-hero {
        padding: 42px 0 25px
      }

      .article-hero h1 {
        font-size: 34px
      }

      .article-body {
        padding: 30px 22px 38px
      }

      .featured-image {
        height: 260px;
        min-height: 0
      }

      .image-placeholder {
        height: 260px
      }

      .article-content {
        font-size: 15px
      }

      .news-grid {
        grid-template-columns: 1fr
      }

      .section-head {
        align-items: start;
        flex-direction: column
      }

      .footer-main {
        grid-template-columns: 1fr
      }

      .footer-bottom {
        flex-direction: column;
        gap: 7px
      }
    }

    /* === HEADER & FOOTER: TEMPLATE FIKES === */
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

    .topbar-info {
      display: flex;
      gap: 25px;
      align-items: center
    }

    .topbar-info span {
      display: flex;
      align-items: center;
      gap: 7px
    }

    .topbar-social {
      display: flex;
      gap: 15px
    }

    .topbar-social a {
      transition: var(--transition)
    }

    .topbar-social a:hover {
      color: var(--secondary)
    }

    .navbar {
      position: sticky;
      top: 0;
      z-index: 999;
      background: rgba(255, 255, 255, .94);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(229, 236, 233, .8);
      transition: var(--transition)
    }

    .navbar.scrolled {
      box-shadow: 0 10px 35px rgba(0, 0, 0, .08)
    }

    .nav-inner {
      min-height: 82px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 30px
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0
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
      font-size: 17px;
      line-height: 1.2
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
      transition: var(--transition);
      white-space: nowrap
    }

    .nav-link:hover {
      color: var(--primary)
    }

    .arrow {
      font-size: 11px;
      transition: var(--transition)
    }

    .nav-item:hover>.nav-link .arrow {
      transform: rotate(180deg)
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
      transition: var(--transition);
      z-index: 20
    }

    .nav-item:hover>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0)
    }

    .dropdown-item {
      position: relative
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
      transition: var(--transition)
    }

    .dropdown-link:hover {
      color: var(--primary);
      background: var(--primary-light)
    }

    .dropdown-item>.dropdown {
      left: calc(100% + 5px);
      top: -10px
    }

    .dropdown-item:hover>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0)
    }

    .nav-cta {
      padding: 12px 19px;
      border-radius: 10px;
      background: var(--primary);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      transition: var(--transition)
    }

    .nav-cta:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(8, 127, 91, .18)
    }

    .menu-toggle {
      display: none;
      border: 0;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 10px;
      width: 44px;
      height: 44px;
      font-size: 22px;
      cursor: pointer
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

    .footer-brand p {
      max-width: 320px;
      font-size: 13px;
      margin: 17px 0
    }

    .footer-logo .logo-text strong {
      color: #fff
    }

    .footer-logo .logo-text small {
      color: #8ad7bd
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
      font-size: 12px;
      transition: var(--transition)
    }

    .footer-links a:hover {
      color: var(--secondary);
      transform: translateX(3px)
    }

    .footer-location {
      grid-column: 1/-1
    }

    .location-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px
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
      font-size: 16px
    }

    .location-header h3 {
      margin: 0 0 3px;
      color: #fff;
      font-size: 13px;
      font-weight: 800
    }

    .location-header p {
      margin: 0;
      color: rgba(255, 255, 255, .5);
      font-size: 9px
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
      box-shadow: 0 15px 40px rgba(0, 0, 0, .2)
    }

    .map-card iframe {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      border: 0;
      filter: saturate(.85) contrast(1.02)
    }

    .map-card:after {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      background: linear-gradient(180deg, rgba(3, 25, 18, .05) 35%, rgba(3, 25, 18, .75) 100%)
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
      gap: 10px
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
      backdrop-filter: blur(10px)
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
      font-size: 11px
    }

    .map-info strong {
      display: block;
      max-width: 150px;
      overflow: hidden;
      color: #fff;
      font-size: 9px;
      font-weight: 800;
      white-space: nowrap;
      text-overflow: ellipsis
    }

    .map-info span {
      display: block;
      margin-top: 2px;
      color: rgba(255, 255, 255, .55);
      font-size: 7px
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
      background: #fff;
      font-size: 8px;
      font-weight: 800;
      box-shadow: 0 8px 20px rgba(0, 0, 0, .2);
      transition: all .3s ease
    }

    .map-direction:hover {
      color: #fff;
      background: var(--primary);
      transform: translateY(-2px)
    }

    .footer-contact {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-top: 10px;
      color: #b7cec5;
      font-size: 11px
    }

    .footer-contact i {
      width: 16px;
      color: var(--secondary);
      margin-top: 3px
    }

    .location-contact {
      margin-top: 5px
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, .08);
      padding: 20px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 11px
    }

    .back-top {
      position: fixed;
      right: 25px;
      bottom: 25px;
      width: 45px;
      height: 45px;
      border: 0;
      border-radius: 12px;
      background: var(--primary);
      color: #fff;
      cursor: pointer;
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: var(--transition);
      z-index: 900
    }

    .back-top.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0)
    }

    @media(max-width:1100px) {
      .nav-link {
        padding: 0 8px;
        font-size: 12px
      }

      .nav-cta {
        display: none
      }
    }

    @media(max-width:900px) {
      .topbar {
        display: none
      }

      .nav-inner {
        min-height: 72px
      }

      .menu-toggle {
        display: block;
        order: 2
      }

      .nav-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        display: none;
        flex-direction: column;
        align-items: stretch;
        padding: 12px 20px 20px;
        background: #fff;
        border-bottom: 1px solid var(--border);
        box-shadow: 0 15px 30px rgba(0, 0, 0, .08);
        max-height: calc(100vh - 72px);
        overflow: auto
      }

      .nav-menu.active {
        display: flex
      }

      .nav-link {
        min-height: 48px;
        padding: 0 12px
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
        padding: 4px 0 4px 12px
      }

      .has-dropdown.open>.dropdown {
        display: block
      }

      .dropdown-item>.dropdown {
        position: static
      }

      .dropdown-item.open>.dropdown {
        display: block
      }

      .footer-main {
        grid-template-columns: repeat(2, 1fr);
        gap: 35px
      }

      .footer-location {
        grid-column: 1/-1
      }
    }

    @media(max-width:650px) {
      .footer-main {
        grid-template-columns: 1fr;
        gap: 30px
      }

      .footer-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 7px
      }

      .map-overlay {
        flex-direction: column;
        align-items: stretch
      }

      .map-info {
        width: 100%
      }

      .map-direction {
        width: 100%
      }
    }
    </style>
  </head>

  <body>
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

    <header class="navbar" id="navbar">
      <div class="container nav-inner">
        <!-- LOGO -->

        <a href="index.php" class="logo">
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
            <a href="#" class="nav-link">
              Tentang FIKES
              <span class="arrow">▾</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="page/tentang-fikes/visi-misi.php" class="dropdown-link"> Visi Misi </a>
              </div>

              <div class="dropdown-item">
                <a href="page/tentang-fikes/struktur-organisasi.php" class="dropdown-link">
                  Struktur Organisasi
                </a>
              </div>

              <div class="dropdown-item">
                <a href="page/tentang-fikes/sertifikat-akreditasi.php" class="dropdown-link">
                  Sertifikat Akreditasi
                </a>
              </div>

              <div class="dropdown-item">
                <a href="page/tentang-fikes/unduh-logo.php" class="dropdown-link"> Unduh Logo </a>
              </div>

              <!-- DAFTAR DOSEN -->

              <div class="dropdown-item has-dropdown">
                <a href="#" class="dropdown-link">
                  Daftar Dosen
                  <span>›</span>
                </a>

                <div class="dropdown">
                  <div class="dropdown-item">
                    <a href="page/dosen/dosen.php" class="dropdown-link"> Keperawatan </a>
                  </div>
                  <div class="dropdown-item">
                    <a href="dosen.html" class="dropdown-link"> Keperawatan </a>
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
              </div>
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
                <a href="himpunan-mahasiswa.html" class="dropdown-link">
                  Himpunan Mahasiswa
                </a>
              </div>

              <div class="dropdown-item">
                <a href="unit-kegiatan-mahasiswa.html" class="dropdown-link">
                  Unit Kegiatan Mahasiswa
                </a>
              </div>
            </div>
          </div>

          <!-- PROGRAM VOKASI -->



          <div class="nav-item">
            <a href="page/program-studi/program-studi.php" class="nav-link"> Program </a>
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
      <section class="article-hero">
        <div class="container">
          <div class="breadcrumb"><a href="index.php">Beranda</a><span>›</span><a
              href="index.php#berita">Berita</a><span>›</span><span>Detail</span></div>
          <div class="hero-grid">
            <div>
              <span class="category"><?= e($berita['kategori'] ?: 'Berita') ?></span>
              <h1><?= e($berita['judul']) ?></h1>
              <?php if (!empty($berita['ringkasan'])): ?><p class="lead"><?= e($berita['ringkasan']) ?></p>
              <?php endif; ?>
              <div class="meta"><span>📅
                  <?= date('d M Y', strtotime($berita['tanggal_terbit'])) ?></span><span>•</span><span>✍️
                  <strong><?= e($berita['penulis'] ?: 'Admin FIKES') ?></strong></span></div>
            </div>
            <aside class="hero-side">
              <div class="hero-side-label">INFORMASI BERITA</div>
              <p>Berita dan kegiatan terbaru dari Fakultas Ilmu Kesehatan.</p>
            </aside>
          </div>
        </div>
      </section>

      <section class="article-area">
        <div class="container">
          <article class="article-card">
            <?php if ($mainImage && ($mainImageExists || preg_match('~^(https?:)?//~i', $mainImage))): ?>
            <img class="featured-image" src="<?= e($mainImage) ?>" alt="<?= e($berita['judul']) ?>"
              onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="image-placeholder" style="display:none">FIKES</div>
            <?php else: ?>
            <div class="image-placeholder">FIKES</div>
            <?php endif; ?>
            <div class="article-body">
              <div class="article-content"><?= $berita['isi'] ?></div>
            </div>
          </article>
        </div>
      </section>

      <?php if ($related): ?><section class="related-section">
        <div class="container">
          <div class="section-head">
            <div><span class="eyebrow">INFORMASI TERKINI</span>
              <h2>Berita Lainnya</h2>
            </div><a class="all-link" href="index.php#berita">Lihat Semua →</a>
          </div>
          <div class="news-grid">
            <?php foreach ($related as $b): $img = beritaImageUrl($b['gambar'] ?? '');
              $exists = beritaImageExists($b['gambar'] ?? ''); ?>
            <article class="news-card"><a href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>"
                class="news-image"><?php if ($img && ($exists || preg_match('~^(https?:)?//~i', $img))): ?><img
                  src="<?= e($img) ?>" alt="<?= e($b['judul']) ?>"><?php else: ?><div class="news-placeholder">FIKES
                </div><?php endif; ?></a>
              <div class="news-body"><span class="news-category"><?= e($b['kategori']) ?></span>
                <div class="news-date">📅 <?= date('d M Y', strtotime($b['tanggal_terbit'])) ?></div>
                <h3><a href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>"><?= e($b['judul']) ?></a></h3>
                <p><?= e($b['ringkasan']) ?></p><a class="read-more"
                  href="detail-berita.php?slug=<?= urlencode($b['slug']) ?>">Baca Selengkapnya →</a>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section><?php endif; ?>
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
          <div class="footer-links">
            <a href="/fikes/page/tentang-fikes/visi-misi.php">Visi Misi</a>
            <a href="/fikes/page/tentang-fikes/struktur-organisasi.php">Struktur Organisasi</a>
            <a href="/fikes/page/tentang-fikes/sertifikat-akreditasi.php">Akreditasi</a>
            <a href="/fikes/page/tentang-fikes/dosen.php">Daftar Dosen</a>
          </div>
        </div>

        <div>
          <h4 class="footer-title">Program Studi</h4>
          <div class="footer-links">
            <a href="/fikes/page/program-studi/program-studi.php">Profesi Ners</a>
            <a href="/fikes/page/program-studi/program-studi.php">Ilmu Keperawatan</a>
            <a href="/fikes/page/program-studi/program-studi.php">Farmasi</a>
            <a href="/fikes/page/program-studi/program-studi.php">Kebidanan</a>
            <a href="/fikes/page/program-studi/program-studi.php">K3</a>
          </div>
        </div>

        <div>
          <h4 class="footer-title">Informasi</h4>
          <div class="footer-links">
            <a href="/fikes/index.php#akademik">Akademik</a>
            <a href="/fikes/index.php#kemahasiswaan">Kemahasiswaan</a>
            <a href="/fikes/index.php#pelayanan">Pelayanan FIKES</a>
            <a href="/fikes/index.php#survey">Survey</a>
          </div>
        </div>

        <div class="footer-location">
          <div class="location-header">
            <div class="location-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div>
              <h3>Lokasi Kampus</h3>
              <p>Fakultas Ilmu Kesehatan</p>
            </div>
          </div>
          <div class="map-card">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1515727139526!2d109.11806027499709!3d-6.991421893009626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbef42471658d%3A0x883656d1325ef066!2sUniversitas%20Bhamada%20Slawi!5e0!3m2!1sid!2sid!4v1787544396003!5m2!1sid!2sid"
              title="Lokasi Fakultas Ilmu Kesehatan" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              allowfullscreen></iframe>
            <div class="map-overlay">
              <div class="map-info">
                <div class="map-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div><strong>Fakultas Ilmu Kesehatan</strong><span>Lihat lokasi kampus</span></div>
              </div><a href="https://www.google.com/maps/search/?api=1&query=Universitas+Bhamada+Slawi" target="_blank"
                class="map-direction"><i class="fa-solid fa-diamond-turn-right"></i> Petunjuk Arah</a>
            </div>
          </div>
          <div class="footer-contact location-contact"><i class="fa-solid fa-location-dot"></i><span>Fakultas Ilmu
              Kesehatan, Universitas Bhamada Slawi</span></div>
          <div class="footer-contact"><i class="fa-solid fa-phone"></i><span>(021) 1234567</span></div>
          <div class="footer-contact"><i class="fa-solid fa-envelope"></i><span>info@fikes.ac.id</span></div>
        </div>
      </div>
      <div class="container footer-bottom"><span>© <span id="year"></span> Fakultas Ilmu Kesehatan. All Rights
          Reserved.</span><span>Website FIKES</span></div>
    </footer>
    <button class="back-top" id="backTop" aria-label="Kembali ke atas">↑</button>
    <script>
    (function() {
      const navbar = document.getElementById('navbar');
      const menuToggle = document.getElementById('menuToggle');
      const navMenu = document.getElementById('navMenu');
      const backTop = document.getElementById('backTop');
      window.addEventListener('scroll', () => {
        if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 20);
        if (backTop) backTop.classList.toggle('show', window.scrollY > 500);
      });
      if (menuToggle && navMenu) menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        menuToggle.innerHTML = navMenu.classList.contains('active') ? '✕' : '☰';
      });
      document.querySelectorAll('.has-dropdown > .dropdown-trigger, .has-dropdown > .nav-link').forEach(link => {
        link.addEventListener('click', e => {
          if (window.innerWidth <= 900) {
            e.preventDefault();
            link.parentElement.classList.toggle('open');
          }
        });
      });
      document.querySelectorAll('.nav-menu a').forEach(a => a.addEventListener('click', () => {
        if (window.innerWidth <= 900 && !a.parentElement.classList.contains('has-dropdown')) {
          navMenu.classList.remove('active');
          menuToggle.innerHTML = '☰';
        }
      }));
      if (backTop) backTop.addEventListener('click', () => window.scrollTo({
        top: 0,
        behavior: 'smooth'
      }));
      const y = document.getElementById('year');
      if (y) y.textContent = new Date().getFullYear();
    })();
    </script>
  </body>

</html>
