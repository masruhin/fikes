<?php
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../../admin/config/database.php';
function survey_e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function survey_defaults($tipe){if($tipe==='skala')return [['Sangat Tidak Puas',1],['Tidak Puas',2],['Cukup',3],['Puas',4],['Sangat Puas',5]];if($tipe==='ya_tidak')return [['Ya',1],['Tidak',0]];return [];}
$slug=trim($_GET['slug']??'');
$st=$pdo->prepare("SELECT * FROM survey WHERE slug=? AND status='aktif' AND (tanggal_mulai IS NULL OR tanggal_mulai<=CURDATE()) AND (tanggal_selesai IS NULL OR tanggal_selesai>=CURDATE()) LIMIT 1");$st->execute([$slug]);$survey=$st->fetch(PDO::FETCH_ASSOC);
if(!$survey){http_response_code(404);exit('Survey tidak ditemukan atau sudah ditutup.');}
$st=$pdo->prepare("SELECT * FROM survey_pertanyaan WHERE survey_id=? AND status='aktif' ORDER BY nomor_urut,id");$st->execute([$survey['id']]);$questions=$st->fetchAll(PDO::FETCH_ASSOC);
$ids=array_column($questions,'id');$choices=[];
if($ids){$in=implode(',',array_fill(0,count($ids),'?'));$q=$pdo->prepare("SELECT * FROM survey_pilihan WHERE pertanyaan_id IN($in) ORDER BY nomor_urut,id");$q->execute($ids);foreach($q->fetchAll(PDO::FETCH_ASSOC) as $c)$choices[$c['pertanyaan_id']][]=$c;}
foreach($questions as $q){$qid=(int)$q['id'];if(!empty($choices[$qid]))continue;foreach(survey_defaults($q['tipe']) as $i=>$d)$choices[$qid][]= ['id'=>'default-'.$qid.'-'.$i,'pertanyaan_id'=>$qid,'label'=>$d[0],'nilai'=>$d[1],'nomor_urut'=>$i+1];}
$success=false;$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(!hash_equals($_SESSION['survey_token_'.$survey['id']]??'',$_POST['token']??''))throw new Exception('Sesi survey tidak valid. Silakan muat ulang halaman.');
  $pdo->beginTransaction();
  $st=$pdo->prepare("INSERT INTO survey_responden(survey_id,nama,email,kategori_responden,identitas,ip_address) VALUES(?,?,?,?,?,?)");$st->execute([$survey['id'],trim($_POST['nama']??''),trim($_POST['email']??''),trim($_POST['kategori_responden']??''),trim($_POST['identitas']??''),$_SERVER['REMOTE_ADDR']??null]);$rid=(int)$pdo->lastInsertId();
  foreach($questions as $q){$v=$_POST['q'][$q['id']]??'';if((int)$q['wajib'] && trim((string)$v)==='')throw new Exception('Semua pertanyaan wajib harus diisi.');$pid=null;$nilai=null;$text=null;if(in_array($q['tipe'],['skala','pilihan_ganda','ya_tidak'],true)&&$v!==''){$sel=$pdo->prepare('SELECT id,nilai FROM survey_pilihan WHERE id=? AND pertanyaan_id=?');$sel->execute([(int)$v,$q['id']]);$row=$sel->fetch(PDO::FETCH_ASSOC);if($row){$pid=(int)$row['id'];$nilai=$row['nilai'];}else{$defaults=survey_defaults($q['tipe']);$idx=is_numeric($v)?(int)$v:-1;if(isset($defaults[$idx])){$nilai=$defaults[$idx][1];$pid=null;}}}else{$text=trim((string)$v);} $ins=$pdo->prepare('INSERT INTO survey_jawaban(responden_id,pertanyaan_id,pilihan_id,jawaban_text,nilai) VALUES(?,?,?,?,?)');$ins->execute([$rid,$q['id'],$pid,$text,$nilai]);}
  $pdo->commit();$success=true;unset($_SESSION['survey_token_'.$survey['id']]);
 }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();$error=$ex->getMessage();}
}
if(empty($_SESSION['survey_token_'.$survey['id']]))$_SESSION['survey_token_'.$survey['id']]=bin2hex(random_bytes(24));$token=$_SESSION['survey_token_'.$survey['id']];
?>
<!doctype html>
<html lang="id">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Isi Survey | FIKES - Fakultas Ilmu Kesehatan</title>

    <meta name="description"
      content="Website resmi Fakultas Ilmu Kesehatan - Informasi akademik, program studi, kemahasiswaan, pelayanan dan informasi FIKES." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


    /* =========================================================
       SURVEY FORM — TATA LETAK KHUSUS HALAMAN ISI SURVEY
    ========================================================= */
    .survey-hero {
      min-height: 360px;
      display: flex;
      align-items: center;
      background:
        radial-gradient(circle at 88% 22%, rgba(19, 168, 120, .12) 0 110px, transparent 111px),
        radial-gradient(circle at 92% 62%, rgba(19, 168, 120, .08) 0 180px, transparent 181px),
        linear-gradient(135deg, #f7fbf9 0%, #ffffff 58%, #eef8f4 100%);
    }

    .survey-hero .container {
      padding: 72px 0 68px;
    }

    .survey-hero .breadcrumb {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
      color: #71817b;
      font-size: 13px;
      font-weight: 600;
    }

    .survey-hero .breadcrumb span {
      color: #52635d;
    }

    .survey-hero .breadcrumb b {
      color: #a0ada8;
      font-weight: 500;
    }

    .survey-hero .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .14em;
      margin-bottom: 10px;
    }

    .survey-hero .eyebrow::before {
      content: "";
      width: 30px;
      height: 3px;
      border-radius: 99px;
      background: var(--secondary);
    }

    .survey-hero h1 {
      max-width: 900px;
      font-size: clamp(38px, 5vw, 64px);
      margin-bottom: 15px;
      letter-spacing: -.035em;
    }

    .survey-hero p {
      max-width: 780px;
      font-size: 17px;
      color: #52635d;
    }

    .survey-form-section {
      background: #f6faf8;
      padding: 70px 0 90px;
    }

    .survey-form-card {
      width: min(100%, 980px);
      margin: 0 auto;
      background: #fff;
      border: 1px solid #e1ebe6;
      border-radius: 24px;
      box-shadow: 0 18px 55px rgba(18, 55, 42, .09);
      overflow: hidden;
    }

    .survey-form-card>form,
    .survey-form-card>h2,
    .survey-form-card>.desc,
    .survey-form-card>.survey-info,
    .survey-form-card>.survey-alert,
    .survey-form-card>.success-panel {
      margin-left: 0;
      margin-right: 0;
    }

    .survey-form-card>h2 {
      padding: 34px 38px 0;
      font-size: 28px;
      letter-spacing: -.02em;
    }

    .survey-form-card>.desc {
      padding: 0 38px;
      margin-top: 8px;
      color: #66756f;
      font-size: 14px;
    }

    .survey-form-card>.desc strong {
      color: #c2413a;
    }

    .survey-info {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
      padding: 24px 38px 30px;
    }

    .survey-info-item {
      min-width: 0;
      padding: 16px 18px;
      border: 1px solid #e4eee9;
      border-radius: 14px;
      background: #f8fbfa;
    }

    .survey-info-item span,
    .survey-info-item strong {
      display: block;
    }

    .survey-info-item span {
      margin-bottom: 5px;
      color: #788781;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .survey-info-item strong {
      color: var(--dark);
      font-size: 13px;
      line-height: 1.5;
      overflow-wrap: anywhere;
    }

    #surveyForm {
      padding: 0 38px 38px;
    }

    .respondent-box {
      margin: 0 0 28px;
      padding: 25px;
      border: 1px solid #dfeae5;
      border-radius: 18px;
      background: linear-gradient(180deg, #fbfdfc, #f7faf9);
    }

    .respondent-box h3 {
      margin-bottom: 5px;
      font-size: 20px;
    }

    .respondent-box>p {
      margin-bottom: 20px;
      color: #72817c;
      font-size: 13px;
    }

    .respondent-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px 20px;
    }

    .survey-field {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .survey-field label {
      color: #344b43;
      font-size: 13px;
      font-weight: 700;
    }

    .survey-field label small {
      color: #8a9893;
      font-weight: 500;
    }

    .survey-field input,
    .survey-field select,
    .survey-field textarea {
      width: 100%;
      border: 1px solid #d7e3de;
      border-radius: 11px;
      background: #fff;
      color: #263d35;
      font: inherit;
      font-size: 14px;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }

    .survey-field input,
    .survey-field select {
      min-height: 46px;
      padding: 10px 13px;
    }

    .survey-field textarea {
      min-height: 125px;
      padding: 12px 13px;
      resize: vertical;
      line-height: 1.6;
    }

    .survey-field input:focus,
    .survey-field select:focus,
    .survey-field textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(8, 127, 91, .10);
    }

    .question-list {
      display: grid;
      gap: 18px;
    }

    .survey-question {
      padding: 24px 25px;
      border: 1px solid #e0eae5;
      border-radius: 18px;
      background: #fff;
      box-shadow: 0 8px 24px rgba(18, 55, 42, .045);
    }

    .question-title {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      color: #213b31;
      font-size: 16px;
      font-weight: 700;
      line-height: 1.6;
    }

    .question-number {
      width: 32px;
      height: 32px;
      flex: 0 0 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 13px;
      font-weight: 800;
    }

    .required {
      color: #c2413a;
      margin-left: 2px;
    }

    .survey-options {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin: 18px 0 0 44px;
    }

    .survey-opt {
      display: flex;
      align-items: center;
      gap: 10px;
      min-height: 50px;
      padding: 11px 14px;
      border: 1px solid #dce7e2;
      border-radius: 12px;
      background: #fbfdfc;
      color: #41554e;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all .2s ease;
    }

    .survey-opt:hover {
      border-color: #a9cfc0;
      background: #f5fbf8;
    }

    .survey-opt input {
      width: 17px;
      height: 17px;
      margin: 0;
      accent-color: var(--primary);
      flex: 0 0 auto;
    }

    .survey-opt:has(input:checked) {
      border-color: var(--primary);
      background: var(--primary-light);
      color: var(--dark);
      box-shadow: 0 0 0 2px rgba(8, 127, 91, .06);
    }

    .survey-question>.survey-field {
      margin: 18px 0 0 44px;
    }

    .empty-choice {
      margin-top: 14px;
      padding: 13px 15px;
      border: 1px dashed #e0b56d;
      border-radius: 10px;
      background: #fffaf0;
      color: #8a6728;
      font-size: 13px;
    }

    .survey-submit-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 15px;
      margin-top: 28px;
      padding-top: 25px;
      border-top: 1px solid #e7eeeb;
    }

    .survey-back {
      color: var(--primary);
      font-size: 13px;
      font-weight: 700;
    }

    .survey-back:hover {
      text-decoration: underline;
    }

    .survey-submit {
      min-height: 48px;
      padding: 12px 22px;
      border: 0;
      border-radius: 11px;
      background: var(--primary);
      color: #fff;
      font: inherit;
      font-size: 13px;
      font-weight: 800;
      cursor: pointer;
      box-shadow: 0 8px 20px rgba(8, 127, 91, .18);
      transition: transform .2s, background .2s;
    }

    .survey-submit:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    .survey-alert {
      margin: 28px 38px 0;
      padding: 14px 16px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 600;
    }

    .survey-alert.err {
      border: 1px solid #f0c7c3;
      background: #fff5f4;
      color: #a3342c;
    }

    .success-panel {
      padding: 65px 38px;
      text-align: center;
    }

    .success-icon {
      width: 70px;
      height: 70px;
      margin: 0 auto 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 34px;
      font-weight: 800;
    }

    .success-panel h2 {
      margin-bottom: 8px;
      font-size: 28px;
    }

    .success-panel p {
      margin-bottom: 24px;
      color: #6d7c76;
    }

    .success-actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .success-primary,
    .success-secondary {
      padding: 11px 17px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 800;
    }

    .success-primary {
      background: var(--primary);
      color: #fff;
    }

    .success-secondary {
      border: 1px solid #d9e4df;
      color: var(--primary);
      background: #fff;
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


    @media(max-width:900px) {
      .survey-info {
        grid-template-columns: 1fr;
      }

      .survey-options {
        grid-template-columns: 1fr;
      }
    }

    @media(max-width:600px) {
      .survey-hero .container {
        padding: 52px 0 48px;
      }

      .survey-hero h1 {
        font-size: 36px;
      }

      .survey-hero p {
        font-size: 15px;
      }

      .survey-form-section {
        padding: 35px 0 55px;
      }

      .survey-form-card {
        border-radius: 18px;
      }

      .survey-form-card>h2 {
        padding: 26px 20px 0;
        font-size: 23px;
      }

      .survey-form-card>.desc {
        padding: 0 20px;
        font-size: 13px;
      }

      .survey-info {
        padding: 20px;
      }

      #surveyForm {
        padding: 0 20px 25px;
      }

      .respondent-box {
        padding: 18px;
      }

      .respondent-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }

      .survey-question {
        padding: 19px;
      }

      .question-title {
        font-size: 14px;
      }

      .survey-options {
        margin-left: 0;
      }

      .survey-question>.survey-field {
        margin-left: 0;
      }

      .survey-submit-row {
        flex-direction: column-reverse;
        align-items: stretch;
      }

      .survey-submit {
        width: 100%;
      }

      .survey-back {
        text-align: center;
      }

      .survey-alert {
        margin: 20px;
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
          <div class="breadcrumb">⌂ <span>Beranda</span><b>›</b><span>Survey</span><b>›</b><span>Isi Survey</span></div>
          <span class="eyebrow">FORMULIR SURVEY FIKES</span>
          <h1><?=survey_e($survey['judul'])?></h1>
          <p>
            <?=survey_e($survey['deskripsi'] ?: 'Silakan isi survey berikut dengan jawaban yang sesuai berdasarkan pengalaman Anda.')?>
          </p>
        </div>
      </section>
      <section class="survey-form-section">
        <div class="container">
          <div class="survey-form-card">
            <?php if($success): ?>
            <div class="success-panel">
              <div class="success-icon">✓</div>
              <h2>Survey Berhasil Dikirim</h2>
              <p>Terima kasih. Jawaban survey Anda berhasil disimpan.</p>
              <div class="success-actions"><a class="success-primary" href="/fikes/page/survey/survey.php">Kembali ke
                  Daftar Survey</a><a class="success-secondary" href="/fikes/index.php">Kembali ke Beranda</a></div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Jawaban survey Anda berhasil disimpan.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#087f5b',
                allowOutsideClick: false
              });
            });
            </script>
            <?php else: ?>
            <?php if($error): ?><div class="survey-alert err"><?=survey_e($error)?></div><?php endif; ?>
            <h2>Isi Formulir Survey</h2>
            <p class="desc">Lengkapi data responden dan jawab seluruh pertanyaan sesuai pengalaman Anda. Pertanyaan
              bertanda <strong>*</strong> wajib diisi.</p>
            <div class="survey-info">
              <div class="survey-info-item"><span>Target
                  Responden</span><strong><?=survey_e($survey['target_responden'])?></strong></div>
              <div class="survey-info-item"><span>Jumlah Pertanyaan</span><strong><?=count($questions)?>
                  Pertanyaan</strong></div>
              <div class="survey-info-item">
                <span>Periode</span><strong><?=survey_e($survey['tanggal_mulai'] ?: 'Terbuka')?> —
                  <?=survey_e($survey['tanggal_selesai'] ?: 'Tidak ditentukan')?></strong>
              </div>
            </div>
            <form method="post" id="surveyForm">
              <input type="hidden" name="token" value="<?=survey_e($token)?>">
              <div class="respondent-box">
                <h3>Data Responden</h3>
                <p>Data identitas digunakan hanya untuk kebutuhan pengelolaan hasil survey.</p>
                <div class="respondent-grid">
                  <div class="survey-field"><label>Nama</label><input name="nama"
                      value="<?=survey_e($_POST['nama']??'')?>" placeholder="Masukkan nama"></div>
                  <div class="survey-field"><label>Email</label><input type="email" name="email"
                      value="<?=survey_e($_POST['email']??'')?>" placeholder="nama@email.com"></div>
                  <div class="survey-field"><label>Kategori Responden</label><select name="kategori_responden">
                      <option value="">Pilih kategori</option>
                      <?php foreach(['Mahasiswa','Karyawan/Pegawai','Dosen','Alumni','Masyarakat','Umum'] as $v): ?>
                      <option value="<?=survey_e($v)?>" <?=($_POST['kategori_responden']??'')===$v?'selected':''?>>
                        <?=survey_e($v)?></option><?php endforeach;?>
                    </select></div>
                  <div class="survey-field"><label>Identitas/NIM/NIP <small>(opsional)</small></label><input
                      name="identitas" value="<?=survey_e($_POST['identitas']??'')?>"
                      placeholder="Masukkan identitas bila diperlukan"></div>
                </div>
              </div>
              <?php if(!$questions): ?><div class="empty-choice">Survey ini belum memiliki pertanyaan aktif.</div>
              <?php else: ?>
              <div class="question-list">
                <?php foreach($questions as $i=>$q): ?><article class="survey-question">
                  <div class="question-title"><span
                      class="question-number"><?=($i+1)?></span><?=survey_e($q['pertanyaan'])?><?php if((int)$q['wajib']): ?><span
                      class="required">*</span><?php endif;?></div>
                  <?php if(in_array($q['tipe'],['skala','pilihan_ganda','ya_tidak'],true)): ?><div
                    class="survey-options"><?php foreach(($choices[$q['id']]??[]) as $c): ?><label
                      class="survey-opt"><input type="radio" name="q[<?=$q['id']?>]" value="<?=survey_e($c['id'])?>"
                        <?=((string)($_POST['q'][$q['id']]??'')===(string)$c['id'])?'checked':''?>><span><?=survey_e($c['label'])?></span></label><?php endforeach; if(empty($choices[$q['id']])): ?>
                    <div class="empty-choice">Pilihan jawaban belum dibuat untuk pertanyaan ini.</div><?php endif;?>
                  </div>
                  <?php elseif($q['tipe']==='isian'): ?><div class="survey-field"><input type="text"
                      name="q[<?=$q['id']?>]" value="<?=survey_e($_POST['q'][$q['id']]??'')?>"
                      placeholder="Tulis jawaban Anda..."></div>
                  <?php else: ?><div class="survey-field"><textarea name="q[<?=$q['id']?>]"
                      placeholder="Tulis jawaban Anda..."><?=survey_e($_POST['q'][$q['id']]??'')?></textarea></div>
                  <?php endif; ?>
                </article><?php endforeach; ?></div>
              <div class="survey-submit-row"><a class="survey-back" href="/fikes/page/survey/survey.php">← Kembali ke
                  Survey</a><button class="survey-submit" type="submit">Kirim Jawaban Survey</button></div>
              <?php endif; ?>
            </form>
            <?php endif; ?>
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
