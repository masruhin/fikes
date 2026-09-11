<?php

require_once __DIR__ . '/../../config/auth.php';

wajib_login();

header('Content-Type: application/json');


// ======================================================
// OUTPUT JSON
// ======================================================

function out($s, $m = '', $d = null)
{
  echo json_encode([
    'success' => $s,
    'message' => $m,
    'data'    => $d
  ], JSON_UNESCAPED_UNICODE);

  exit;
}


// ======================================================
// UPLOAD FOTO PROGRAM STUDI
// ======================================================
//FUNGSI UPLOAD FOTO
function uploadFotoProgramStudi($file, $fotoLama = null)
{
  // Tidak ada file baru
  if (
    !isset($file) ||
    !isset($file['error']) ||
    $file['error'] === UPLOAD_ERR_NO_FILE
  ) {
    return [
      'nama' => $fotoLama,
      'file_baru' => null
    ];
  }

  // Error upload
  if ($file['error'] !== UPLOAD_ERR_OK) {
    throw new Exception('Gagal mengupload foto.');
  }

  // Maksimal 2 MB
  if ($file['size'] > 2 * 1024 * 1024) {
    throw new Exception('Ukuran foto maksimal 2 MB.');
  }

  // Cek MIME sebenarnya
  $finfo = finfo_open(FILEINFO_MIME_TYPE);

  if (!$finfo) {
    throw new Exception('Tidak dapat memeriksa format foto.');
  }

  $mime = finfo_file($finfo, $file['tmp_name']);

  finfo_close($finfo);

  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
  ];

  if (!isset($allowed[$mime])) {
    throw new Exception(
      'Format foto harus JPG, JPEG, PNG atau WEBP.'
    );
  }

  // Folder:
  // E:\xampp\htdocs\fikes\admin\uploads\program-studi\

  $folder = dirname(__DIR__, 2) . '/uploads/program-studi/';

  // Buat folder jika belum ada
  if (!is_dir($folder)) {
    if (!mkdir($folder, 0755, true)) {
      throw new Exception('Folder upload tidak dapat dibuat.');
    }
  }

  // Nama file unik
  $namaFile =
    'prodi_' .
    date('YmdHis') .
    '_' .
    bin2hex(random_bytes(4)) .
    '.' .
    $allowed[$mime];

  $tujuan = $folder . $namaFile;

  // Pindahkan file
  if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
    throw new Exception('Foto gagal disimpan ke folder upload.');
  }

  return [
    'nama' => $namaFile,
    'file_baru' => $tujuan
  ];
}

// ======================================================
// HAPUS FILE FOTO
// ======================================================

function hapusFotoProgramStudi($namaFile)
{
  if (empty($namaFile)) {
    return;
  }

  $folder = dirname(__DIR__, 2) . '/uploads/program-studi/';

  $path = $folder . basename($namaFile);

  if (is_file($path)) {
    @unlink($path);
  }
}

//FUNGSI UPLOAD BROSUR
function uploadBrosurProgramStudi($file, $brosurLama = '')
{
  if (
    !isset($file) ||
    !isset($file['error']) ||
    $file['error'] === UPLOAD_ERR_NO_FILE
  ) {
    return $brosurLama;
  }

  if ($file['error'] !== UPLOAD_ERR_OK) {
    throw new Exception('Gagal mengunggah brosur.');
  }

  // Maksimal 5 MB
  if ($file['size'] > 5 * 1024 * 1024) {
    throw new Exception('Ukuran brosur maksimal 5 MB.');
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);

  if ($mime !== 'application/pdf') {
    throw new Exception('Format brosur harus PDF.');
  }

  $folder = dirname(__DIR__, 2) . '/uploads/program-studi/';

  if (!is_dir($folder)) {
    mkdir($folder, 0755, true);
  }

  $namaFile =
    'brosur_' .
    date('YmdHis') .
    '_' .
    bin2hex(random_bytes(4)) .
    '.pdf';

  $target = $folder . $namaFile;

  if (!move_uploaded_file($file['tmp_name'], $target)) {
    throw new Exception('Brosur gagal disimpan.');
  }

  return $namaFile;
}

//FUNGSI HAPUS BROSUR
function hapusBrosurProgramStudi($namaFile)
{
  if (!$namaFile) {
    return;
  }

  $file = dirname(__DIR__, 2)
    . '/uploads/program-studi/'
    . basename($namaFile);

  if (is_file($file)) {
    @unlink($file);
  }
}


