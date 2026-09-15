<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';
function tanggal_id($v)
{
  if (!$v) return '-';
  $b = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  $t = strtotime($v);
  return $t ? date('d', $t) . ' ' . $b[(int)date('m', $t) - 1] . ' ' . date('Y', $t) : $v;
}
$uploadDir = __DIR__ . '/../../uploads/akademik/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);
function upload_doc($field, $old = '')
{
  global $uploadDir;
  if (empty($_FILES[$field]['name'])) return $old;
  $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
  $allow = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
  if (!in_array($ext, $allow, true)) throw new Exception('Format dokumen tidak didukung.');
  if ($_FILES[$field]['size'] > 10 * 1024 * 1024) throw new Exception('Ukuran dokumen maksimal 10 MB.');
  $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
  if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $name)) throw new Exception('Upload dokumen gagal.');
  return $name;
}
function redirect_ok($msg)
{
  header('Location: index.php?ok=' . rawurlencode($msg));
  exit;
}
try {
  $action = $_POST['action'] ?? '';
  if ($action === 'kurikulum') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [trim($_POST['kode_mk'] ?? ''), trim($_POST['nama_mk'] ?? ''), trim($_POST['semester'] ?? ''), (float)($_POST['sks'] ?? 0), trim($_POST['jenis'] ?? 'Wajib'), (int)($_POST['prodi_id'] ?? 0)];
    if ($data[1] === '' || $data[5] < 1) throw new Exception('Nama mata kuliah dan Program Studi wajib diisi.');
    if ($id) {
      $pdo->prepare('UPDATE prodi_kurikulum SET kode_mk=?,nama_mk=?,semester=?,sks=?,jenis=? WHERE id=? AND prodi_id=?')->execute([$data[0], $data[1], $data[2], $data[3], $data[4], $id, $data[5]]);
    } else {
      $next = (int)$pdo->query('SELECT COALESCE(MAX(nomor_urut),0)+1 FROM prodi_kurikulum')->fetchColumn();
      $pdo->prepare('INSERT INTO prodi_kurikulum(prodi_id,kode_mk,nama_mk,semester,sks,jenis,nomor_urut) VALUES(?,?,?,?,?,?,?)')->execute([$data[5], $data[0], $data[1], $data[2], $data[3], $data[4], $next]);
    }
    redirect_ok('Kurikulum berhasil disimpan.');
  }
  if ($action === 'kurikulum_delete') {
    $id = (int)$_POST['id'];
    $pdo->prepare('DELETE FROM prodi_kurikulum WHERE id=?')->execute([$id]);
    redirect_ok('Mata kuliah berhasil dihapus.');
  }
  if ($action === 'silabus') {
    $id = (int)($_POST['id'] ?? 0);
    $old = $_POST['old_file'] ?? '';
    $file = upload_doc('file_dokumen', $old);
    $d = [(int)$_POST['kurikulum_id'], trim($_POST['judul'] ?? ''), trim($_POST['deskripsi'] ?? ''), $file];
    if ($d[0] < 1 || $d[1] === '') throw new Exception('Kurikulum dan judul silabus wajib diisi.');
    if ($id) $pdo->prepare('UPDATE akademik_silabus SET kurikulum_id=?,judul=?,deskripsi=?,file_dokumen=? WHERE id=?')->execute([$d[0], $d[1], $d[2], $d[3], $id]);
    else $pdo->prepare("INSERT INTO akademik_silabus(kurikulum_id,judul,deskripsi,file_dokumen) VALUES(?,?,?,?)")->execute($d);
    redirect_ok('Silabus berhasil disimpan.');
  }
  if ($action === 'silabus_delete') {
    $pdo->prepare('DELETE FROM akademik_silabus WHERE id=?')->execute([(int)$_POST['id']]);
    redirect_ok('Silabus berhasil dihapus.');
  }
  if (in_array($action, ['kalender', 'registrasi', 'penilaian'], true)) {
    if ($action === 'kalender') {
      $id = (int)($_POST['id'] ?? 0);
      $d = [trim($_POST['tahun_ajaran'] ?? ''), trim($_POST['kategori'] ?? ''), trim($_POST['judul'] ?? ''), $_POST['tanggal_mulai'] ?? '', $_POST['tanggal_selesai'] ?: null, trim($_POST['keterangan'] ?? ''), (int)($_POST['nomor_urut'] ?? 1)];
      if ($id) $pdo->prepare('UPDATE akademik_kalender SET tahun_ajaran=?,kategori=?,judul=?,tanggal_mulai=?,tanggal_selesai=?,keterangan=?,nomor_urut=? WHERE id=?')->execute([...$d, $id]);
      else $pdo->prepare('INSERT INTO akademik_kalender(tahun_ajaran,kategori,judul,tanggal_mulai,tanggal_selesai,keterangan,nomor_urut) VALUES(?,?,?,?,?,?,?)')->execute($d);
    } elseif ($action === 'registrasi') {
      $id = (int)($_POST['id'] ?? 0);
      $d = [trim($_POST['tahun_ajaran'] ?? ''), trim($_POST['jenis'] ?? 'Registrasi'), trim($_POST['judul'] ?? ''), $_POST['tanggal_mulai'] ?? '', $_POST['tanggal_selesai'] ?: null, trim($_POST['keterangan'] ?? '')];
      if ($id) $pdo->prepare('UPDATE akademik_registrasi SET tahun_ajaran=?,jenis=?,judul=?,tanggal_mulai=?,tanggal_selesai=?,keterangan=? WHERE id=?')->execute([...$d, $id]);
      else $pdo->prepare('INSERT INTO akademik_registrasi(tahun_ajaran,jenis,judul,tanggal_mulai,tanggal_selesai,keterangan) VALUES(?,?,?,?,?,?)')->execute($d);
    } else {
      $id = (int)($_POST['id'] ?? 0);
      $d = [trim($_POST['komponen'] ?? ''), (float)($_POST['bobot'] ?? 0), trim($_POST['keterangan'] ?? ''), (int)($_POST['nomor_urut'] ?? 1)];
      if ($id) $pdo->prepare('UPDATE akademik_penilaian SET komponen=?,bobot=?,keterangan=?,nomor_urut=? WHERE id=?')->execute([...$d, $id]);
      else $pdo->prepare('INSERT INTO akademik_penilaian(komponen,bobot,keterangan,nomor_urut) VALUES(?,?,?,?)')->execute($d);
    }
    redirect_ok('Data akademik berhasil disimpan.');
  }
  if ($action === 'jadwal') {
    $id = (int)($_POST['id'] ?? 0);
    $d = [(int)($_POST['prodi_id'] ?? 0) ?: null, trim($_POST['jenis'] ?? 'Kuliah'), trim($_POST['kode_mk'] ?? ''), trim($_POST['nama_kegiatan'] ?? ''), $_POST['tanggal'] ?? '', trim($_POST['hari'] ?? ''), $_POST['jam_mulai'] ?? '', $_POST['jam_selesai'] ?? '', trim($_POST['ruang'] ?? ''), trim($_POST['keterangan'] ?? '')];
    if ($id) $pdo->prepare('UPDATE akademik_jadwal SET prodi_id=?,jenis=?,kode_mk=?,nama_kegiatan=?,tanggal=?,hari=?,jam_mulai=?,jam_selesai=?,ruang=?,keterangan=? WHERE id=?')->execute([...$d, $id]);
    else $pdo->prepare('INSERT INTO akademik_jadwal(prodi_id,jenis,kode_mk,nama_kegiatan,tanggal,hari,jam_mulai,jam_selesai,ruang,keterangan) VALUES(?,?,?,?,?,?,?,?,?,?)')->execute($d);
    redirect_ok('Jadwal berhasil disimpan.');
  }
  if ($action === 'dokumen') {
    $id = (int)($_POST['id'] ?? 0);
    $old = $_POST['old_file'] ?? '';
    $file = upload_doc('file_dokumen', $old);
    $d = [trim($_POST['kategori'] ?? 'panduan'), trim($_POST['judul'] ?? ''), trim($_POST['deskripsi'] ?? ''), trim($_POST['isi'] ?? ''), $file, trim($_POST['link_url'] ?? ''), (int)($_POST['nomor_urut'] ?? 1)];
    if ($id) $pdo->prepare('UPDATE akademik_dokumen SET kategori=?,judul=?,deskripsi=?,isi=?,file_dokumen=?,link_url=?,nomor_urut=? WHERE id=?')->execute([...$d, $id]);
    else $pdo->prepare('INSERT INTO akademik_dokumen(kategori,judul,deskripsi,isi,file_dokumen,link_url,nomor_urut) VALUES(?,?,?,?,?,?,?)')->execute($d);
    redirect_ok('Dokumen berhasil disimpan.');
  }
  if ($action === 'delete') {
    $map = ['kalender' => 'akademik_kalender', 'jadwal' => 'akademik_jadwal', 'registrasi' => 'akademik_registrasi', 'dokumen' => 'akademik_dokumen', 'penilaian' => 'akademik_penilaian'];
    $t = $map[$_POST['jenis'] ?? ''] ?? '';
    if ($t) {
      $pdo->prepare("DELETE FROM $t WHERE id=?")->execute([(int)$_POST['id']]);
      redirect_ok('Data berhasil dihapus.');
    }
  }
} catch (Throwable $e) {
  header('Location: index.php?err=' . rawurlencode($e->getMessage()));
  exit;
}
$prodi = $pdo->query("SELECT id,nama,jenjang FROM program_studi WHERE status='aktif' ORDER BY nama")->fetchAll();
$kur = $pdo->query("SELECT k.*,p.nama prodi_nama FROM prodi_kurikulum k LEFT JOIN program_studi p ON p.id=k.prodi_id ORDER BY p.nama,k.semester,k.nomor_urut,k.id")->fetchAll();
$sil = $pdo->query("SELECT s.*,k.nama_mk,p.nama prodi_nama FROM akademik_silabus s LEFT JOIN prodi_kurikulum k ON k.id=s.kurikulum_id LEFT JOIN program_studi p ON p.id=k.prodi_id ORDER BY p.nama,k.semester,s.id DESC")->fetchAll();
$kal = $pdo->query("SELECT * FROM akademik_kalender ORDER BY tanggal_mulai,id")->fetchAll();
$jad = $pdo->query("SELECT j.*,p.nama prodi_nama FROM akademik_jadwal j LEFT JOIN program_studi p ON p.id=j.prodi_id ORDER BY j.tanggal,j.jam_mulai,j.id")->fetchAll();
$reg = $pdo->query("SELECT * FROM akademik_registrasi ORDER BY tanggal_mulai,id")->fetchAll();
$doc = $pdo->query("SELECT * FROM akademik_dokumen ORDER BY kategori,nomor_urut,id DESC")->fetchAll();
$nilai = $pdo->query("SELECT * FROM akademik_penilaian ORDER BY nomor_urut,id")->fetchAll();
$editType = $_GET['edit_type'] ?? '';
$editId = (int)($_GET['edit_id'] ?? 0);
$editRow = [];
$editMap = ['kurikulum' => 'prodi_kurikulum', 'silabus' => 'akademik_silabus', 'kalender' => 'akademik_kalender', 'jadwal' => 'akademik_jadwal', 'registrasi' => 'akademik_registrasi', 'dokumen' => 'akademik_dokumen', 'penilaian' => 'akademik_penilaian'];
if ($editId && isset($editMap[$editType])) {
  $st = $pdo->prepare("SELECT * FROM {$editMap[$editType]} WHERE id=? LIMIT 1");
  $st->execute([$editId]);
  $editRow = $st->fetch() ?: [];
}
$page_title = 'Akademik';
include __DIR__ . '/../../includes/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .ak-wrap {
    padding: 24px;
    max-width: none;
    width: 100%;
    box-sizing: border-box
  }

  .ak-wrap .page-head {
    margin-bottom: 18px
  }

  .ak-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    position: sticky;
    top: 0;
    z-index: 20;
    padding: 8px 0;
    background: rgba(245, 248, 247, .96);
    backdrop-filter: blur(8px)
  }

  .ak-tabs a {
    padding: 10px 14px;
    border: 1px solid #dfe9e5;
    border-radius: 10px;
    background: #fff;
    color: #36564d;
    font-weight: 700;
    font-size: 12px;
    text-decoration: none;
    transition: .2s
  }

  .ak-tabs a:hover {
    background: #e7f7f1;
    color: #087f5b;
    transform: translateY(-1px)
  }

  .ak-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 20px;
    width: 100%
  }

  .ak-panel,
  .ak-panel.full {
    grid-column: 1/-1;
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
    background: #fff;
    border: 1px solid #e2ebe8;
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(18, 55, 42, .055);
    overflow: hidden
  }

  .ak-panel.editing {
    border-color: #0a8f68;
    box-shadow: 0 10px 30px rgba(10, 143, 104, .12)
  }

  .ak-panel h2 {
    font-size: 18px;
    margin: 0 0 16px;
    color: #12372a;
    display: flex;
    align-items: center;
    gap: 7px
  }

  .ak-panel form {
    min-width: 0
  }

  .ak-form {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px 14px
  }

  .ak-form .full {
    grid-column: 1/-1
  }

  .ak-form label {
    display: block;
    font-size: 11px;
    font-weight: 800;
    margin-bottom: 6px;
    color: #47635a
  }

  .ak-form input,
  .ak-form select,
  .ak-form textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 11px;
    border: 1px solid #d7e4df;
    border-radius: 10px;
    background: #fff;
    font: inherit;
    font-size: 12px;
    color: #183d32;
    outline: none;
    transition: .2s
  }

  .ak-form input:focus,
  .ak-form select:focus,
  .ak-form textarea:focus {
    border-color: #56b894;
    box-shadow: 0 0 0 3px rgba(86, 184, 148, .12)
  }

  .ak-form textarea {
    min-height: 90px;
    resize: vertical
  }

  .ak-actions {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
    margin-top: 12px
  }

  .ak-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 0;
    border-radius: 8px;
    padding: 9px 12px;
    background: #087f5b;
    color: #fff;
    font-weight: 800;
    font-size: 11px;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap
  }

  .ak-btn:hover {
    filter: brightness(.96)
  }

  .ak-btn.danger {
    background: #c73c3c
  }

  .ak-btn.light {
    background: #edf7f3;
    color: #087f5b
  }

  .ak-note {
    padding: 11px;
    background: #fff8e7;
    border: 1px solid #f1dfac;
    border-radius: 10px;
    font-size: 11px;
    margin-bottom: 15px
  }

  .alert {
    padding: 11px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 12px
  }

  .alert.ok {
    background: #e7f7f1;
    color: #087f5b
  }

  .alert.err {
    background: #fdecec;
    color: #a22
  }

  .mini {
    font-size: 10px;
    color: #6c7c77
  }

  .ak-panel hr {
    border: 0;
    border-top: 1px solid #e7eeeb;
    margin: 18px 0 10px
  }

  .ak-table-wrap {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border: 1px solid #e5ece9;
    border-radius: 12px;
    -webkit-overflow-scrolling: touch
  }

  .ak-table {
    width: 100%;
    min-width: 720px;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 12px;
    background: #fff
  }

  .ak-table th,
  .ak-table td {
    padding: 11px 10px;
    border-bottom: 1px solid rgba(0, 0, 0, .055);
    text-align: left;
    vertical-align: middle;
    white-space: nowrap
  }

  .ak-table td:nth-child(3) {
    white-space: normal;
    min-width: 180px
  }

  .ak-table th {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 800
  }

  .ak-table tr:last-child td {
    border-bottom: 0
  }

  .ak-table tbody tr:hover td {
    background: rgba(255, 255, 255, .55)
  }

  .ak-table td form {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0
  }

  .ak-table td .ak-btn {
    padding: 7px 10px;
    font-size: 10px
  }

  /* Setiap bagian akademik memiliki warna tabel yang berbeda. */
  #kurikulum {
    border-top: 4px solid #0b8f67
  }

  #kurikulum .ak-table th {
    background: #e7f7f1;
    color: #087f5b
  }

  #kurikulum .ak-table td:first-child {
    font-weight: 700;
    color: #087f5b
  }

  #silabus {
    border-top: 4px solid #4778c7
  }

  #silabus .ak-table th {
    background: #eaf1ff;
    color: #315fa7
  }

  #silabus .ak-table a {
    color: #315fa7;
    font-weight: 700
  }

  #kalender {
    border-top: 4px solid #d99518
  }

  #kalender .ak-table th {
    background: #fff4d9;
    color: #9a6500
  }

  #kalender .ak-table td:first-child {
    font-weight: 700;
    color: #9a6500
  }

  #jadwal {
    border-top: 4px solid #7b61b8
  }

  #jadwal .ak-table th {
    background: #f0ebfb;
    color: #674c9e
  }

  #jadwal .ak-table td:first-child {
    font-weight: 700;
    color: #674c9e
  }

  #registrasi {
    border-top: 4px solid #df6b45
  }

  #registrasi .ak-table th {
    background: #fff0ea;
    color: #b95332
  }

  #registrasi .ak-table td:first-child {
    font-weight: 700;
    color: #b95332
  }

  #dokumen {
    border-top: 4px solid #278ca5
  }

  #dokumen .ak-table th {
    background: #e7f7fa;
    color: #16758c
  }

  #dokumen .ak-table td:first-child {
    font-weight: 700;
    color: #16758c
  }

  #penilaian {
    border-top: 4px solid #b05286
  }

  #penilaian .ak-table th {
    background: #f9eaf2;
    color: #943c6a
  }

  #penilaian .ak-table td:nth-child(2) {
    font-weight: 800;
    color: #943c6a
  }

  @media(max-width:1100px) {
    .ak-wrap {
      padding: 18px
    }

    .ak-form {
      grid-template-columns: repeat(2, minmax(0, 1fr))
    }
  }

  @media(max-width:700px) {
    .ak-wrap {
      padding: 12px
    }

    .ak-tabs {
      position: static
    }

    .ak-panel,
    .ak-panel.full {
      padding: 14px;
      border-radius: 14px
    }

    .ak-panel h2 {
      font-size: 16px
    }

    .ak-form {
      grid-template-columns: 1fr;
      gap: 10px
    }

    .ak-form .full {
      grid-column: auto
    }

    .ak-table {
      min-width: 680px
    }

    .ak-table th,
    .ak-table td {
      padding: 9px 8px
    }
  }

  .swal2-container {
    z-index: 20000 !important
  }

  .swal2-popup {
    border-radius: 18px !important
  }

  .swal2-title {
    font-size: 20px !important
  }

  .swal2-html-container {
    font-size: 13px !important
  }

  .swal2-confirm {
    border-radius: 10px !important;
    font-weight: 700 !important
  }

  .swal2-cancel {
    border-radius: 10px !important;
    font-weight: 700 !important
  }
