<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
header('Content-Type: application/json; charset=utf-8');

function out($success, $message = '', $data = null)
{
  echo json_encode(['success' => $success, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
  exit;
}

function uploadFotoProgramStudi($file, $fotoLama = '')
{
  if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return $fotoLama;
  if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Gagal mengunggah foto.');
  if ($file['size'] > 2 * 1024 * 1024) throw new Exception('Ukuran foto maksimal 2 MB.');

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  if (!isset($allowed[$mime])) throw new Exception('Format foto harus JPG, JPEG, PNG atau WEBP.');

  $folder = dirname(__DIR__, 2) . '/uploads/program-studi/';
  if (!is_dir($folder) && !mkdir($folder, 0755, true)) throw new Exception('Folder upload foto tidak dapat dibuat.');

  $nama = 'prodi_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
  if (!move_uploaded_file($file['tmp_name'], $folder . $nama)) throw new Exception('Foto gagal disimpan.');
  return $nama;
}

function uploadBrosurProgramStudi($file, $brosurLama = '')
{
  if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return $brosurLama;
  if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Gagal mengunggah brosur.');
  if ($file['size'] > 5 * 1024 * 1024) throw new Exception('Ukuran brosur maksimal 5 MB.');

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  if ($mime !== 'application/pdf') throw new Exception('Format brosur harus PDF.');

  $folder = dirname(__DIR__, 2) . '/uploads/program-studi/';
  if (!is_dir($folder) && !mkdir($folder, 0755, true)) throw new Exception('Folder upload brosur tidak dapat dibuat.');

  $nama = 'brosur_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.pdf';
  if (!move_uploaded_file($file['tmp_name'], $folder . $nama)) throw new Exception('Brosur gagal disimpan.');
  return $nama;
}

function hapusFileProdi($namaFile)
{
  if (!$namaFile) return;
  $file = dirname(__DIR__, 2) . '/uploads/program-studi/' . basename($namaFile);
  if (is_file($file)) @unlink($file);
}

$a = $_GET['action'] ?? $_POST['action'] ?? '';

try {
  if ($a === 'list') {
    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? '');

    $sql = "SELECT id,kode_prodi,nama,jenjang,gelar,status,foto,brosur FROM program_studi
                WHERE (kode_prodi LIKE :search_kode OR nama LIKE :search_nama)";
    $params = [':search_kode' => '%' . $search . '%', ':search_nama' => '%' . $search . '%'];
    if ($status !== '') {
      $sql .= " AND status = :status";
      $params[':status'] = $status;
    }
    $sql .= " ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    out(true, 'Data berhasil dimuat.', $stmt->fetchAll());
  }

  if ($a === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    $q = $pdo->prepare('SELECT * FROM program_studi WHERE id=?');
    $q->execute([$id]);
    $x = $q->fetch();
    if (!$x) out(false, 'Data tidak ditemukan.');

    foreach (['misi' => 'prodi_misi', 'cpl' => 'prodi_capaian_pembelajaran', 'kurikulum' => 'prodi_kurikulum', 'fasilitas' => 'prodi_fasilitas'] as $k => $t) {
      $q = $pdo->prepare("SELECT * FROM $t WHERE prodi_id=? ORDER BY nomor_urut,id");
      $q->execute([$id]);
      $x[$k] = $q->fetchAll();
    }
    out(true, '', $x);
  }

  if ($a === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $fields = [
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
    foreach ($fields as $field) $v[$field] = trim($_POST[$field] ?? '');
    $v['sks_lulus'] = $v['sks_lulus'] === '' ? null : (int)$v['sks_lulus'];
    $v['tanggal_akreditasi'] = $v['tanggal_akreditasi'] === '' ? null : $v['tanggal_akreditasi'];

    $fotoLama = '';
    $brosurLama = '';
    if ($id) {
      $q = $pdo->prepare('SELECT foto,brosur FROM program_studi WHERE id=?');
      $q->execute([$id]);
      $lama = $q->fetch();
      if (!$lama) out(false, 'Data Program Studi tidak ditemukan.');
      $fotoLama = $lama['foto'] ?? '';
      $brosurLama = $lama['brosur'] ?? '';
    }

    $fotoBaru = uploadFotoProgramStudi($_FILES['foto'] ?? null, $fotoLama);
    $brosurBaru = uploadBrosurProgramStudi($_FILES['brosur'] ?? null, $brosurLama);
    $v['foto'] = $fotoBaru;
    $v['brosur'] = $brosurBaru;

    try {
      $pdo->beginTransaction();
      if ($id) {
        $setFields = array_merge($fields, ['foto', 'brosur']);
        $set = implode(',', array_map(fn($x) => "$x=:$x", $setFields));
        $v['id'] = $id;
        $pdo->prepare("UPDATE program_studi SET $set WHERE id=:id")->execute($v);
      } else {
        $insertFields = array_merge($fields, ['foto', 'brosur']);
        $cols = implode(',', $insertFields);
        $pars = ':' . implode(',:', $insertFields);
        $pdo->prepare("INSERT INTO program_studi($cols) VALUES($pars)")->execute($v);
        $id = (int)$pdo->lastInsertId();
      }

      $pdo->prepare('DELETE FROM prodi_misi WHERE prodi_id=?')->execute([$id]);
      foreach ($_POST['misi'] ?? [] as $i => $z) if (trim($z) !== '')
        $pdo->prepare('INSERT INTO prodi_misi(prodi_id,nomor_urut,isi) VALUES(?,?,?)')->execute([$id, $i + 1, trim($z)]);

      $pdo->prepare('DELETE FROM prodi_capaian_pembelajaran WHERE prodi_id=?')->execute([$id]);
      foreach ($_POST['cpl_isi'] ?? [] as $i => $z) if (trim($z) !== '')
        $pdo->prepare('INSERT INTO prodi_capaian_pembelajaran(prodi_id,kategori,isi,nomor_urut) VALUES(?,?,?,?)')->execute([$id, trim($_POST['cpl_kategori'][$i] ?? 'Umum'), trim($z), $i + 1]);

      $pdo->prepare('DELETE FROM prodi_kurikulum WHERE prodi_id=?')->execute([$id]);
      foreach ($_POST['kur_nama'] ?? [] as $i => $z) if (trim($z) !== '')
        $pdo->prepare('INSERT INTO prodi_kurikulum(prodi_id,kode_mk,nama_mk,semester,sks,nomor_urut) VALUES(?,?,?,?,?,?)')->execute([$id, trim($_POST['kur_kode'][$i] ?? ''), trim($z), trim($_POST['kur_semester'][$i] ?? ''), (float)($_POST['kur_sks'][$i] ?? 0), $i + 1]);

      $pdo->prepare('DELETE FROM prodi_fasilitas WHERE prodi_id=?')->execute([$id]);
      foreach ($_POST['fas_nama'] ?? [] as $i => $z) if (trim($z) !== '')
        $pdo->prepare('INSERT INTO prodi_fasilitas(prodi_id,nama_fasilitas,deskripsi,nomor_urut) VALUES(?,?,?,?)')->execute([$id, trim($z), trim($_POST['fas_desc'][$i] ?? ''), $i + 1]);

      $pdo->commit();
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      if ($fotoBaru && $fotoBaru !== $fotoLama) hapusFileProdi($fotoBaru);
      if ($brosurBaru && $brosurBaru !== $brosurLama) hapusFileProdi($brosurBaru);
      throw $e;
    }

    if ($fotoLama && $fotoBaru !== $fotoLama) hapusFileProdi($fotoLama);
    if ($brosurLama && $brosurBaru !== $brosurLama) hapusFileProdi($brosurLama);
    out(true, 'Program Studi berhasil disimpan.');
  }

  if ($a === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $q = $pdo->prepare('SELECT foto,brosur FROM program_studi WHERE id=?');
    $q->execute([$id]);
    $file = $q->fetch();
    if (!$file) out(false, 'Data tidak ditemukan.');

    $pdo->beginTransaction();
    foreach (['prodi_misi', 'prodi_capaian_pembelajaran', 'prodi_kurikulum', 'prodi_fasilitas'] as $t)
      $pdo->prepare("DELETE FROM $t WHERE prodi_id=?")->execute([$id]);
    $pdo->prepare('DELETE FROM program_studi WHERE id=?')->execute([$id]);
    $pdo->commit();
    hapusFileProdi($file['foto'] ?? '');
    hapusFileProdi($file['brosur'] ?? '');
    out(true, 'Program Studi berhasil dihapus.');
  }

  out(false, 'Aksi tidak dikenali.');
} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  out(false, $e->getMessage());
}