// ======================================================
// ACTION
// ======================================================

$a = $_GET['action'] ?? $_POST['action'] ?? '';


// ======================================================
// LIST
// ======================================================

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
            foto,
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


// ======================================================
// GET DETAIL
// ======================================================

if ($a === 'get') {

  $id = (int)($_GET['id'] ?? 0);

  $q = $pdo->prepare("
        SELECT *
        FROM program_studi
        WHERE id = ?
    ");

  $q->execute([$id]);

  $x = $q->fetch();

  if (!$x) {
    out(false, 'Data tidak ditemukan');
  }

  foreach (
    [
      'misi'       => 'prodi_misi',
      'cpl'        => 'prodi_capaian_pembelajaran',
      'kurikulum'  => 'prodi_kurikulum',
      'fasilitas'  => 'prodi_fasilitas'
    ] as $k => $t
  ) {

    $q = $pdo->prepare("
            SELECT *
            FROM $t
            WHERE prodi_id = ?
            ORDER BY nomor_urut, id
        ");

    $q->execute([$id]);

    $x[$k] = $q->fetchAll();
  }

  out(true, '', $x);
}


// ======================================================
// SAVE
// ======================================================

if ($a === 'save') {

  $id = (int)($_POST['id'] ?? 0);


  // ==================================================
  // FIELD PROGRAM STUDI
  // ==================================================

  $f = [
    'kode_prodi',
    'nama',
    'jenjang',
    'gelar',

    'kaprodi_nama',
    'kaprodi_nidn',
    'kaprodi_email',

    'deskripsi',
    'foto',
    'brosur',
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


  // ==================================================
  // AMBIL DATA POST
  // ==================================================

  $v = [];

  foreach ($f as $x) {

    // foto tidak berasal dari $_POST
    if ($x === 'foto') {
      continue;
    }

    $v[$x] = trim($_POST[$x] ?? '');
  }


  // ==================================================
  // KONVERSI DATA
  // ==================================================

  $v['sks_lulus'] =
    $v['sks_lulus'] === ''
    ? null
    : (int)$v['sks_lulus'];

  $v['tanggal_akreditasi'] =
    $v['tanggal_akreditasi'] === ''
    ? null
    : $v['tanggal_akreditasi'];


  // ==================================================
  // FOTO LAMA
  // ==================================================

  $fotoLama = null;

  if ($id > 0) {

    $qFoto = $pdo->prepare("
            SELECT foto
            FROM program_studi
            WHERE id = ?
        ");

    $qFoto->execute([$id]);

    $fotoLama = $qFoto->fetchColumn();
  }


  // ==================================================
  // UPLOAD FOTO BARU
  // ==================================================

  $fotoUpload = uploadFotoProgramStudi(
    $_FILES['foto'] ?? null,
    $fotoLama
  );

  $v['foto'] = $fotoUpload['nama'];

  $fotoBaruPath = $fotoUpload['file_baru'];


  try {

    $pdo->beginTransaction();


    // ==============================================
    // UPDATE
    // ==============================================

    if ($id) {

      $set = implode(
        ',',
        array_map(
          fn($x) => "$x=:$x",
          $f
        )
      );

      $v['id'] = $id;

      $pdo->prepare("
                UPDATE program_studi
                SET $set
                WHERE id = :id
            ")->execute($v);
    }


    // ==============================================
    // INSERT
    // ==============================================

    else {

      $cols = implode(',', $f);

      $pars = ':' . implode(',:', $f);

      $pdo->prepare("
                INSERT INTO program_studi($cols)
                VALUES($pars)
            ")->execute($v);

      $id = $pdo->lastInsertId();
    }


    // ==================================================
    // MISI
    // ==================================================

    $pdo->prepare("
            DELETE FROM prodi_misi
            WHERE prodi_id = ?
        ")->execute([$id]);

    foreach ($_POST['misi'] ?? [] as $i => $z) {

      if (trim($z) !== '') {

        $pdo->prepare("
                    INSERT INTO prodi_misi
                    (
                        prodi_id,
                        nomor_urut,
                        isi
                    )
                    VALUES (?, ?, ?)
                ")->execute([
          $id,
          $i + 1,
          trim($z)
        ]);
      }
    }


    // ==================================================
    // CPL
    // ==================================================

    $pdo->prepare("
            DELETE FROM prodi_capaian_pembelajaran
            WHERE prodi_id = ?
        ")->execute([$id]);

    foreach ($_POST['cpl_isi'] ?? [] as $i => $z) {

      if (trim($z) !== '') {

        $pdo->prepare("
                    INSERT INTO prodi_capaian_pembelajaran
                    (
                        prodi_id,
                        kategori,
                        isi,
                        nomor_urut
                    )
                    VALUES (?, ?, ?, ?)
                ")->execute([
          $id,
          trim(
            $_POST['cpl_kategori'][$i]
              ?? 'Umum'
          ),
          trim($z),
          $i + 1
        ]);
      }
    }


    // ==================================================
    // KURIKULUM
    // ==================================================

    $pdo->prepare("
            DELETE FROM prodi_kurikulum
            WHERE prodi_id = ?
        ")->execute([$id]);

    foreach ($_POST['kur_nama'] ?? [] as $i => $z) {

      if (trim($z) !== '') {

        $pdo->prepare("
                    INSERT INTO prodi_kurikulum
                    (
                        prodi_id,
                        kode_mk,
                        nama_mk,
                        semester,
                        sks,
                        nomor_urut
                    )
                    VALUES (?, ?, ?, ?, ?, ?)
                ")->execute([
          $id,
          trim($_POST['kur_kode'][$i] ?? ''),
          trim($z),
          trim($_POST['kur_semester'][$i] ?? ''),
          (float)($_POST['kur_sks'][$i] ?? 0),
          $i + 1
        ]);
      }
    }


    // ==================================================
    // FASILITAS
    // ==================================================

    $pdo->prepare("
            DELETE FROM prodi_fasilitas
            WHERE prodi_id = ?
        ")->execute([$id]);

    foreach ($_POST['fas_nama'] ?? [] as $i => $z) {

      if (trim($z) !== '') {

        $pdo->prepare("
                    INSERT INTO prodi_fasilitas
                    (
                        prodi_id,
                        nama_fasilitas,
                        deskripsi,
                        nomor_urut
                    )
                    VALUES (?, ?, ?, ?)
                ")->execute([
          $id,
          trim($z),
          trim($_POST['fas_desc'][$i] ?? ''),
          $i + 1
        ]);
      }
    }


    // ==================================================
    // COMMIT
    // ==================================================

    $pdo->commit();


    // ==================================================
    // HAPUS FOTO LAMA
    // HANYA JIKA FOTO BARU BERHASIL DISIMPAN
    // ==================================================

    if (
      !empty($fotoBaruPath) &&
      !empty($fotoLama) &&
      $fotoLama !== $v['foto']
    ) {

      hapusFotoProgramStudi($fotoLama);
    }


    out(
      true,
      'Program Studi berhasil disimpan.'
    );
  } catch (Throwable $e) {

    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }


    // Jika database gagal disimpan,
    // hapus foto baru agar tidak menjadi file sampah

    if (!empty($fotoBaruPath) && is_file($fotoBaruPath)) {
      @unlink($fotoBaruPath);
    }


    out(
      false,
      $e->getMessage()
    );
  }
}


// ======================================================
// DELETE
// ======================================================

if ($a === 'delete') {

  $id = (int)($_POST['id'] ?? 0);


  // Ambil nama foto sebelum data dihapus

  $qFoto = $pdo->prepare("
        SELECT foto
        FROM program_studi
        WHERE id = ?
    ");

  $qFoto->execute([$id]);

  $foto = $qFoto->fetchColumn();


  try {

    $pdo->beginTransaction();


    // Hapus data pendukung

    foreach (
      [
        'prodi_misi',
        'prodi_capaian_pembelajaran',
        'prodi_kurikulum',
        'prodi_fasilitas'
      ] as $t
    ) {

      $pdo->prepare("
                DELETE FROM $t
                WHERE prodi_id = ?
            ")->execute([$id]);
    }


    // Hapus program studi

    $pdo->prepare("
            DELETE FROM program_studi
            WHERE id = ?
        ")->execute([$id]);


    $pdo->commit();


    // Hapus file foto

    if (!empty($foto)) {
      hapusFotoProgramStudi($foto);
    }


    out(
      true,
      'Program Studi berhasil dihapus.'
    );
  } catch (Throwable $e) {

    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }

    out(
      false,
      $e->getMessage()
    );
  }
}


// ======================================================
// ACTION TIDAK DIKENALI
// ======================================================

out(false, 'Aksi tidak dikenali');
