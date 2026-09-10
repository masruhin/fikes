<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
header('Content-Type: application/json');
function out($s, $m = '', $d = null)
{
  echo json_encode(['success' => $s, 'message' => $m, 'data' => $d], JSON_UNESCAPED_UNICODE);
  exit;
}
$a = $_GET['action'] ?? $_POST['action'] ?? '';
if ($a === 'list') {

  $search = trim($_GET['search'] ?? '');
  $status = trim($_GET['status'] ?? '');

  $sql = "
        SELECT
            id,
            kode_prodi,
            nama,
            jenjang,
            gelar,
            status
        FROM program_studi
        WHERE (
            kode_prodi LIKE :search_kode
            OR nama LIKE :search_nama
        )
    ";

  $params = [
    ':search_kode' => '%' . $search . '%',
    ':search_nama' => '%' . $search . '%'
  ];

  if ($status !== '') {

    $sql .= " AND status = :status";

    $params[':status'] = $status;
  }

  $sql .= " ORDER BY id ASC";

  $stmt = $pdo->prepare($sql);

  $stmt->execute($params);

  $data = $stmt->fetchAll();

  out(
    true,
    'Data berhasil dimuat.',
    $data
  );
}
if ($a === 'get') {
  $id = (int)$_GET['id'];
  $q = $pdo->prepare("SELECT * FROM program_studi WHERE id=?");
  $q->execute([$id]);
  $x = $q->fetch();
  if (!$x) out(false, 'Data tidak ditemukan');
  foreach (['misi' => 'prodi_misi', 'cpl' => 'prodi_capaian_pembelajaran', 'kurikulum' => 'prodi_kurikulum', 'fasilitas' => 'prodi_fasilitas'] as $k => $t) {
    $q = $pdo->prepare("SELECT * FROM $t WHERE prodi_id=? ORDER BY nomor_urut,id");
    $q->execute([$id]);
    $x[$k] = $q->fetchAll();
  }
  out(true, '', $x);
}
if ($a === 'save') {
  $id = (int)($_POST['id'] ?? 0);
  $f = [
    'kode_prodi',
    'nama',
    'jenjang',
    'gelar',

    'kaprodi_nama',
    'kaprodi_nidn',
    'kaprodi_email',

    'deskripsi',
    'status',

    'sekretaris_nama',
    'sekretaris_nidn',

    'kontak_telepon',
    'kontak_email',
    'alamat',

    'durasi_studi',
    'sks_lulus',

    'akreditasi',
    'nomor_akreditasi',
    'tanggal_akreditasi',

    'visi'
  ];
  $v = [];
  foreach ($f as $x) $v[$x] = trim($_POST[$x] ?? '');
  $v['sks_lulus'] = $v['sks_lulus'] === '' ? null : (int)$v['sks_lulus'];
  $v['tanggal_akreditasi'] = $v['tanggal_akreditasi'] === '' ? null : $v['tanggal_akreditasi'];
  try {
    $pdo->beginTransaction();
    if ($id) {
      $set = implode(',', array_map(fn($x) => "$x=:$x", $f));
      $v['id'] = $id;
      $pdo->prepare("UPDATE program_studi SET $set WHERE id=:id")->execute($v);
    } else {
      $cols = implode(',', $f);
      $pars = ':' . implode(',:', $f);
      $pdo->prepare("INSERT INTO program_studi($cols) VALUES($pars)")->execute($v);
      $id = $pdo->lastInsertId();
    }
    $pdo->prepare("DELETE FROM prodi_misi WHERE prodi_id=?")->execute([$id]);
    foreach ($_POST['misi'] ?? [] as $i => $z) if (trim($z) !== '') $pdo->prepare("INSERT INTO prodi_misi(prodi_id,nomor_urut,isi) VALUES(?,?,?)")->execute([$id, $i + 1, trim($z)]);
    $pdo->prepare("DELETE FROM prodi_capaian_pembelajaran WHERE prodi_id=?")->execute([$id]);
    foreach ($_POST['cpl_isi'] ?? [] as $i => $z) if (trim($z) !== '') $pdo->prepare("INSERT INTO prodi_capaian_pembelajaran(prodi_id,kategori,isi,nomor_urut) VALUES(?,?,?,?)")->execute([$id, trim($_POST['cpl_kategori'][$i] ?? 'Umum'), trim($z), $i + 1]);
    $pdo->prepare("DELETE FROM prodi_kurikulum WHERE prodi_id=?")->execute([$id]);
    foreach ($_POST['kur_nama'] ?? [] as $i => $z) if (trim($z) !== '') $pdo->prepare("INSERT INTO prodi_kurikulum(prodi_id,kode_mk,nama_mk,semester,sks,nomor_urut) VALUES(?,?,?,?,?,?)")->execute([$id, trim($_POST['kur_kode'][$i] ?? ''), trim($z), trim($_POST['kur_semester'][$i] ?? ''), (float)($_POST['kur_sks'][$i] ?? 0), $i + 1]);
    $pdo->prepare("DELETE FROM prodi_fasilitas WHERE prodi_id=?")->execute([$id]);
    foreach ($_POST['fas_nama'] ?? [] as $i => $z) if (trim($z) !== '') $pdo->prepare("INSERT INTO prodi_fasilitas(prodi_id,nama_fasilitas,deskripsi,nomor_urut) VALUES(?,?,?,?)")->execute([$id, trim($z), trim($_POST['fas_desc'][$i] ?? ''), $i + 1]);
    $pdo->commit();
    out(true, 'Program Studi berhasil disimpan.');
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    out(false, $e->getMessage());
  }
}
if ($a === 'delete') {
  $id = (int)$_POST['id'];
  try {
    $pdo->beginTransaction();
    foreach (['prodi_misi', 'prodi_capaian_pembelajaran', 'prodi_kurikulum', 'prodi_fasilitas'] as $t) $pdo->prepare("DELETE FROM $t WHERE prodi_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM program_studi WHERE id=?")->execute([$id]);
    $pdo->commit();
    out(true, 'Program Studi berhasil dihapus.');
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    out(false, $e->getMessage());
  }
}
out(false, 'Aksi tidak dikenali');
