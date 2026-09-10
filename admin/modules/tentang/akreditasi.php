<?php
$page_title = 'Akreditasi';
require_once __DIR__ . '/../../config/auth.php';
wajib_login();

/*
|--------------------------------------------------------------------------
| CRUD Sertifikat Akreditasi
|--------------------------------------------------------------------------
| Pola dibuat sama seperti halaman struktur.php:
| - proses PHP dikerjakan sebelum header.php
| - menggunakan $pdo dari database.php
| - modal tambah/edit
| - redirect setelah simpan/hapus
| - upload file ke admin/uploads/upload-sertifikat/
*/
$project_url = '';
if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) {
  $project_url = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/admin/'));
}
$upload_dir = __DIR__ . '/../../uploads/upload-sertifikat/';
$upload_url = $project_url . '/admin/uploads/upload-sertifikat/';

if (!is_dir($upload_dir)) {
  mkdir($upload_dir, 0755, true);
}

$pesan = '';
$tipe = 'success';

$allowed = ['pdf', 'jpg', 'jpeg', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'];
$max_size = 10 * 1024 * 1024; // 10 MB

function hapus_file_sertifikat($nama_file)
{
  global $upload_dir;

  if ($nama_file && file_exists($upload_dir . basename($nama_file))) {
    unlink($upload_dir . basename($nama_file));
  }
}

/* ==========================================================
   HAPUS
   ========================================================== */
if (isset($_GET['hapus'])) {
  $id = (int) $_GET['hapus'];

  $stmt = $pdo->prepare("SELECT file_sertifikat FROM sertifikat_akreditasi WHERE id_sertifikat = ?");
  $stmt->execute([$id]);
  $data_hapus = $stmt->fetch();

  if ($data_hapus) {
    $stmt = $pdo->prepare("DELETE FROM sertifikat_akreditasi WHERE id_sertifikat = ?");
    $stmt->execute([$id]);

    hapus_file_sertifikat($data_hapus['file_sertifikat']);

    header('Location: akreditasi.php?pesan=hapus');
    exit;
  }

  header('Location: akreditasi.php?pesan=gagal');
  exit;
}

/* ==========================================================
   PESAN
   ========================================================== */
if (isset($_GET['pesan'])) {
  if ($_GET['pesan'] === 'simpan') {
    $pesan = 'Data sertifikat berhasil disimpan.';
  }

  if ($_GET['pesan'] === 'hapus') {
    $pesan = 'Data sertifikat berhasil dihapus.';
  }

  if ($_GET['pesan'] === 'gagal') {
    $pesan = 'Data sertifikat tidak ditemukan.';
    $tipe = 'danger';
  }

  if ($_GET['pesan'] === 'format') {
    $pesan = 'Format file harus PDF, JPG, JPEG, XLS, XLSX, DOC, DOCX, PPT, atau PPTX.';
    $tipe = 'danger';
  }

  if ($_GET['pesan'] === 'ukuran') {
    $pesan = 'Ukuran file maksimal 10 MB.';
    $tipe = 'danger';
  }

  if ($_GET['pesan'] === 'upload') {
    $pesan = 'File gagal diupload. Pastikan folder upload dapat ditulis.';
    $tipe = 'danger';
  }

  if ($_GET['pesan'] === 'wajib') {
    $pesan = 'File sertifikat wajib dipilih saat menambah data.';
    $tipe = 'danger';
  }

  if ($_GET['pesan'] === 'tanggal') {
    $pesan = 'Tanggal kadaluarsa tidak boleh lebih awal dari tanggal SK.';
    $tipe = 'danger';
  }
}

/* ==========================================================
   EDIT
   ========================================================== */
$edit = null;

if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];

  $stmt = $pdo->prepare("SELECT * FROM sertifikat_akreditasi WHERE id_sertifikat = ?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();

  if (!$edit) {
    $pesan = 'Data sertifikat tidak ditemukan.';
    $tipe = 'danger';
  }
}

