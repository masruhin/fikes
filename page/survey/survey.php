<?php
require_once __DIR__ . '/../../admin/config/database.php';
function e($v)
{
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
$surveys = $pdo->query("SELECT s.*,(SELECT COUNT(*) FROM survey_pertanyaan p WHERE p.survey_id=s.id AND p.status='aktif') jumlah_pertanyaan,(SELECT COUNT(*) FROM survey_responden r WHERE r.survey_id=s.id) jumlah_responden FROM survey s WHERE s.status='aktif' AND (s.tanggal_mulai IS NULL OR s.tanggal_mulai<=CURDATE()) AND (s.tanggal_selesai IS NULL OR s.tanggal_selesai>=CURDATE()) ORDER BY s.nomor_urut,s.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Survey | FIKES - Fakultas Ilmu Kesehatan</title>

  <meta name="description"
    content="Website resmi Fakultas Ilmu Kesehatan - Informasi akademik, program studi, kemahasiswaan, pelayanan dan informasi FIKES." />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap");

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
      font-family: "Plus Jakarta Sans", sans-serif;
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

    .section {
      padding: 90px 0
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
      font-size: 17px
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
      font-weight: 700
    }

    .nav-menu {
      display: flex;
      gap: 3px
    }

    .nav-link {
      min-height: 82px;
      padding: 0 13px;
      display: flex;
      align-items: center;
      font-size: 13px;
      font-weight: 600;
      color: #344b43
    }

    .nav-link:hover {
      color: var(--primary)
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

    /* =========================
       NAVBAR DROPDOWN
    ========================= */
    .nav-item {
      position: relative
    }

    .has-dropdown>.nav-link {
      gap: 4px
    }

    .dropdown {
      position: absolute;
      top: calc(100% + 1px);
      left: 0;
      min-width: 235px;
      padding: 10px;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      box-shadow: 0 18px 45px rgba(18, 55, 42, .14);
      opacity: 0;
      visibility: hidden;
      transform: translateY(8px);
      transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
      z-index: 1001;
    }

    .has-dropdown:hover>.dropdown,
    .has-dropdown:focus-within>.dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-item {
      display: block
    }

    .dropdown-link {
      display: block;
      padding: 10px 12px;
      border-radius: 8px;
      color: #40544d;
      font-size: 12px;
      line-height: 1.4;
      white-space: nowrap;
    }

    .dropdown-link:hover {
      background: var(--primary-light);
      color: var(--primary);
    }

    /* =========================
       SURVEY CARDS
    ========================= */
    .page-hero {
      padding: 42px 0 34px;
      background: #fff;
      border-bottom: 1px solid var(--border);
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: #71817b;
      margin-bottom: 18px;
    }

    .breadcrumb b {
      color: #a2aea9;
      font-weight: 500;
    }

    .eyebrow {
      display: inline-block;
      color: var(--primary);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .12em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .page-hero h1 {
      font-size: clamp(30px, 4vw, 44px);
      margin-bottom: 8px;
    }

    .page-hero p {
      max-width: 760px;
      font-size: 14px;
      color: #65756f;
    }

    .survey-section {
      padding: 70px 0 90px;
      background: var(--light);
    }

    .section-heading {
      max-width: 760px;
      margin-bottom: 30px;
    }

    .section-heading h2 {
      font-size: 30px;
      margin-bottom: 8px;
    }

    .section-heading p {
      font-size: 13px;
      color: #6d7c76;
    }

    .survey-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 24px;
      align-items: stretch;
    }

    .survey-card {
      position: relative;
      display: flex;
      min-width: 0;
      overflow: hidden;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: 0 10px 35px rgba(18, 55, 42, .06);
      transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }

    .survey-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 50px rgba(18, 55, 42, .12);
      border-color: rgba(8, 127, 91, .28);
    }

    .survey-card-accent {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), #13a878);
    }

    .survey-card-body {
      display: flex;
      flex-direction: column;
      width: 100%;
      min-height: 330px;
      padding: 26px;
    }

    .survey-card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 20px;
    }

    .survey-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 20px;
      font-weight: 800;
    }

    .survey-status {
      padding: 6px 11px;
      border-radius: 999px;
      background: #e9f8f2;
      color: var(--primary);
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .survey-card h3 {
      font-size: 20px;
      margin-bottom: 13px;
      color: var(--dark);
    }

    .survey-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 16px;
    }

    .survey-meta span {
      display: inline-flex;
      align-items: center;
      padding: 6px 9px;
      border-radius: 8px;
      background: #f5f8f7;
      color: #60716a;
      font-size: 10px;
      font-weight: 600;
    }

    .survey-card p {
      color: #667770;
      font-size: 13px;
      line-height: 1.7;
      margin-bottom: 18px;
    }

    .survey-period {
      display: grid;
      gap: 3px;
      padding: 12px 14px;
      margin-top: auto;
      margin-bottom: 16px;
      background: #f7faf9;
      border-radius: 11px;
      border: 1px solid #edf2ef;
    }

    .survey-period strong {
      color: var(--dark);
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .survey-period span {
      color: #6c7b75;
      font-size: 11px;
    }

    .survey-btn {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 12px 15px;
      border-radius: 10px;
      background: var(--primary);
      color: #fff;
      font-size: 12px;
      font-weight: 800;
      transition: background .2s ease, transform .2s ease;
    }

    .survey-btn:hover {
      background: var(--primary-dark);
    }

    .survey-btn span {
      font-size: 16px;
      line-height: 1;
    }

    .survey-empty {
      grid-column: 1 / -1;
      text-align: center;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 55px 25px;
    }

    .survey-empty .survey-icon {
      margin: 0 auto 15px;
    }

    .survey-empty h3 {
      margin-bottom: 7px;
      font-size: 20px;
    }

    .survey-empty p {
      font-size: 13px;
      color: #6d7c76;
    }

    /* =========================
   HERO SLIDER - CENTER
========================= */
    .hero-slide {
      position: relative;
    }

    .hero-content {
      width: min(100% - 40px, 900px);
      margin: 0 auto;
      padding: 0 20px;
      text-align: center;

      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .hero-content h1 {
      text-align: center;
      margin-left: auto;
      margin-right: auto;
    }

    .hero-content p {
      text-align: center;
      margin-left: auto;
      margin-right: auto;
    }

    .hero-content .hero-buttons {
      justify-content: center;
    }

    .slider-container {
      height: 100%;
      position: relative;
      overflow: hidden
    }

    .slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      visibility: hidden;
      transition: opacity .7s
    }

    .slide.active {
      opacity: 1;
      visibility: visible
    }

    .slide>img {
      width: 100%;
      height: 100%;
      object-fit: cover
    }

    .slide-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(5, 32, 24, .82), rgba(5, 32, 24, .38), rgba(5, 32, 24, .08))
    }

    .slide-content {
      position: absolute;
      z-index: 2;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      width: min(900px, calc(100% - 80px));
      max-width: 900px;
      color: #fff;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .slide-label {
      display: inline-block;
      color: var(--secondary);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 1.5px;
      margin-bottom: 18px
    }

    .slide h1 {
      font-size: 54px;
      color: #fff
    }

    .slide h1 span {
      display: block;
      color: var(--secondary)
    }

    .slide p {
      max-width: 720px;
      margin: 18px auto 28px;
      color: rgba(255, 255, 255, .88);
      text-align: center;
    }

    .slide-actions {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }

    .slide-btn {
      padding: 13px 18px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 700;
      display: inline-flex;
      gap: 14px;
      align-items: center
    }

    .slide-btn-primary {
      background: var(--primary);
      color: #fff
    }

    .slide-btn-outline {
      border: 1px solid rgba(255, 255, 255, .5);
      color: #fff
    }

    .slider-btn {
      position: absolute;
      z-index: 4;
      top: 50%;
      transform: translateY(-50%);
      width: 46px;
      height: 46px;
      border: 1px solid rgba(255, 255, 255, .35);
      border-radius: 50%;
      background: rgba(0, 0, 0, .2);
      color: #fff
    }

    .slider-prev {
      left: 22px
    }

    .slider-next {
      right: 22px
    }

    .slider-bottom {
      position: absolute;
      z-index: 5;
      bottom: 28px;
      left: 0;
      right: 0;
      display: flex;
      justify-content: space-between;
      padding: 0 30px
    }

    .slider-dots {
      display: flex;
      gap: 8px
    }

    .slider-dot {
      width: 28px;
      height: 4px;
      border: 0;
      background: rgba(255, 255, 255, .4);
      cursor: pointer
    }

    .slider-dot.active {
      background: var(--secondary)
    }

    .scroll-indicator {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #fff;
      font-size: 9px
    }

    .scroll-line {
      width: 50px;
      height: 1px;
      background: var(--secondary)
    }

    .section-header {
      text-align: center;
      max-width: 720px;
      margin: 0 auto 45px
    }

    .section-label {
      color: var(--primary);
      font-size: 12px;
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

    .news-section {
      background: var(--light)
    }

    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px
    }

    .news-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 12px 35px rgba(18, 55, 42, .07);
      transition: .3s
    }

    .news-card:hover {
      transform: translateY(-7px);
      box-shadow: var(--shadow)
    }

    .news-image {
      height: 220px;
      display: block;
      position: relative;
      overflow: hidden;
      background: var(--primary-light)
    }

    .news-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: .4s
    }

    .news-card:hover .news-image img {
      transform: scale(1.05)
    }

    .news-placeholder {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 30px;
      font-weight: 800
    }

    .news-category {
      position: absolute;
      left: 14px;
      top: 14px;
      background: var(--secondary);
      color: var(--dark);
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 800
    }

    .news-body {
      padding: 20px
    }

    .news-date {
      font-size: 11px;
      color: #7a8a84;
      margin-bottom: 8px
    }

    .news-body h3 {
      font-size: 18px;
      margin-bottom: 9px
    }

    .news-body p {
      font-size: 13px;
      color: var(--text);
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden
    }

    .news-link {
      display: inline-block;
      margin-top: 14px;
      color: var(--primary);
      font-size: 12px;
      font-weight: 800
    }

    .program-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 24px;
      align-items: stretch;
    }

    .program-card {
      position: relative;
      min-width: 0;
      min-height: 265px;
      padding: 28px 26px 24px;
      border: 1px solid var(--border);
      border-radius: 20px;
      background: #fff;
      box-shadow: 0 10px 30px rgba(18, 55, 42, .06);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }

    .program-card::after {
      content: "";
      position: absolute;
      width: 82px;
      height: 82px;
      right: -30px;
      bottom: -30px;
      border-radius: 50%;
      background: var(--primary-light);
      pointer-events: none;
    }

    .program-card:hover {
      transform: translateY(-6px);
      border-color: rgba(8, 127, 91, .25);
      box-shadow: 0 18px 45px rgba(18, 55, 42, .11);
    }

    .program-icon {
      width: 58px;
      height: 58px;
      flex: 0 0 58px;
      margin-bottom: 18px;
      border-radius: 16px;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 29px;
      line-height: 1;
    }

    .program-card h3 {
      font-size: 18px;
      line-height: 1.4;
      margin-bottom: 8px;
      min-height: 51px;
      display: flex;
      align-items: flex-start;
    }

    .program-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
      margin-bottom: 10px;
    }

    .program-meta span {
      display: inline-flex;
      align-items: center;
      min-height: 25px;
      padding: 4px 9px;
      border-radius: 999px;
      background: #f1f7f4;
      color: var(--primary-dark);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .15px;
    }

    .program-card p {
      font-size: 13px;
      line-height: 1.65;
      margin: 0;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .program-link {
      position: relative;
      z-index: 1;
      margin-top: auto;
      padding-top: 18px;
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      width: fit-content;
    }

    .program-link:hover {
      color: var(--primary-dark);
    }

    .program-empty {
      grid-column: 1 / -1;
      padding: 35px;
      text-align: center;
      border: 1px dashed var(--border);
      border-radius: 18px;
      background: var(--light);
    }


    .about-grid,
    .student-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px
    }

    .about-image {
      min-height: 400px;
      border-radius: 24px;
      background: linear-gradient(135deg, var(--primary), #13a878);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-align: center
    }

    .about-logo {
      font-size: 55px;
      font-weight: 800
    }

    .about-image h3 {
      color: #fff;
      margin-top: 8px
    }

    .feature-list {
      display: grid;
      gap: 16px;
      margin-top: 25px
    }

    .feature {
      display: flex;
      gap: 12px
    }

    .feature-check {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      flex-shrink: 0
    }

    .feature strong,
    .feature span {
      display: block
    }

    .feature span {
      font-size: 12px
    }

    .services {
      background: var(--light)
    }

    .service-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px
    }

    .service-card,
    .student-card {
      padding: 28px;
      border: 1px solid var(--border);
      border-radius: 18px;
      background: #fff
    }

    .service-card-icon {
      font-size: 28px;
      margin-bottom: 10px
    }

    .service-card p,
    .student-card p {
      font-size: 13px
    }

    .student-card.secondary {
      background: var(--dark);
      color: #d9e8e2
    }

    .student-card.secondary h3 {
      color: #fff
    }

    .student-links {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 18px
    }

    .student-links a {
      padding: 8px 12px;
      border-radius: 8px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 11px;
      font-weight: 700
    }

    .cta {
      padding: 70px 0
    }

    .cta-box {
      padding: 55px;
      border-radius: 24px;
      background: var(--dark);
      color: #fff;
      text-align: center
    }

    .cta-box h2 {
      color: #fff
    }

    .cta-box p {
      margin: 10px auto 20px;
      max-width: 650px
    }

    .btn {
      display: inline-block;
      padding: 13px 18px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 13px
    }

    .btn-primary {
      background: var(--primary);
      color: #fff
    }

    footer {
      background: var(--dark);
      color: rgba(255, 255, 255, .55)
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

    .footer-logo strong {
      color: #fff
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

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, .08);
      padding: 20px 0;
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
      border-radius: 10px;
      background: var(--primary);
      color: #fff;
      display: none;
      z-index: 100
    }

    .back-top.show {
      display: block
    }

    .article-section {
      background: var(--light)
    }

    .back-link {
      display: inline-block;
      color: var(--primary);
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 20px
    }

    .article-wrap {
      max-width: 900px;
      margin: auto;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 22px;
      padding: 35px;
      box-shadow: var(--shadow)
    }

    .article-category {
      display: inline-block;
      color: var(--primary);
      font-weight: 800;
      font-size: 12px;
      margin-bottom: 8px
    }

    .article-wrap h1 {
      font-size: 40px
    }

    .article-meta {
      font-size: 12px;
      color: #7b8984;
      margin: 12px 0 24px
    }

    .article-image {
      width: 100%;
      max-height: 520px;
      object-fit: cover;
      border-radius: 16px;
      margin-bottom: 28px
    }

    .article-content {
      font-size: 16px;
      line-height: 1.9;
      color: #40544d
    }

    .related {
      max-width: 1100px;
      margin: 50px auto 0
    }

    .related h2 {
      margin-bottom: 22px
    }

    @media(max-width:900px) {
      .dropdown {
        position: static;
        min-width: 0;
        padding: 0 0 0 12px;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        opacity: 1;
        visibility: visible;
        transform: none;
        display: none;
      }

      .has-dropdown:hover>.dropdown,
      .has-dropdown:focus-within>.dropdown {
        display: block;
      }

      .dropdown-link {
        white-space: normal;
      }

      .survey-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .topbar {
        display: none
      }

      .nav-inner {
        min-height: 72px
      }

      .menu-toggle {
        display: block;
        width: 44px;
        height: 44px;
        border: 0;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 22px
      }

      .nav-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        display: none;
        background: #fff;
        padding: 10px 20px 25px
      }

      .nav-menu.active {
        display: block
      }

      .nav-link {
        min-height: 48px
      }

      .nav-cta {
        display: none
      }

      .hero-slider {
        height: 540px
      }

      .slide h1 {
        font-size: 38px
      }

      .news-grid,
      .program-grid {
        grid-template-columns: 1fr 1fr
      }

      .service-grid {
        grid-template-columns: 1fr 1fr
      }

      .about-grid,
      .student-grid {
        grid-template-columns: 1fr
      }

      .footer-main {
        grid-template-columns: 1fr 1fr
      }
    }

    @media(max-width:600px) {
      .survey-section {
        padding: 55px 0 70px;
      }

      .survey-grid {
        grid-template-columns: 1fr;
      }

      .survey-card-body {
        min-height: 0;
        padding: 22px;
      }

      .page-hero {
        padding: 32px 0 28px;
      }

      .container {
        width: min(100% - 28px, 1180px)
      }

      .hero-slider {
        height: 600px
      }

      .slide-content {
        left: 50%;
        right: auto;
        width: calc(100% - 40px);
        transform: translate(-50%, -50%);
      }

      .slide h1 {
        font-size: 32px
      }

      .slider-bottom {
        padding: 0 15px
      }

      .scroll-indicator {
        display: none
      }

      .news-grid,
      .program-grid,
      .service-grid {
        grid-template-columns: 1fr
      }

      .section-title {
        font-size: 28px
      }

      .footer-main {
        grid-template-columns: 1fr
      }

      .article-wrap {
        padding: 22px
      }

      .article-wrap h1 {
        font-size: 28px
      }
    }
  </style>
