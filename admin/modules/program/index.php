<?php
$page_title = 'Program Studi';
require_once __DIR__ . '/../../config/auth.php';
if (isset($_GET['hapus'])) {
  $s = $pdo->prepare("DELETE FROM program_studi WHERE id=?");
  $s->execute([(int)$_GET['hapus']]);
  header('Location:index.php');
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id'];
  $d = [trim($_POST['nama']), trim($_POST['jenjang']), trim($_POST['gelar']), trim($_POST['deskripsi']), $_POST['status']];
  if ($id) {
    $s = $pdo->prepare("UPDATE program_studi SET nama=?,jenjang=?,gelar=?,deskripsi=?,status=? WHERE id=?");
    $s->execute([...$d, $id]);
  } else {
    $s = $pdo->prepare("INSERT INTO program_studi(nama,jenjang,gelar,deskripsi,status) VALUES(?,?,?,?,?)");
    $s->execute($d);
  }
  header('Location:index.php');
  exit;
}
$jenjang_filter = trim($_GET['jenjang'] ?? '');
$nama_filter = trim($_GET['nama'] ?? '');
$sql = 'SELECT * FROM program_studi WHERE 1=1';
$params = [];
if ($jenjang_filter !== '') {
  $sql .= ' AND jenjang=?';
  $params[] = $jenjang_filter;
}
if ($nama_filter !== '') {
  $sql .= ' AND nama LIKE ?';
  $params[] = '%' . $nama_filter . '%';
}
$sql .= ' ORDER BY nama';
$st = $pdo->prepare($sql);
$st->execute($params);
$data = $st->fetchAll();
require __DIR__ . '/../../includes/header.php'; ?>
<div class="page-head">
  <div><span class="eyebrow">AKADEMIK</span>
    <h1>
      <?= e($jenjang_filter ? "Program " . $jenjang_filter : "Program Studi") ?><?= e($nama_filter ? " - " . $nama_filter : "") ?>
    </h1>
    <p>Kelola seluruh program pendidikan FIKES.</p>
  </div><button class="btn primary" onclick="openModal()">+ Tambah Program</button>
</div>
<div class="panel table-panel">
  <div class="table-tools"><input id="search" onkeyup="filterTable()" placeholder="Cari program studi..."></div>
  <div class="table-wrap">
    <table id="dataTable">
      <thead>
        <tr>
          <th>Program Studi</th>
          <th>Jenjang</th>
          <th>Gelar</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $d): ?><tr>
            <td><strong><?= e($d['nama']) ?></strong></td>
            <td><?= e($d['jenjang']) ?></td>
            <td><?= e($d['gelar']) ?></td>
            <td><span class="badge success"><?= e($d['status']) ?></span></td>
            <td><button class="btn small" onclick='editProgram(<?= json_encode($d) ?>)'>Edit</button> <a
                class="btn small danger-text" href="?hapus=<?= $d['id'] ?>"
                onclick="return confirm('Hapus program?')">Hapus</a></td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="modal" id="modal">
  <div class="modal-box">
    <div class="modal-head">
      <h2>Program Studi</h2><button onclick="closeModal()">×</button>
    </div>
    <form method="post"><input type="hidden" name="id" id="id">
      <div class="form-grid">
        <div><label>Nama</label><input name="nama" id="nama" required></div>
        <div><label>Jenjang</label><select name="jenjang" id="jenjang">
            <option>Profesi</option>
            <option>Sarjana</option>
            <option>Diploma</option>
          </select></div>
        <div><label>Gelar</label><input name="gelar" id="gelar"></div>
        <div><label>Status</label><select name="status" id="status">
            <option>aktif</option>
            <option>nonaktif</option>
          </select></div>
      </div><label>Deskripsi</label><textarea name="deskripsi" id="deskripsi"></textarea>
      <div class="modal-actions"><button type="button" class="btn" onclick="closeModal()">Batal</button><button
          class="btn primary">Simpan</button></div>
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

  function editProgram(d) {
    openModal();
    ['id', 'nama', 'jenjang', 'gelar', 'deskripsi', 'status'].forEach(k => document.getElementById(k).value = d[k] ?? '')
  }

  function filterTable() {
    let q = search.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(r => r.style.display = r.innerText.toLowerCase().includes(
      q) ? '' : 'none')
  }
</script><?php require __DIR__ . '/../../includes/footer.php'; ?>
