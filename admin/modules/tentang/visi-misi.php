<?php
$page_title = 'Visi & Misi';
require_once __DIR__ . '/../../config/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $visi = trim($_POST['visi']);
  $s = $pdo->prepare("UPDATE visi_misi SET visi=? WHERE id=1");
  $s->execute([$visi]);
  $pdo->exec("TRUNCATE TABLE misi");
  $s = $pdo->prepare("INSERT INTO misi(nomor,judul,isi) VALUES(?,?,?)");
  foreach ($_POST['judul'] ?? [] as $i => $judul) if (trim($judul)) $s->execute([$i + 1, trim($judul), trim($_POST['isi'][$i])]);
  header('Location:visi-misi.php?saved=1');
  exit;
}
$visi = $pdo->query("SELECT * FROM visi_misi WHERE id=1")->fetch();
$misi = $pdo->query("SELECT * FROM misi ORDER BY nomor")->fetchAll();
require __DIR__ . '/../../includes/header.php'; ?>
<div class="page-head">
  <div><span class="eyebrow">TENTANG FIKES</span>
    <h1>Visi & Misi</h1>
    <p>Konten ini terhubung dengan halaman profil FIKES.</p>
  </div>
</div>
<?php if (isset($_GET['saved'])): ?><div class="alert success">Visi dan misi berhasil disimpan.</div><?php endif; ?>
<form method="post">
  <div class="panel">
    <div class="panel-head">
      <div><span class="eyebrow">VISI FIKES</span>
        <h2>Visi</h2>
      </div>
    </div><textarea name="visi" rows="5" required><?= e($visi['visi']) ?></textarea>
  </div>
  <div class="panel">
    <div class="panel-head">
      <div><span class="eyebrow">MISI FIKES</span>
        <h2>Daftar Misi</h2>
      </div>
    </div>
    <div id="misiWrap"><?php foreach ($misi as $i => $m): ?><div class="mission-row">
          <div class="mission-number"><?= sprintf('%02d', $i + 1) ?></div>
          <div><input name="judul[]" value="<?= e($m['judul']) ?>" placeholder="Judul misi"><textarea name="isi[]"
              rows="3" placeholder="Isi misi"><?= e($m['isi']) ?></textarea></div>
        </div><?php endforeach; ?></div>
    <button type="button" class="btn" onclick="addMission()">+ Tambah Misi</button>
  </div><button class="btn primary">Simpan Perubahan</button>
</form>
<script>
  function addMission() {
    let n = document.querySelectorAll('.mission-row').length + 1;
    document.getElementById('misiWrap').insertAdjacentHTML('beforeend',
      `<div class="mission-row"><div class="mission-number">${String(n).padStart(2,'0')}</div><div><input name="judul[]" placeholder="Judul misi"><textarea name="isi[]" rows="3" placeholder="Isi misi"></textarea></div></div>`
    )
  }
</script>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
