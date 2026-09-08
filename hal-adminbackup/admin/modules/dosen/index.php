<?php
$page_title = 'Daftar Dosen';
require_once __DIR__ . '/../../config/auth.php';

if (isset($_GET['hapus'])) {
  $stmt = $pdo->prepare("DELETE FROM dosen WHERE id=?");
  $stmt->execute([(int)$_GET['hapus']]);
  header('Location: index.php?ok=deleted');
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  $data = [trim($_POST['nidn']), trim($_POST['nama']), trim($_POST['program_studi']), trim($_POST['jabatan']), trim($_POST['email']), $_POST['status']];
  if ($id) {
    $stmt = $pdo->prepare("UPDATE dosen SET nidn=?,nama=?,program_studi=?,jabatan=?,email=?,status=? WHERE id=?");
    $stmt->execute([...$data, $id]);
  } else {
    $stmt = $pdo->prepare("INSERT INTO dosen(nidn,nama,program_studi,jabatan,email,status) VALUES(?,?,?,?,?,?)");
    $stmt->execute($data);
  }
  header('Location: index.php?ok=saved');
  exit;
}
$prodi_filter=trim($_GET['prodi']??''); if($prodi_filter!==''){ $st=$pdo->prepare('SELECT * FROM dosen WHERE program_studi=? ORDER BY nama'); $st->execute([$prodi_filter]); $dosen=$st->fetchAll(); } else { $dosen=$pdo->query('SELECT * FROM dosen ORDER BY nama')->fetchAll(); }
require __DIR__ . '/../../includes/header.php';
?>
<div class="page-head">
  <div><span class="eyebrow">AKADEMIK</span>
    <h1><?= e($prodi_filter ? "Dosen " . $prodi_filter : "Daftar Dosen") ?></h1>
    <p>Kelola tenaga pengajar berdasarkan program studi.</p>
  </div><button class="btn primary" onclick="openModal()">+ Tambah Dosen</button>
</div>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Data berhasil diproses.</div><?php endif; ?>
<div class="panel table-panel">
  <div class="table-tools"><input id="search" onkeyup="filterTable()" placeholder="Cari nama, NIDN, atau program studi..."></div>
  <div class="table-wrap">
    <table id="dataTable">
      <thead>
        <tr>
          <th>Nama</th>
          <th>NIDN</th>
          <th>Program Studi</th>
          <th>Jabatan</th>
          <th>Email</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dosen as $d): ?>
          <tr>
            <td><strong><?= e($d['nama']) ?></strong></td>
            <td><?= e($d['nidn']) ?></td>
            <td><?= e($d['program_studi']) ?></td>
            <td><?= e($d['jabatan']) ?></td>
            <td><?= e($d['email']) ?></td>
            <td><span class="badge success"><?= e($d['status']) ?></span></td>
            <td class="actions"><button class="btn small" onclick='editDosen(<?= json_encode($d) ?>)'>Edit</button> <a class="btn small danger-text" href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal" id="modal">
  <div class="modal-box">
    <div class="modal-head">
      <h2 id="modalTitle">Tambah Dosen</h2><button onclick="closeModal()">×</button>
    </div>
    <form method="post"><input type="hidden" name="id" id="id">
      <div class="form-grid">
        <div><label>NIDN</label><input name="nidn" id="nidn"></div>
        <div><label>Nama</label><input name="nama" id="nama" required></div>
        <div><label>Program Studi</label><select name="program_studi" id="program_studi">
            <option>Keperawatan</option>
            <option>Kebidanan</option>
            <option>Farmasi</option>
            <option>K3</option>
          </select></div>
        <div><label>Jabatan</label><input name="jabatan" id="jabatan"></div>
        <div><label>Email</label><input type="email" name="email" id="email"></div>
        <div><label>Status</label><select name="status" id="status">
            <option>aktif</option>
            <option>nonaktif</option>
          </select></div>
      </div>
      <div class="modal-actions"><button type="button" class="btn" onclick="closeModal()">Batal</button><button class="btn primary">Simpan</button></div>
    </form>
  </div>
</div>
<script>
  function openModal() {
    document.getElementById('modal').classList.add('show');
  }

  function closeModal() {
    document.getElementById('modal').classList.remove('show');
  }

  function editDosen(d) {
    openModal();
    document.getElementById('modalTitle').textContent = 'Edit Dosen';
    for (const k of ['id', 'nidn', 'nama', 'program_studi', 'jabatan', 'email', 'status']) document.getElementById(k).value = d[k] ?? '';
  }

  function filterTable() {
    let q = document.getElementById('search').value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(r => r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none');
  }
</script>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