</style>
<div class="ak-wrap">
  <div class="page-head">
    <div>
      <h1>Akademik</h1>
      <p>Kelola kurikulum, silabus, kalender, jadwal, registrasi, dokumen mahasiswa dan sistem penilaian.</p>
    </div>
  </div>
  <?php if (isset($_GET['ok'])): ?><div class="alert ok"><?= e($_GET['ok']) ?></div>
    <?php endif; ?><?php if (isset($_GET['err'])): ?><div class="alert err"><?= e($_GET['err']) ?></div><?php endif; ?>
  <div class="ak-tabs"><a href="#kurikulum">Kurikulum</a><a href="#silabus">Silabus</a><a
      href="#kalender">Kalender</a><a href="#jadwal">Jadwal</a><a href="#registrasi">Registrasi</a><a
      href="#dokumen">Dokumen Mahasiswa</a><a href="#penilaian">Penilaian</a></div>
  <div class="ak-grid">
    <section class="ak-panel full<?= ($editType === 'kurikulum' && $editId) ? ' editing' : '' ?>" id="kurikulum">
      <h2>📚 Kurikulum &amp; Silabus — Mata Kuliah</h2>
      <form class="ak-form" method="post"><input type="hidden" name="action" value="kurikulum"><input type="hidden"
          name="id" value="<?= ($editType === 'kurikulum' ? ($editRow['id'] ?? 0) : 0) ?>">
        <div><label>Program Studi *</label><select name="prodi_id" required>
            <option value="">Pilih Program Studi</option><?php foreach ($prodi as $p): ?><option value="<?= $p['id'] ?>"
                <?= ($editType === 'kurikulum' && ($editRow['prodi_id'] ?? 0) == $p['id']) ? 'selected' : '' ?>><?= e($p['nama']) ?> —
                <?= e($p['jenjang']) ?></option><?php endforeach; ?>
          </select></div>
        <div><label>Kode Mata Kuliah</label><input name="kode_mk"
            value="<?= e($editType === 'kurikulum' ? ($editRow['kode_mk'] ?? '') : '') ?>"></div>
        <div><label>Nama Mata Kuliah *</label><input name="nama_mk"
            value="<?= e($editType === 'kurikulum' ? ($editRow['nama_mk'] ?? '') : '') ?>" required></div>
        <div><label>Semester</label><input name="semester"
            value="<?= e($editType === 'kurikulum' ? ($editRow['semester'] ?? '') : '') ?>" placeholder="1 / 2 / 3..."></div>
        <div><label>SKS</label><input type="number" step="0.5" name="sks"
            value="<?= e($editType === 'kurikulum' ? ($editRow['sks'] ?? 2) : 2) ?>"></div>
        <div><label>Jenis</label><select name="jenis">
            <option <?= ($editType === 'kurikulum' && ($editRow['jenis'] ?? '') === 'Wajib') ? 'selected' : '' ?>>Wajib</option>
            <option <?= ($editType === 'kurikulum' && ($editRow['jenis'] ?? '') === 'Pilihan') ? 'selected' : '' ?>>Pilihan</option>
          </select></div>
        <div class="full"><button
            class="ak-btn"><?= ($editType === 'kurikulum' && $editId) ? 'Simpan Perubahan' : '+ Tambah Mata Kuliah' ?></button>
        </div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Prodi</th>
            <th>Kode</th>
            <th>Mata Kuliah</th>
            <th>Sem</th>
            <th>SKS</th>
            <th>Jenis</th>
            <th>Aksi</th>
          </tr><?php foreach ($kur as $x): ?><tr>
              <td><?= e($x['prodi_nama']) ?></td>
              <td><?= e($x['kode_mk']) ?></td>
              <td><strong><?= e($x['nama_mk']) ?></strong></td>
              <td><?= e($x['semester']) ?></td>
              <td><?= e($x['sks']) ?></td>
              <td><?= e($x['jenis'] ?: 'Wajib') ?></td>
              <td>
                <form method="post" class="js-delete-form" data-confirm="Hapus mata kuliah ini?"><input type="hidden"
                    name="action" value="kurikulum_delete"><input type="hidden" name="id" value="<?= $x['id'] ?>"><a
                    class="ak-btn light" href="?edit_type=kurikulum&edit_id=<?= $x['id'] ?>#kurikulum">Edit</a></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
    <section class="ak-panel<?= ($editType === 'silabus' && $editId) ? ' editing' : '' ?>" id="silabus">
      <h2>📘 Silabus</h2>
      <form class="ak-form" method="post" enctype="multipart/form-data"><input type="hidden" name="action"
          value="silabus"><input type="hidden" name="id"
          value="<?= ($editType === 'silabus' ? ($editRow['id'] ?? 0) : 0) ?>"><input type="hidden" name="old_file"
          value="<?= e($editType === 'silabus' ? ($editRow['file_dokumen'] ?? '') : '') ?>">
        <div class="full"><label>Mata Kuliah *</label><select name="kurikulum_id" required>
            <option value="">Pilih mata kuliah</option><?php foreach ($kur as $x): ?><option value="<?= $x['id'] ?>"
                <?= ($editType === 'silabus' && ($editRow['kurikulum_id'] ?? 0) == $x['id']) ? 'selected' : '' ?>>
                <?= e($x['prodi_nama']) ?> — <?= e($x['kode_mk']) ?> <?= e($x['nama_mk']) ?></option><?php endforeach; ?>
          </select></div>
        <div class="full"><label>Judul Silabus *</label><input name="judul"
            value="<?= e($editType === 'silabus' ? ($editRow['judul'] ?? '') : '') ?>" required></div>
        <div class="full"><label>File PDF/Office (maks. 10 MB)</label><input type="file" name="file_dokumen"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"></div>
        <div class="full"><label>Deskripsi</label><textarea
            name="deskripsi"><?= e($editType === 'silabus' ? ($editRow['deskripsi'] ?? '') : '') ?></textarea></div>
        <div class="full"><button
            class="ak-btn"><?= ($editType === 'silabus' && $editId) ? 'Simpan Perubahan' : '+ Simpan Silabus' ?></button></div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Prodi</th>
            <th>Mata Kuliah</th>
            <th>Silabus</th>
            <th>Aksi</th>
          </tr><?php foreach ($sil as $x): ?><tr>
              <td><?= e($x['prodi_nama']) ?></td>
              <td><?= e($x['nama_mk']) ?></td>
              <td><a href="../../uploads/akademik/<?= rawurlencode(basename($x['file_dokumen'])) ?>"
                  target="_blank"><?= e($x['judul']) ?></a></td>
              <td>
                <form method="post" class="js-delete-form" data-confirm="Hapus silabus?"><input type="hidden"
                    name="action" value="silabus_delete"><input type="hidden" name="id" value="<?= $x['id'] ?>"><a
                    class="ak-btn light" href="?edit_type=silabus&edit_id=<?= $x['id'] ?>#silabus">Edit</a></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
    <section class="ak-panel<?= ($editType === 'kalender' && $editId) ? ' editing' : '' ?>" id="kalender">
      <h2>🗓️ Kalender Akademik</h2>
      <form class="ak-form" method="post"><input type="hidden" name="action" value="kalender"><input type="hidden"
          name="id" value="<?= ($editType === 'kalender' ? ($editRow['id'] ?? 0) : 0) ?>">
        <div><label>Tahun Ajaran</label><input name="tahun_ajaran"
            value="<?= e($editType === 'kalender' ? ($editRow['tahun_ajaran'] ?? '') : '') ?>" placeholder="2026/2027" required>
        </div>
        <div><label>Kategori</label><input name="kategori"
            value="<?= e($editType === 'kalender' ? ($editRow['kategori'] ?? '') : '') ?>"
            placeholder="Perkuliahan / Ujian / Libur" required></div>
        <div class="full"><label>Judul</label><input name="judul"
            value="<?= e($editType === 'kalender' ? ($editRow['judul'] ?? '') : '') ?>" required></div>
        <div><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai"
            value="<?= e($editType === 'kalender' ? ($editRow['tanggal_mulai'] ?? '') : '') ?>" required></div>
        <div><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai"
            value="<?= e($editType === 'kalender' ? ($editRow['tanggal_selesai'] ?? '') : '') ?>"></div>
        <div class="full"><label>Keterangan</label><textarea
            name="keterangan"><?= e($editType === 'kalender' ? ($editRow['keterangan'] ?? '') : '') ?></textarea></div>
        <div><label>Urutan</label><input type="number" name="nomor_urut"
            value="<?= e($editType === 'kalender' ? ($editRow['nomor_urut'] ?? 1) : 1) ?>"></div>
        <div><label>&nbsp;</label><button
            class="ak-btn"><?= ($editType === 'kalender' && $editId) ? 'Simpan Perubahan' : '+ Tambah Kalender' ?></button></div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Tahun</th>
            <th>Kategori</th>
            <th>Kegiatan</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr><?php foreach ($kal as $x): ?><tr>
              <td><?= e($x['tahun_ajaran']) ?></td>
              <td><?= e($x['kategori']) ?></td>
              <td><?= e($x['judul']) ?></td>
              <td>
                <?= tanggal_id($x['tanggal_mulai']) ?><?= $x['tanggal_selesai'] ? ' - ' . tanggal_id($x['tanggal_selesai']) : '' ?>
              </td>
              <td>
                <form method="post" class="js-delete-form" data-confirm="Hapus kalender?"><input type="hidden"
                    name="action" value="delete"><input type="hidden" name="jenis" value="kalender"><input type="hidden"
                    name="id" value="<?= $x['id'] ?>"><a class="ak-btn light"
                    href="?edit_type=kalender&edit_id=<?= $x['id'] ?>#kalender">Edit</a><button
                    class="ak-btn danger">Hapus</button></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
    <section class="ak-panel<?= ($editType === 'jadwal' && $editId) ? ' editing' : '' ?>" id="jadwal">
      <h2>🕘 Jadwal Kuliah &amp; Ujian</h2>
      <form class="ak-form" method="post">
        <input type="hidden" name="action" value="jadwal">
        <input type="hidden" name="id" value="<?= ($editType === 'jadwal' ? ($editRow['id'] ?? 0) : 0) ?>">
        <div><label>Program Studi</label><select name="prodi_id">
            <option value="0">Semua Prodi</option><?php foreach ($prodi as $p): ?>
              <option value="<?= $p['id'] ?>"
                <?= ($editType === 'jadwal' && (int)($editRow['prodi_id'] ?? 0) === (int)$p['id']) ? 'selected' : '' ?>>
                <?= e($p['nama']) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div><label>Jenis</label><select name="jenis">
            <?php foreach (['Kuliah', 'UTS', 'UAS'] as $jenis): ?>
              <option value="<?= e($jenis) ?>"
                <?= ($editType === 'jadwal' && ($editRow['jenis'] ?? 'Kuliah') === $jenis) ? 'selected' : '' ?>><?= e($jenis) ?>
              </option>
            <?php endforeach; ?>
          </select></div>
        <div><label>Kode MK</label><input name="kode_mk"
            value="<?= e($editType === 'jadwal' ? ($editRow['kode_mk'] ?? '') : '') ?>"></div>
        <div><label>Nama Kegiatan *</label><input name="nama_kegiatan"
            value="<?= e($editType === 'jadwal' ? ($editRow['nama_kegiatan'] ?? '') : '') ?>" required></div>
        <div><label>Tanggal</label><input type="date" name="tanggal"
            value="<?= e($editType === 'jadwal' ? ($editRow['tanggal'] ?? '') : '') ?>" required></div>
        <div><label>Hari</label><input name="hari" value="<?= e($editType === 'jadwal' ? ($editRow['hari'] ?? '') : '') ?>"></div>
        <div><label>Jam Mulai</label><input type="time" name="jam_mulai"
            value="<?= e($editType === 'jadwal' ? substr((string)($editRow['jam_mulai'] ?? ''), 0, 5) : '') ?>" required></div>
        <div><label>Jam Selesai</label><input type="time" name="jam_selesai"
            value="<?= e($editType === 'jadwal' ? substr((string)($editRow['jam_selesai'] ?? ''), 0, 5) : '') ?>" required></div>
        <div><label>Ruang</label><input name="ruang" value="<?= e($editType === 'jadwal' ? ($editRow['ruang'] ?? '') : '') ?>">
        </div>
        <div><label>Keterangan</label><input name="keterangan"
            value="<?= e($editType === 'jadwal' ? ($editRow['keterangan'] ?? '') : '') ?>"></div>
        <div class="full"><button
            class="ak-btn"><?= ($editType === 'jadwal' && $editId) ? 'Simpan Perubahan' : '+ Tambah Jadwal' ?></button><?php if ($editType === 'jadwal' && $editId): ?><a
              class="ak-btn light" href="index.php#jadwal">Batal Edit</a><?php endif; ?></div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Jenis</th>
            <th>Kegiatan</th>
            <th>Prodi</th>
            <th>Tanggal</th>
            <th>Jam/Ruang</th>
            <th>Aksi</th>
          </tr>
          <?php foreach ($jad as $x): ?><tr>
              <td><?= e($x['jenis']) ?></td>
              <td><?= e($x['nama_kegiatan']) ?></td>
              <td><?= e($x['prodi_nama'] ?: 'Semua') ?></td>
              <td><?= tanggal_id($x['tanggal']) ?></td>
              <td><?= e(substr($x['jam_mulai'], 0, 5)) ?>-<?= e(substr($x['jam_selesai'], 0, 5)) ?><br><?= e($x['ruang']) ?></td>
              <td>
                <a class="ak-btn light" href="?edit_type=jadwal&edit_id=<?= $x['id'] ?>#jadwal">Edit</a>
                <form method="post" style="display:inline" class="js-delete-form" data-confirm="Hapus jadwal?"><input
                    type="hidden" name="action" value="delete"><input type="hidden" name="jenis" value="jadwal"><input
                    type="hidden" name="id" value="<?= $x['id'] ?>"><button class="ak-btn danger">Hapus</button></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
    <section class="ak-panel<?= ($editType === 'registrasi' && $editId) ? ' editing' : '' ?>" id="registrasi">
      <h2>📝 Jadwal Registrasi</h2>
      <form class="ak-form" method="post"><input type="hidden" name="action" value="registrasi"><input type="hidden"
          name="id" value="<?= ($editType === 'registrasi' ? ($editRow['id'] ?? 0) : 0) ?>">
        <div><label>Tahun Ajaran</label><input name="tahun_ajaran"
            value="<?= e($editType === 'registrasi' ? ($editRow['tahun_ajaran'] ?? '') : '') ?>" required></div>
        <div><label>Jenis</label><select
            name="jenis"><?php foreach (['UKT/SPP', 'KRS', 'Persetujuan KRS', 'Registrasi'] as $jenis): ?><option
                value="<?= e($jenis) ?>"
                <?= ($editType === 'registrasi' && ($editRow['jenis'] ?? 'Registrasi') === $jenis) ? 'selected' : '' ?>><?= e($jenis) ?>
              </option><?php endforeach; ?></select></div>
        <div class="full"><label>Judul</label><input name="judul"
            value="<?= e($editType === 'registrasi' ? ($editRow['judul'] ?? '') : '') ?>" required></div>
        <div><label>Mulai</label><input type="date" name="tanggal_mulai"
            value="<?= e($editType === 'registrasi' ? ($editRow['tanggal_mulai'] ?? '') : '') ?>" required></div>
        <div><label>Selesai</label><input type="date" name="tanggal_selesai"
            value="<?= e($editType === 'registrasi' ? ($editRow['tanggal_selesai'] ?? '') : '') ?>"></div>
        <div class="full"><label>Keterangan</label><textarea
            name="keterangan"><?= e($editType === 'registrasi' ? ($editRow['keterangan'] ?? '') : '') ?></textarea></div>
        <div class="full"><button
            class="ak-btn"><?= ($editType === 'registrasi' && $editId) ? 'Simpan Perubahan' : '+ Tambah Registrasi' ?></button>
        </div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Tahun</th>
            <th>Jenis</th>
            <th>Judul</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr><?php foreach ($reg as $x): ?><tr>
              <td><?= e($x['tahun_ajaran']) ?></td>
              <td><?= e($x['jenis']) ?></td>
              <td><?= e($x['judul']) ?></td>
              <td>
                <?= tanggal_id($x['tanggal_mulai']) ?><?= $x['tanggal_selesai'] ? ' - ' . tanggal_id($x['tanggal_selesai']) : '' ?>
              </td>
              <td>
                <form method="post" class="js-delete-form" data-confirm="Hapus registrasi?"><input type="hidden"
                    name="action" value="delete"><input type="hidden" name="jenis" value="registrasi"><input type="hidden"
                    name="id" value="<?= $x['id'] ?>"><a class="ak-btn light"
                    href="?edit_type=registrasi&edit_id=<?= $x['id'] ?>#registrasi">Edit</a><button
                    class="ak-btn danger">Hapus</button></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
    <section class="ak-panel<?= ($editType === 'dokumen' && $editId) ? ' editing' : '' ?>" id="dokumen">
      <h2>📁 Administrasi &amp; Dokumen Mahasiswa</h2>
      <div class="ak-note">Kategori: <strong>Panduan Akademik</strong>, <strong>Unduhan Formulir</strong>, dan
        <strong>Layanan Kelulusan</strong>.
      </div>
      <form class="ak-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="dokumen">
        <input type="hidden" name="id" value="<?= ($editType === 'dokumen' ? ($editRow['id'] ?? 0) : 0) ?>">
        <input type="hidden" name="old_file" value="<?= e($editType === 'dokumen' ? ($editRow['file_dokumen'] ?? '') : '') ?>">
        <div><label>Kategori</label><select name="kategori">
            <?php foreach (['panduan' => 'Panduan Akademik', 'formulir' => 'Unduhan Formulir', 'kelulusan' => 'Layanan Kelulusan'] as $k => $label): ?>
              <option value="<?= e($k) ?>"
                <?= ($editType === 'dokumen' && ($editRow['kategori'] ?? 'panduan') === $k) ? 'selected' : '' ?>><?= e($label) ?>
              </option>
            <?php endforeach; ?>
          </select></div>
        <div><label>Urutan</label><input type="number" name="nomor_urut"
            value="<?= e($editType === 'dokumen' ? ($editRow['nomor_urut'] ?? 1) : 1) ?>"></div>
        <div class="full"><label>Judul *</label><input name="judul"
            value="<?= e($editType === 'dokumen' ? ($editRow['judul'] ?? '') : '') ?>" required></div>
        <div class="full"><label>Deskripsi</label><textarea
            name="deskripsi"><?= e($editType === 'dokumen' ? ($editRow['deskripsi'] ?? '') : '') ?></textarea></div>
        <div class="full"><label>Isi / Persyaratan (untuk layanan kelulusan, satu poin per baris)</label><textarea
            name="isi" placeholder="Persyaratan skripsi/tesis