</head>

<body>

  <!-- =========================================================
     TOP BAR
========================================================= -->

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
      <a href="/fikes/index.php" class="logo">
        <div class="logo-icon">F</div>
        <div class="logo-text"><strong>FIKES</strong><small>FAKULTAS ILMU KESEHATAN</small></div>
      </a>
      <button class="menu-toggle" id="menuToggle">☰</button>
      <nav class="nav-menu" id="navMenu">
        <div class="nav-item has-dropdown">
          <a href="#" class="nav-link">Tentang FIKES <span class="arrow">▾</span></a>
          <div class="dropdown">
            <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/visi-misi.php" class="dropdown-link">Visi
                Misi</a></div>
            <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/struktur-organisasi.php"
                class="dropdown-link">Struktur Organisasi</a></div>
            <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/sertifikat-akreditasi.php"
                class="dropdown-link">Sertifikat Akreditasi</a></div>
            <div class="dropdown-item"><a href="/fikes/page/tentang-fikes/unduh-logo.php" class="dropdown-link">Unduh
                Logo</a></div>
          </div>
        </div>
        <div class="nav-item has-dropdown">
          <a href="#" class="nav-link">Kemahasiswaan <span class="arrow">▾</span></a>
          <div class="dropdown">
            <div class="dropdown-item"><a href="/fikes/page/kemahasiswaan/hima.php" class="dropdown-link">Unit
                Himpunan Mahasiswa</a></div>
            <div class="dropdown-item"><a href="/fikes/page/kemahasiswaan/ukm.php" class="dropdown-link">UKM
                Kemahasiswaan</a></div>
          </div>
        </div>
        <div class="nav-item"><a href="/fikes/page/program-studi/program-studi.php" class="nav-link">Program</a></div>
        <div class="nav-item has-dropdown">
          <a href="/fikes/page/akademik/akademik.php" class="nav-link">Akademik <span class="arrow">▾</span></a>
          <div class="dropdown">
            <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#kurikulum"
                class="dropdown-link">Kurikulum &amp; Silabus</a></div>
            <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#kalender"
                class="dropdown-link">Kalender Pendidikan</a></div>
            <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#jadwal" class="dropdown-link">Jadwal
                Kuliah &amp; Ujian</a></div>
            <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#registrasi"
                class="dropdown-link">Jadwal Registrasi</a></div>
            <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#dokumen"
                class="dropdown-link">Administrasi &amp; Dokumen Mahasiswa</a></div>
            <div class="dropdown-item"><a href="/fikes/page/akademik/akademik.php#penilaian"
                class="dropdown-link">Sistem Penilaian</a></div>
          </div>
        </div>
        <!-- <div class="nav-item"><a href="#pelayanan" class="nav-link">Pelayanan FIKES</a></div> -->
        <div class="nav-item"><a href="/fikes/page/survey/survey.php" class="nav-link">Survey</a></div>
      </nav>
      <a href="/fikes/page/program-studi/program-studi.php" class="nav-cta">Jelajahi Program <span>→</span></a>
    </div>
  </header>
  <main>
    <section class="page-hero survey-hero">
      <div class="container">
        <div class="breadcrumb">⌂ <span>Beranda</span><b>›</b><span>Survey</span></div>
        <span class="eyebrow">PELAYANAN & MUTU FIKES</span>
        <h1>Survey FIKES</h1>
        <p>Berikan penilaian dan masukan Anda untuk membantu FIKES meningkatkan kualitas pendidikan, pelayanan,
          sarana, dan prasarana.</p>
      </div>
    </section>
    <section class="survey-section">
      <div class="container">
        <div class="section-heading">
          <span class="eyebrow">PARTISIPASI ANDA</span>
          <h2>Survey yang tersedia</h2>
          <p>Silakan pilih survey sesuai dengan kategori Anda. Daftar ini terhubung langsung dengan database dan dapat
            ditambah melalui Dashboard Admin.</p>
        </div>
        <div class="survey-grid">
          <?php if (!$surveys): ?>
            <div class="survey-empty">
              <div class="survey-icon">✓</div>
              <h3>Belum ada survey aktif</h3>
              <p>Silakan kembali lagi nanti untuk mengikuti survey FIKES.</p>
            </div>
            <?php else: foreach ($surveys as $s): ?>
              <article class="survey-card">
                <div class="survey-card-accent"></div>
                <div class="survey-card-body">
                  <div class="survey-card-top">
                    <div class="survey-icon">✓</div><span class="survey-status">Aktif</span>
                  </div>
                  <h3><?= e($s['judul']) ?></h3>
                  <div class="survey-meta"><span>👤 <?= e($s['target_responden']) ?></span><span>☑
                      <?= e($s['jumlah_pertanyaan']) ?> Pertanyaan</span></div>
                  <p><?= e($s['deskripsi'] ?: 'Silakan isi survey sesuai pengalaman dan penilaian Anda.') ?></p>
                  <div class="survey-period"><strong>Periode Survey</strong><span><?= e($s['tanggal_mulai'] ?: 'Terbuka') ?>
                      — <?= e($s['tanggal_selesai'] ?: 'Tidak ditentukan') ?></span></div>
                  <a class="survey-btn" href="/fikes/page/survey/isi-survey.php?slug=<?= rawurlencode($s['slug']) ?>">Isi
                    Survey <span>→</span></a>
                </div>
              </article>
          <?php endforeach;
          endif; ?>
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
          <a href="/fikes/page/akademik/akademik.php"> Akademik </a>

          <a href="#"> Kemahasiswaan </a>

          <a href="#"> Pelayanan FIKES </a>

          <a href="/fikes/page/survey/survey.php"> Survey </a>
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
  <script>
    let current = 0;
    let slides = [];
    let dots = [];

    function showSlide(n) {
      if (!slides.length) return;
      current = (n + slides.length) % slides.length;
      slides.forEach((s, i) => s.classList.toggle("active", i === current));
      dots.forEach((d, i) => d.classList.toggle("active", i === current));
    }

    function changeSlide(n) {
      showSlide(current + n);
    }

    function currentSlide(n) {
      showSlide(n - 1);
    }
    document.addEventListener("DOMContentLoaded", () => {
      slides = [...document.querySelectorAll(".slide")];
      dots = [...document.querySelectorAll(".slider-dot")];
      showSlide(0);
      if (slides.length > 1) setInterval(() => changeSlide(1), 7000);
      const t = document.getElementById("menuToggle"),
        m = document.getElementById("navMenu");
      if (t && m) t.onclick = () => m.classList.toggle("active");
      const nav = document.getElementById("navbar");
      window.addEventListener("scroll", () => {
        if (nav) nav.classList.toggle("scrolled", scrollY > 20);
        const b = document.getElementById("backTop");
        if (b) b.classList.toggle("show", scrollY > 500)
      });
      const b = document.getElementById("backTop");
      if (b) b.onclick = () => scrollTo({
        top: 0,
        behavior: "smooth"
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Label kolom otomatis untuk mode kartu di HP.
      document.querySelectorAll('.ak-table').forEach(function(table) {
        const headers = Array.from(table.querySelectorAll('thead th')).map(function(th) {
          return th.textContent.trim();
        });
        table.querySelectorAll('tbody tr').forEach(function(row) {
          Array.from(row.children).forEach(function(cell, index) {
            if (headers[index]) cell.setAttribute('data-label', headers[index]);
          });
        });
      });

      const f = document.getElementById('prodiFilter');
      if (f) {
        f.addEventListener('change', function() {
          document.querySelectorAll('#kurTable tbody tr').forEach(r => {
            r.style.display = !this.value || r.dataset.prodi === this.value ? '' : 'none';
          });
        });
      }
      document.querySelectorAll('.ak-side a').forEach(a => a.addEventListener('click', () => document
        .querySelectorAll('.ak-side a').forEach(x => x.classList.remove('active'))));
    });
  </script>

</body>

</html>