/* ==========================================================
   SIMPAN / UPDATE
   ========================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id = (int) ($_POST['id_sertifikat'] ?? 0);

  $id_prodi = trim($_POST['id_prodi'] ?? '');
  $id_institusi = trim($_POST['id_institusi'] ?? '');
  $id_lembaga = trim($_POST['id_lembaga'] ?? '');
  $nomor_sk = trim($_POST['nomor_sk'] ?? '');
  $peringkat = trim($_POST['peringkat'] ?? '');
  $tanggal_sk = trim($_POST['tanggal_sk'] ?? '');
  $tanggal_kadaluarsa = trim($_POST['tanggal_kadaluarsa'] ?? '');
  $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

  if (
    $id_prodi === '' ||
    $id_institusi === '' ||
    $id_lembaga === '' ||
    $nomor_sk === '' ||
    $peringkat === '' ||
    $tanggal_sk === '' ||
    $tanggal_kadaluarsa === ''
  ) {
    header('Location: akreditasi.php?pesan=gagal');
    exit;
  }

  if ($tanggal_kadaluarsa < $tanggal_sk) {
    header('Location: akreditasi.php?pesan=tanggal');
    exit;
  }

  /* Ambil file lama jika edit */
  $file_lama = '';

  if ($id > 0) {
    $stmt = $pdo->prepare("SELECT file_sertifikat FROM sertifikat_akreditasi WHERE id_sertifikat = ?");
    $stmt->execute([$id]);
    $data_lama = $stmt->fetch();

    if ($data_lama) {
      $file_lama = $data_lama['file_sertifikat'];
    }
  }

  $file_baru = $file_lama;

  /* ======================================================
       UPLOAD FILE
       ====================================================== */
  if (!empty($_FILES['file_sertifikat']['name'])) {

    $nama_asli = $_FILES['file_sertifikat']['name'];
    $ukuran = (int) $_FILES['file_sertifikat']['size'];
    $tmp = $_FILES['file_sertifikat']['tmp_name'];

    $ext = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
      header('Location: akreditasi.php?pesan=format');
      exit;
    }

    if ($ukuran > $max_size) {
      header('Location: akreditasi.php?pesan=ukuran');
      exit;
    }

    $file_baru = 'sertifikat_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (!move_uploaded_file($tmp, $upload_dir . $file_baru)) {
      header('Location: akreditasi.php?pesan=upload');
      exit;
    }
  }

  /* ======================================================
       INSERT
       ====================================================== */
  if ($id === 0) {

    if ($file_baru === '') {
      header('Location: akreditasi.php?pesan=wajib');
      exit;
    }

    $stmt = $pdo->prepare("
            INSERT INTO sertifikat_akreditasi
            (
                id_prodi,
                id_institusi,
                id_lembaga,
                nomor_sk,
                peringkat,
                tanggal_sk,
                tanggal_kadaluarsa,
                file_sertifikat,
                status_aktif
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

    $stmt->execute([
      $id_prodi,
      $id_institusi,
      $id_lembaga,
      $nomor_sk,
      $peringkat,
      $tanggal_sk,
      $tanggal_kadaluarsa,
      $file_baru,
      $status_aktif
    ]);

    header('Location: akreditasi.php?pesan=simpan');
    exit;
  }

  /* ======================================================
       UPDATE
       ====================================================== */
  $stmt = $pdo->prepare("
        UPDATE sertifikat_akreditasi SET
            id_prodi = ?,
            id_institusi = ?,
            id_lembaga = ?,
            nomor_sk = ?,
            peringkat = ?,
            tanggal_sk = ?,
            tanggal_kadaluarsa = ?,
            file_sertifikat = ?,
            status_aktif = ?
        WHERE id_sertifikat = ?
    ");

  $stmt->execute([
    $id_prodi,
    $id_institusi,
    $id_lembaga,
    $nomor_sk,
    $peringkat,
    $tanggal_sk,
    $tanggal_kadaluarsa,
    $file_baru,
    $status_aktif,
    $id
  ]);

  /* Hapus file lama jika diganti */
  if ($file_baru !== $file_lama && $file_lama !== '') {
    hapus_file_sertifikat($file_lama);
  }

  header('Location: akreditasi.php?pesan=simpan');
  exit;
}

/* ==========================================================
   SEARCH + PAGINATION
   ========================================================== */

$q = trim($_GET['q'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$per_halaman = 10;

$where = '';
$params = [];

if ($q !== '') {
  $where = "WHERE
        id_prodi LIKE ?
        OR id_institusi LIKE ?
        OR id_lembaga LIKE ?
        OR nomor_sk LIKE ?
        OR peringkat LIKE ?";

  $kata = '%' . $q . '%';

  $params = [$kata, $kata, $kata, $kata, $kata];
}

/* Hitung total data */
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM sertifikat_akreditasi
    $where
");

$stmt->execute($params);

$total_data = (int) $stmt->fetchColumn();

$total_halaman = max(1, (int) ceil($total_data / $per_halaman));

if ($halaman > $total_halaman) {
  $halaman = $total_halaman;
}

$offset = ($halaman - 1) * $per_halaman;

/* Ambil data sesuai halaman */
$sql = "
    SELECT *
    FROM sertifikat_akreditasi
    $where
    ORDER BY id_sertifikat DESC
    LIMIT $per_halaman OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

require __DIR__ . '/../../includes/header.php';
?>

<div class="page-head">
  <div>
    <span class="eyebrow">TENTANG FIKES</span>
    <h1>Sertifikat Akreditasi</h1>
    <p>Kelola data dan dokumen sertifikat akreditasi FIKES.</p>
  </div>

  <div>
    <a href="akreditasi.php" class="btn">↻ Reset</a>
    <button type="button" class="btn primary" onclick="bukaModalAkreditasi()">
      ＋ Tambah Sertifikat
    </button>
  </div>
</div>

<?php if ($pesan): ?>
  <div class="alert <?= $tipe ?>">
    <?= e($pesan) ?>
  </div>
<?php endif; ?>

<div class="panel">

  <div class="panel-head">
    <div>
      <h2>Data Sertifikat Akreditasi</h2>
      <p style="margin:4px 0 0;color:#8993a5;font-size:11px;">
        Kelola sertifikat institusi maupun program studi.
      </p>
    </div>
  </div>

  <!-- SEARCH -->
  <div style="padding:0 22px 18px;">
    <form method="get" style="display:flex;gap:10px;max-width:650px;">
      <input type="text" name="q" value="<?= e($q) ?>"
        placeholder="Cari prodi, institusi, lembaga, nomor SK, atau peringkat..." style="flex:1;">

      <button type="submit" class="btn primary">
        🔍 Cari
      </button>

      <?php if ($q !== ''): ?>
        <a href="akreditasi.php" class="btn">
          Reset
        </a>
      <?php endif; ?>
    </form>
  </div>

  <div style="padding:0 22px 14px;color:#8993a5;font-size:12px;">
    Menampilkan
    <strong style="color:#15243f;">
      <?= $total_data ? $offset + 1 : 0 ?>
      -
      <?= min($offset + $per_halaman, $total_data) ?>
    </strong>
    dari
    <strong style="color:#15243f;"><?= $total_data ?></strong>
    data
  </div>

  <div class="table-wrap">
    <table id="tabelAkreditasi">

      <thead>
        <tr>
          <th>No</th>
          <th>ID Prodi</th>
          <th>ID Institusi</th>
          <th>ID Lembaga</th>
          <th>Nomor SK</th>
          <th>Peringkat</th>
          <th>Tanggal SK</th>
          <th>Kadaluarsa</th>
          <th>File</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody>

        <?php if (!$data): ?>

          <tr>
            <td colspan="11" style="text-align:center;color:#8993a5;padding:35px;">
              <?= $q !== ''
                ? 'Data sertifikat tidak ditemukan.'
                : 'Belum ada sertifikat akreditasi.' ?>
            </td>
          </tr>

        <?php else: ?>

          <?php $no = $offset + 1; ?>

          <?php foreach ($data as $row): ?>

            <tr>

              <td><?= $no++ ?></td>

              <td>
                <strong><?= e($row['id_prodi']) ?></strong>
              </td>

              <td style="white-space:normal;min-width:170px;">
                <?= e($row['id_institusi']) ?>
              </td>

              <td>
                <?= e($row['id_lembaga']) ?>
              </td>

              <td style="white-space:normal;min-width:190px;">
                <?= e($row['nomor_sk']) ?>
              </td>

              <td>
                <span class="badge success">
                  <?= e($row['peringkat']) ?>
                </span>
              </td>

              <td>
                <?= date('d-m-Y', strtotime($row['tanggal_sk'])) ?>
              </td>

              <td>
                <?= date('d-m-Y', strtotime($row['tanggal_kadaluarsa'])) ?>
              </td>

              <td>

                <?php if (!empty($row['file_sertifikat'])): ?>

                  <a href="<?= e($upload_url . $row['file_sertifikat']) ?>" target="_blank" class="btn small">
                    Lihat File
                  </a>

                <?php else: ?>

                  <span class="badge">Belum ada</span>

                <?php endif; ?>

              </td>

              <td>

                <?php if ($row['status_aktif']): ?>

                  <span class="badge success">Aktif</span>

                <?php else: ?>

                  <span class="badge">Tidak Aktif</span>

                <?php endif; ?>

              </td>

              <td style="white-space:nowrap;">

                <a href="?edit=<?= (int)$row['id_sertifikat'] ?>&q=<?= urlencode($q) ?>&halaman=<?= $halaman ?>"
                  class="btn small">
                  Edit
                </a>

                <a href="?hapus=<?= (int)$row['id_sertifikat'] ?>" class="btn small danger-text"
                  onclick="return confirm('Hapus sertifikat ini? File juga akan dihapus dari server.');">
                  Hapus
                </a>

              </td>

            </tr>

          <?php endforeach; ?>

        <?php endif; ?>

      </tbody>

    </table>
  </div>

  <!-- PAGINATION DI BAWAH TABEL -->
  <?php if ($total_halaman > 1): ?>

    <div class="pagination-area">

      <div class="pagination-info">
        Menampilkan <?= $total_data ? $offset + 1 : 0 ?>
        - <?= min($offset + $per_halaman, $total_data) ?>
        dari <?= $total_data ?> data
      </div>

      <div class="pagination">

        <?php
        $url = function ($hal) use ($q) {
          return 'akreditasi.php?q=' . urlencode($q) . '&halaman=' . $hal;
        };
        ?>

        <?php if ($halaman > 1): ?>
          <a href="<?= e($url($halaman - 1)) ?>" class="page-link">
            ‹
          </a>
        <?php endif; ?>

        <?php
        $mulai = max(1, $halaman - 2);
        $akhir = min($total_halaman, $halaman + 2);

        for ($i = $mulai; $i <= $akhir; $i++):
        ?>

          <a href="<?= e($url($i)) ?>" class="page-link <?= $i === $halaman ? 'active' : '' ?>">
            <?= $i ?>
          </a>

        <?php endfor; ?>

        <?php if ($halaman < $total_halaman): ?>
          <a href="<?= e($url($halaman + 1)) ?>" class="page-link">
            ›
          </a>
        <?php endif; ?>

      </div>

    </div>

  <?php endif; ?>

</div>


<!-- =========================================================
     MODAL TAMBAH / EDIT
========================================================= -->

<div class="modal <?= $edit ? 'show' : '' ?>" id="modalAkreditasi">

  <div class="modal-box large">

    <div class="modal-head">

      <div>

        <span class="eyebrow">FORM DATA</span>

        <h2>
          <?= $edit ? 'Edit Sertifikat Akreditasi' : 'Tambah Sertifikat Akreditasi' ?>
        </h2>

      </div>

      <button type="button" onclick="tutupModalAkreditasi()">
        ×
      </button>

    </div>


    <form method="post" enctype="multipart/form-data">

      <input type="hidden" name="id_sertifikat" value="<?= (int)($edit['id_sertifikat'] ?? 0) ?>">


      <div class="form-grid">

        <div>

          <label>ID Prodi</label>

          <input type="text" name="id_prodi" value="<?= e($edit['id_prodi'] ?? '') ?>" placeholder="Contoh: Keperawatan"
            required>

        </div>


        <div>

          <label>ID Institusi</label>

          <input type="text" name="id_institusi" value="<?= e($edit['id_institusi'] ?? '') ?>"
            placeholder="Contoh: Universitas Bhamada" required>

        </div>


        <div>

          <label>ID Lembaga</label>

          <input type="text" name="id_lembaga" value="<?= e($edit['id_lembaga'] ?? '') ?>"
            placeholder="Contoh: LAM-PTKes" required>

        </div>


        <div>

          <label>Peringkat</label>

          <input type="text" name="peringkat" value="<?= e($edit['peringkat'] ?? '') ?>"
            placeholder="Contoh: Baik Sekali" required>

        </div>


        <div class="full">

          <label>Nomor SK</label>

          <input type="text" name="nomor_sk" value="<?= e($edit['nomor_sk'] ?? '') ?>" placeholder="Nomor SK Akreditasi"
            required>

        </div>


        <div>

          <label>Tanggal SK</label>

          <input type="date" name="tanggal_sk" value="<?= e($edit['tanggal_sk'] ?? '') ?>" required>

        </div>


        <div>

          <label>Tanggal Kadaluarsa</label>

          <input type="date" name="tanggal_kadaluarsa" value="<?= e($edit['tanggal_kadaluarsa'] ?? '') ?>" required>

        </div>


        <div class="full">

          <label>File Sertifikat</label>

          <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.xls,.xlsx,.doc,.docx,.ppt,.pptx"
            <?= empty($edit) ? 'required' : '' ?>>

          <small style="display:block;color:#8993a5;margin-top:5px;">
            PDF, JPG, JPEG, XLS, XLSX, DOC, DOCX, PPT, PPTX.
            Maksimal 10 MB.
          </small>


          <?php if (!empty($edit['file_sertifikat'])): ?>

            <div style="margin-top:12px;">

              File saat ini:

              <a href="<?= e($upload_url . $edit['file_sertifikat']) ?>" target="_blank">
                <?= e($edit['file_sertifikat']) ?>
              </a>

            </div>

          <?php endif; ?>

        </div>


        <div class="full">

          <label style="display:flex;align-items:center;gap:8px;">

            <input type="checkbox" name="status_aktif" value="1"
              <?= !isset($edit['status_aktif']) || $edit['status_aktif'] ? 'checked' : '' ?> style="width:auto;">

            Sertifikat Aktif

          </label>

        </div>

      </div>


      <div class="modal-actions">

        <button type="button" class="btn" onclick="tutupModalAkreditasi()">
          Batal
        </button>

        <button type="submit" class="btn primary">
          💾 Simpan Data
        </button>

      </div>

    </form>

  </div>

</div>



<style>
  .pagination-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 18px 22px 22px;
    border-top: 1px solid #eef1f6;
    flex-wrap: wrap;
  }

  .pagination-info {
    color: #8993a5;
    font-size: 12px;
  }

  .pagination {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .page-link {
    min-width: 34px;
    height: 34px;
    padding: 0 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e4e9f1;
    border-radius: 8px;
    background: #fff;
    color: #52627a;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    box-sizing: border-box;
  }

  .page-link:hover {
    border-color: #1478ff;
    color: #1478ff;
  }

  .page-link.active {
    background: #1478ff;
    border-color: #1478ff;
    color: #fff;
  }
</style>

<script>
  function bukaModalAkreditasi() {
    document
      .getElementById('modalAkreditasi')
      .classList.add('show');
  }


  function tutupModalAkreditasi() {

    document
      .getElementById('modalAkreditasi')
      .classList.remove('show');

    <?php if ($edit): ?>

      if (window.history.replaceState) {

        window.history.replaceState({},
          document.title,
          'akreditasi.php'
        );

      }

    <?php endif; ?>

  }


  document
    .getElementById('modalAkreditasi')
    .addEventListener('click', function(e) {

      if (e.target === this) {
        tutupModalAkreditasi();
      }

    });
</script>


<?php require __DIR__ . '/../../includes/footer.php'; ?>