Pendaftaran yudisium
Pelaksanaan wisuda"><?= e($editType === 'dokumen' ? ($editRow['isi'] ?? '') : '') ?></textarea></div>
        <div><label>File</label><input type="file" name="file_dokumen"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"><?php if ($editType === 'dokumen' && !empty($editRow['file_dokumen'])): ?>
            <div class="mini">File saat ini: <a
                href="../../uploads/akademik/<?= rawurlencode(basename($editRow['file_dokumen'])) ?>"
                target="_blank"><?= e(basename($editRow['file_dokumen'])) ?></a>. Kosongkan jika tidak ingin mengganti.
            </div><?php endif; ?>
        </div>
        <div><label>Link URL (opsional)</label><input name="link_url"
            value="<?= e($editType === 'dokumen' ? ($editRow['link_url'] ?? '') : '') ?>"></div>
        <div class="full"><button
            class="ak-btn"><?= ($editType === 'dokumen' && $editId) ? 'Simpan Perubahan' : '+ Tambah Dokumen' ?></button><?php if ($editType === 'dokumen' && $editId): ?><a
              class="ak-btn light" href="index.php#dokumen">Batal Edit</a><?php endif; ?></div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Kategori</th>
            <th>Judul</th>
            <th>File</th>
            <th>Aksi</th>
          </tr>
          <?php foreach ($doc as $x): ?><tr>
              <td><?= e($x['kategori']) ?></td>
              <td><?= e($x['judul']) ?><br><span class="mini"><?= e($x['deskripsi']) ?></span></td>
              <td><?php if ($x['file_dokumen']): ?><a
                    href="../../uploads/akademik/<?= rawurlencode(basename($x['file_dokumen'])) ?>"
                    target="_blank">Buka</a><?php else: ?>-<?php endif; ?></td>
              <td>
                <a class="ak-btn light" href="?edit_type=dokumen&edit_id=<?= $x['id'] ?>#dokumen">Edit</a>
                <form method="post" style="display:inline" class="js-delete-form" data-confirm="Hapus dokumen?"><input
                    type="hidden" name="action" value="delete"><input type="hidden" name="jenis" value="dokumen"><input
                    type="hidden" name="id" value="<?= $x['id'] ?>"><button class="ak-btn danger">Hapus</button></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
    <section class="ak-panel<?= ($editType === 'penilaian' && $editId) ? ' editing' : '' ?>" id="penilaian">
      <h2>📊 Sistem Penilaian</h2>
      <form class="ak-form" method="post"><input type="hidden" name="action" value="penilaian"><input type="hidden"
          name="id" value="<?= ($editType === 'penilaian' ? ($editRow['id'] ?? 0) : 0) ?>">
        <div><label>Komponen</label><input name="komponen"
            value="<?= e($editType === 'penilaian' ? ($editRow['komponen'] ?? '') : '') ?>" required></div>
        <div><label>Bobot (%)</label><input type="number" step="0.01" name="bobot"
            value="<?= e($editType === 'penilaian' ? ($editRow['bobot'] ?? 0) : 0) ?>" required></div>
        <div class="full"><label>Keterangan</label><input name="keterangan"
            value="<?= e($editType === 'penilaian' ? ($editRow['keterangan'] ?? '') : '') ?>"></div>
        <div><label>Urutan</label><input type="number" name="nomor_urut"
            value="<?= e($editType === 'penilaian' ? ($editRow['nomor_urut'] ?? 1) : 1) ?>"></div>
        <div><label>&nbsp;</label><button
            class="ak-btn"><?= ($editType === 'penilaian' && $editId) ? 'Simpan Perubahan' : '+ Tambah Komponen' ?></button></div>
      </form>
      <hr>
      <div class="ak-table-wrap">
        <table class="ak-table">
          <tr>
            <th>Komponen</th>
            <th>Bobot</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr><?php foreach ($nilai as $x): ?><tr>
              <td><?= e($x['komponen']) ?></td>
              <td><?= e($x['bobot']) ?>%</td>
              <td><?= e($x['keterangan']) ?></td>
              <td>
                <form method="post" class="js-delete-form" data-confirm="Hapus komponen penilaian?"><input type="hidden"
                    name="action" value="delete"><input type="hidden" name="jenis" value="penilaian"><input type="hidden"
                    name="id" value="<?= $x['id'] ?>"><a class="ak-btn light"
                    href="?edit_type=penilaian&edit_id=<?= $x['id'] ?>#penilaian">Edit</a><button
                    class="ak-btn danger">Hapus</button></form>
              </td>
            </tr><?php endforeach; ?>
        </table>
      </div>
    </section>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toast sukses setelah proses tambah/edit/hapus selesai.
    const params = new URLSearchParams(window.location.search);
    const ok = params.get('ok');
    const err = params.get('err');

    if (ok) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: ok,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2600,
        timerProgressBar: true
      });
      window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
    }

    if (err) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: err,
        confirmButtonText: 'Tutup'
      });
      window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
    }

    // Konfirmasi hapus yang lebih cantik dan aman.
    document.querySelectorAll('.js-delete-form').forEach(function(form) {
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        const message = form.dataset.confirm || 'Yakin ingin menghapus data ini?';

        Swal.fire({
          icon: 'warning',
          title: 'Konfirmasi Hapus',
          text: message,
          showCancelButton: true,
          confirmButtonText: 'Ya, Hapus',
          cancelButtonText: 'Batal',
          reverseButtons: true,
          focusCancel: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d'
        }).then(function(result) {
          if (result.isConfirmed) {
            // Lepaskan handler agar submit benar-benar berjalan.
            form.dataset.swalConfirmed = '1';
            form.submit();
          }
        });
      });
    });

    // Jika user sedang mengedit, beri indikator kecil pada panel.
    const editing = document.querySelector('.ak-panel.editing');
    if (editing) {
      const title = editing.querySelector('h2');
      if (title && !title.querySelector('.edit-indicator')) {
        const badge = document.createElement('span');
        badge.className = 'edit-indicator';
        badge.textContent = 'Mode Edit';
        badge.style.cssText =
          'font-size:10px;padding:5px 9px;border-radius:999px;' +
          'background:#e6f7f1;color:#087f5b;margin-left:auto;font-weight:800;';
        title.appendChild(badge);
      }
    }
  });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
