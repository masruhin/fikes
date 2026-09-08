<?php
$page_title = 'Struktur Organisasi';
require_once __DIR__ . '/../../config/auth.php';
wajib_login();

// URL project dihitung di sini karena header.php belum dipanggil.
$project_url = '';
if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) {
  $project_url = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/admin/'));
}
$upload_dir = __DIR__ . '/../../uploads/struktur/';
$upload_url = $project_url . '/admin/uploads/struktur/';

if (!is_dir($upload_dir)) {
  mkdir($upload_dir, 0755, true);
}

$pesan = '';
$tipe = 'success';

function hapus_file_struktur($nama_file)
{
  global $upload_dir;
  if ($nama_file && file_exists($upload_dir . $nama_file)) {
    unlink($upload_dir . $nama_file);
  }
}

if (isset($_GET['hapus'])) {
  $id = (int) $_GET['hapus'];
  $stmt = $pdo->prepare("SELECT gambar FROM struktur_organisasi WHERE id=?");
  $stmt->execute([$id]);
  $data_hapus = $stmt->fetch();

  if ($data_hapus) {
    $stmt = $pdo->prepare("DELETE FROM struktur_organisasi WHERE id=?");
    $stmt->execute([$id]);
    hapus_file_struktur($data_hapus['gambar']);
    header('Location: struktur.php?pesan=hapus');
    exit;
  }

  header('Location: struktur.php?pesan=gagal');
  exit;
}

if (isset($_GET['pesan'])) {
  if ($_GET['pesan'] === 'simpan') $pesan = 'Data struktur organisasi berhasil disimpan.';
  if ($_GET['pesan'] === 'hapus') $pesan = 'Data struktur organisasi berhasil dihapus.';
  if ($_GET['pesan'] === 'gagal') {
    $pesan = 'Data tidak ditemukan.';
    $tipe = 'danger';
  }
  if ($_GET['pesan'] === 'format') {
    $pesan = 'Format gambar harus JPG, JPEG, PNG, atau WEBP.';
    $tipe = 'danger';
  }
  if ($_GET['pesan'] === 'ukuran') {
    $pesan = 'Ukuran gambar maksimal 3 MB.';
    $tipe = 'danger';
  }
}

$edit = null;
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM struktur_organisasi WHERE id=?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
  if (!$edit) {
    $pesan = 'Data struktur tidak ditemukan.';
    $tipe = 'danger';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int) ($_POST['id'] ?? 0);
  $periode = trim($_POST['periode'] ?? '');
  $sk_rektor = trim($_POST['sk_rektor'] ?? '');
  $dekan = trim($_POST['dekan'] ?? '');
  $wakil_akademik = trim($_POST['wakil_dekan_akademik'] ?? '');
  $wakil_adum = trim($_POST['wakil_dekan_adum_keu'] ?? '');
  $wakil_kemahasiswaan = trim($_POST['wakil_dekan_kemahasiswaan'] ?? '');

  $gambar_lama = '';
  if ($id) {
    $stmt = $pdo->prepare("SELECT gambar FROM struktur_organisasi WHERE id=?");
    $stmt->execute([$id]);
    $lama = $stmt->fetch();
    $gambar_lama = $lama['gambar'] ?? '';
  }

  $gambar_baru = $gambar_lama;

  if (!empty($_FILES['gambar']['name'])) {
    $nama_asli = $_FILES['gambar']['name'];
    $ukuran = (int) $_FILES['gambar']['size'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed, true)) {
      header('Location: struktur.php?pesan=format');
      exit;
    }
    if ($ukuran > 3 * 1024 * 1024) {
      header('Location: struktur.php?pesan=ukuran');
      exit;
    }

    $gambar_baru = 'struktur_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($tmp, $upload_dir . $gambar_baru)) {
      $pesan = 'Gambar gagal diupload. Pastikan folder upload dapat ditulis.';
      $tipe = 'danger';
    } else {
      hapus_file_struktur($gambar_lama);
    }
  }

  if ($pesan === '') {
    if ($id) {
      $stmt = $pdo->prepare("UPDATE struktur_organisasi SET periode=?, sk_rektor=?, dekan=?, wakil_dekan_akademik=?, wakil_dekan_adum_keu=?, wakil_dekan_kemahasiswaan=?, gambar=? WHERE id=?");
      $stmt->execute([$periode, $sk_rektor, $dekan, $wakil_akademik, $wakil_adum, $wakil_kemahasiswaan, $gambar_baru, $id]);
    } else {
      $stmt = $pdo->prepare("INSERT INTO struktur_organisasi (periode,sk_rektor,dekan,wakil_dekan_akademik,wakil_dekan_adum_keu,wakil_dekan_kemahasiswaan,gambar) VALUES (?,?,?,?,?,?,?)");
      $stmt->execute([$periode, $sk_rektor, $dekan, $wakil_akademik, $wakil_adum, $wakil_kemahasiswaan, $gambar_baru]);
    }
    header('Location: struktur.php?pesan=simpan');
    exit;
  }
}

$data = $pdo->query("SELECT * FROM struktur_organisasi ORDER BY id DESC")->fetchAll();
?>

<?php require __DIR__ . '/../../includes/header.php'; ?>

<div class="page-head">
  <div>
    <span class="eyebrow">TENTANG FIKES</span>
    <h1>Struktur Organisasi</h1>
    <p>Kelola periode, SK Rektor, pimpinan, dan gambar struktur organisasi.</p>
  </div>
  <div>
    <a href="struktur.php" class="btn">↻ Reset</a>
    <button type="button" class="btn primary" onclick="bukaModalStruktur()">＋ Tambah Struktur</button>
  </div>
