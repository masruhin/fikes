<?php
$page_title = 'Unduh Logo';
require_once __DIR__ . '/../../config/auth.php';
$upload_dir = __DIR__ . "/../../uploads/upload-logo/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$pesan = "";
$error = "";

if (isset($_POST["upload_logo"])) {
  if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] != 0) {
    $error = "Silakan pilih file logo.";
  } else {
    $file = $_FILES["logo"];
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "gif", "svg", "webp", "ico", "bmp"];

    if ($file["size"] > 10 * 1024 * 1024) {
      $error = "Ukuran file maksimal 10 MB.";
    } elseif (!in_array($ext, $allowed)) {
      $error = "Format file tidak diperbolehkan.";
    } else {
      $nama = date("YmdHis") . "_" . uniqid() . "." . $ext;
      if (move_uploaded_file($file["tmp_name"], $upload_dir . $nama)) {
        $pesan = "Logo berhasil diupload.";
      } else {
        $error = "Upload logo gagal.";
      }
    }
  }
}

if (isset($_GET["hapus"])) {
  $nama = basename($_GET["hapus"]);
  if (is_file($upload_dir . $nama)) {
    unlink($upload_dir . $nama);
    $pesan = "Logo berhasil dihapus.";
  }
}

$logos = [];
foreach (scandir($upload_dir) as $file) {
  if ($file != "." && $file != ".." && is_file($upload_dir . $file)) $logos[] = $file;
}
rsort($logos);

include "../../includes/header.php";
?>

<style>
  .logo-box {
    background: #fff;
    border: 1px solid #e5eaf1;
    border-radius: 16px;
    padding: 30px
  }

  .upload-area {
    border: 2px dashed #d9e2ef;
    border-radius: 14px;
    padding: 35px;
    text-align: center;
    background: #fbfcfe
  }

  .logo-icon {
    width: 76px;
    height: 76px;
    margin: auto;
    border-radius: 18px;
    background: linear-gradient(135deg, #1683ff, #35a8ff);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    font-weight: bold
  }

  .logo-help {
    color: #7c8da5;
    font-size: 14px;
    margin: 10px 0
  }

  .logo-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-top: 25px
  }

  .logo-card {
    border: 1px solid #e5eaf1;
    border-radius: 14px;
    overflow: hidden;
    background: white
  }

  .logo-preview {
    height: 170px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
    background: #f8fafc
  }

  .logo-preview img {
    max-width: 100%;
    max-height: 135px;
    object-fit: contain
  }

  .logo-info {
    padding: 14px
  }

  .logo-name {
    font-size: 13px;
    font-weight: 600;
    word-break: break-all;
    margin-bottom: 10px
  }

  .logo-actions {
    display: flex;
    gap: 8px
  }

  .logo-actions a {
    flex: 1;
    text-align: center;
    text-decoration: none;
    padding: 9px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600
  }

  .btn-main,
  .btn-all {
    display: inline-block;
    padding: 12px 18px;
    border: 0;
    border-radius: 9px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer
  }

  .btn-main {
    background: #1683ff;
    color: #fff
  }

  .btn-all {
    background: #0b8b68;
    color: #fff
  }

  .btn-download {
    background: #0b8b68;
    color: #fff
  }

  .btn-delete {
    background: #fff1f1;
    color: #e53935
  }

  .alert-success,
  .alert-error {
    padding: 13px 16px;
    border-radius: 10px;
    margin-bottom: 20px
  }

  .alert-success {
    background: #eafaf4;
    color: #087653
  }

  .alert-error {
    background: #fff0f0;
    color: #c62828
  }

  @media(max-width:1000px) {
    .logo-grid {
      grid-template-columns: repeat(3, 1fr)
    }
  }

  @media(max-width:700px) {
    .logo-grid {
      grid-template-columns: repeat(2, 1fr)
    }
  }

  @media(max-width:480px) {
    .logo-grid {
      grid-template-columns: 1fr
    }
  }
</style>

<div class="page-content">
  <div class="page-title">
    <div>
      <div class="eyebrow">TENTANG FIKES</div>
      <h1>Logo Website</h1>
      <p>Pengaturan logo dan identitas visual.</p>
    </div>
    <?php if (count($logos) > 0): ?>
      <a href="download-all.php" class="btn-all">↓ Download All</a>
    <?php endif; ?>
  </div>

  <?php if ($pesan): ?><div class="alert-success"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="logo-box">
    <form method="post" enctype="multipart/form-data">
      <div class="upload-area">
        <div class="logo-icon">F</div>
        <h2>Upload Logo FIKES</h2>
        <p class="logo-help">Upload logo utama untuk digunakan pada website publik.</p>
        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.gif,.svg,.webp,.ico,.bmp" required>
        <br><br>
        <button type="submit" name="upload_logo" class="btn-main">Upload Logo</button>
        <div class="logo-help">JPG, JPEG, PNG, GIF, SVG, WEBP, ICO, BMP — maksimal 10 MB.</div>
      </div>
    </form>

    <?php if (count($logos) > 0): ?>
      <h2 style="margin-top:30px">Daftar Logo</h2>
      <div class="logo-grid">
        <?php foreach ($logos as $logo): ?>
          <div class="logo-card">
            <div class="logo-preview">
              <img src="../../uploads/upload-logo/<?= rawurlencode($logo) ?>" alt="Logo FIKES">
            </div>
            <div class="logo-info">
              <div class="logo-name"><?= htmlspecialchars($logo) ?></div>
              <div class="logo-actions">
                <a class="btn-download" href="../../uploads/upload-logo/<?= rawurlencode($logo) ?>" download>↓ Download</a>
                <a class="btn-delete" href="logo.php?hapus=<?= rawurlencode($logo) ?>"
                  onclick="return confirm('Hapus logo ini?')">Hapus</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div style="text-align:center;padding:45px 20px;color:#7c8da5">Belum ada logo yang diupload.</div>
    <?php endif; ?>
  </div>
</div>

<?php include "../../includes/footer.php"; ?>
