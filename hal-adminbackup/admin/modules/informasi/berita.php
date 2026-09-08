<?php $page_title = 'Berita & Informasi';
require_once __DIR__ . '/../../config/auth.php';
if (isset($_GET['hapus'])) {
  $s = $pdo->prepare("DELETE FROM berita WHERE id=?");
  $s->execute([(int)$_GET['hapus']]);
  header('Location:berita.php');
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id'];
  $d = [trim($_POST['judul']), trim($_POST['isi']), $_POST['status']];
  if ($id) {
    $s = $pdo->prepare("UPDATE berita SET judul=?,isi=?,status=? WHERE id=?");
    $s->execute([...$d, $id]);
  } else {
    $s = $pdo->prepare("INSERT INTO berita(judul,isi,status) VALUES(?,?,?)");
    $s->execute($d);
  }
  header('Location:berita.php');
  exit;
}
$data = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC")->fetchAll();
require __DIR__ . '/../../includes/header.php'; ?>
<div class="page-head">
  <div><span class="eyebrow">INFORMASI</span>
    <h1>Berita & Informasi</h1>
    <p>Publikasikan berita terbaru FIKES.</p>
  </div><button class="btn primary" onclick="openModal()">+ Tulis Berita</button>
</div>
<div class="panel table-panel">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Judul</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody><?php foreach ($data as $d): ?><tr>
            <td><strong><?= e($d['judul']) ?></strong></td>
            <td><span class="badge"><?= e($d['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
            <td><button class="btn small" onclick='editItem(<?= json_encode($d) ?>)'>Edit</button> <a class="btn small danger-text" href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Hapus berita?')">Hapus</a></td>
          </tr><?php endforeach; ?></tbody>
    </table>
  </div>
</div>
<div class="modal" id="modal">
  <div class="modal-box large">
    <div class="modal-head">
      <h2>Berita</h2><button onclick="closeModal()">×</button>
    </div>
    <form method="post"><input type="hidden" name="id" id="id"><label>Judul Berita</label><input name="judul" id="judul" required><label>Isi Berita</label><textarea name="isi" id="isi" rows="12"></textarea><label>Status</label><select name="status" id="status">
        <option>draft</option>
        <option>publish</option>
      </select>
      <div class="modal-actions"><button type="button" class="btn" onclick="closeModal()">Batal</button><button class="btn primary">Simpan</button></div>
    </form>
  </div>
</div>
<script>
  function openModal() {
    modal.classList.add('show')
  }

  function closeModal() {
    modal.classList.remove('show')
  }

  function editItem(d) {
    openModal();
    ['id', 'judul', 'isi', 'status'].forEach(k => document.getElementById(k).value = d[k] ?? '')
  }
</script>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