</div>

<?php if ($pesan): ?>
  <div class="alert <?= $tipe ?>"><?= e($pesan) ?></div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <div>
      <h2>Data Struktur Organisasi</h2>
      <p style="margin:4px 0 0;color:#8993a5;font-size:11px;">Data dapat disimpan untuk beberapa periode.</p>
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Gambar</th>
          <th>Periode</th>
          <th>SK Rektor</th>
          <th>Dekan</th>
          <th>Wakil Dekan Akademik</th>
          <th>Wakil Dekan Adum & Keu</th>
          <th>Wakil Dekan Kemahasiswaan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$data): ?>
          <tr>
            <td colspan="9" style="text-align:center;color:#8993a5;padding:35px;">Belum ada data struktur organisasi.</td>
          </tr>
        <?php else: ?>
          <?php $no = 1;
          foreach ($data as $row): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <?php if (!empty($row['gambar'])): ?>
                  <a href="<?= e($upload_url . $row['gambar']) ?>" target="_blank"><img
                      src="<?= e($upload_url . $row['gambar']) ?>" alt="Struktur"
                      style="width:90px;height:60px;object-fit:cover;border-radius:7px;border:1px solid #e8ecf3;"></a>
                <?php else: ?><span class="badge">Belum ada</span><?php endif; ?>
              </td>
              <td><span class="badge success"><?= e($row['periode']) ?></span></td>
              <td style="white-space:normal;min-width:190px;"><?= e($row['sk_rektor']) ?></td>
              <td style="white-space:normal;min-width:180px;"><?= e($row['dekan']) ?></td>
              <td style="white-space:normal;min-width:210px;"><?= e($row['wakil_dekan_akademik']) ?></td>
              <td style="white-space:normal;min-width:210px;"><?= e($row['wakil_dekan_adum_keu']) ?></td>
              <td style="white-space:normal;min-width:210px;"><?= e($row['wakil_dekan_kemahasiswaan']) ?></td>
              <td><a href="?edit=<?= (int)$row['id'] ?>" class="btn small">Edit</a> <a href="?hapus=<?= (int)$row['id'] ?>"
                  class="btn small danger-text"
                  onclick="return confirm('Hapus data struktur periode <?= e($row['periode']) ?>?')">Hapus</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal <?= $edit ? 'show' : '' ?>" id="modalStruktur">
  <div class="modal-box large">
    <div class="modal-head">
      <div><span class="eyebrow">FORM DATA</span>
        <h2><?= $edit ? 'Edit Struktur Organisasi' : 'Tambah Struktur Organisasi' ?></h2>
      </div><button type="button" onclick="tutupModalStruktur()">×</button>
    </div>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <div class="form-grid">
        <div><label>Periode</label><input type="text" name="periode" value="<?= e($edit['periode'] ?? '2024 - 2026') ?>"
            required></div>
        <div><label>SK Rektor</label><input type="text" name="sk_rektor"
            value="<?= e($edit['sk_rektor'] ?? 'Nomor 030/Univ.BHAMADA/KEP/V/2024') ?>" required></div>
        <div><label>Dekan</label><input type="text" name="dekan"
            value="<?= e($edit['dekan'] ?? 'Rosmalia, S.T.,M.Kes.') ?>" required></div>
        <div><label>Wakil Dekan Bidang Akademik</label><input type="text" name="wakil_dekan_akademik"
            value="<?= e($edit['wakil_dekan_akademik'] ?? 'Siswati, S.Si.T.,Bdn.,M.Kes.') ?>" required></div>
        <div><label>Wakil Dekan Bidang Adum & Keu</label><input type="text" name="wakil_dekan_adum_keu"
            value="<?= e($edit['wakil_dekan_adum_keu'] ?? 'Sri Hidayati, Ns.,M.Kep.,Sp.Kep.MB.') ?>" required></div>
        <div><label>Wakil Dekan Bidang Kemahasiswaan</label><input type="text" name="wakil_dekan_kemahasiswaan"
            value="<?= e($edit['wakil_dekan_kemahasiswaan'] ?? 'Deni Irawan, Ns.,M.Kep.') ?>" required></div>
        <div class="full"><label>Gambar Struktur Organisasi</label><input type="file" name="gambar"
            accept=".jpg,.jpeg,.png,.webp"><small
            style="display:block;color:#8993a5;margin-top:-8px;margin-bottom:12px;">Format JPG, JPEG, PNG, WEBP.
            Maksimal 3 MB.</small>
          <?php if (!empty($edit['gambar'])): ?><img src="<?= e($upload_url . $edit['gambar']) ?>" alt="Gambar struktur"
              style="max-width:100%;max-height:280px;border:1px solid #e8ecf3;border-radius:10px;"><?php endif; ?>
        </div>
      </div>
      <div class="modal-actions"><button type="button" class="btn" onclick="tutupModalStruktur()">Batal</button><button
          type="submit" class="btn primary">💾 Simpan Data</button></div>
    </form>
  </div>
</div>

<script>
  function bukaModalStruktur() {
    document.getElementById('modalStruktur').classList.add('show');
  }

  function tutupModalStruktur() {
    document.getElementById('modalStruktur').classList.remove('show');
    if (window.history.replaceState && <?= $edit ? 'true' : 'false' ?>) {
      window.history.replaceState({}, document.title, 'struktur.php');
    }
  }
  document.getElementById('modalStruktur').addEventListener('click', function(e) {
    if (e.target === this) tutupModalStruktur();
  });
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
