<?php
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

function json_ok($data = [])
{
  echo json_encode([
    'success' => true,
    'data' => $data
  ]);
  exit;
}

function json_error($message)
{
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => $message
  ]);
  exit;
}

/*
 * Mapping tipe detail ke tabel database.
 * Semua memakai PHP prosedural sederhana.
 */
$tables = [
  'pendidikan' => 'dosen_pendidikan',
  'ajar'       => 'dosen_bidang_ajar',
  'keilmuan'   => 'dosen_keilmuan'
];

/* SIMPAN DATA UTAMA DOSEN */
if ($action === 'save_dosen') {

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method tidak diizinkan.');
  }

  $id            = (int)($_POST['id'] ?? 0);
  $nidn          = trim($_POST['nidn'] ?? '');
  $nama          = trim($_POST['nama'] ?? '');
  $program_studi = trim($_POST['program_studi'] ?? '');
  $jabatan       = trim($_POST['jabatan'] ?? '');
  $email         = trim($_POST['email'] ?? '');
  $status        = trim($_POST['status'] ?? 'aktif');
  $hapus_foto    = (int)($_POST['hapus_foto'] ?? 0);

  if ($nama === '') {
    json_error('Nama dosen wajib diisi.');
  }

  $uploadDir = __DIR__ . '/../../uploads/dosen/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $fotoLama = '';
  if ($id > 0) {
    $stmt = $pdo->prepare("SELECT foto FROM dosen WHERE id=?");
    $stmt->execute([$id]);
    $fotoLama = (string)($stmt->fetchColumn() ?: '');
  }

  $fotoBaru = $fotoLama;

  if (!empty($_FILES['foto']['name'])) {
    $file = $_FILES['foto'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
      json_error('Upload foto gagal.');
    }

    if ($file['size'] > 2 * 1024 * 1024) {
      json_error('Ukuran foto maksimal 2 MB.');
    }

    $allowed = [
      'image/jpeg' => 'jpg',
      'image/png'  => 'png',
      'image/webp' => 'webp'
    ];

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
      json_error('Format foto harus JPG, JPEG, PNG, atau WEBP.');
    }

    $fotoBaru = 'dosen_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fotoBaru)) {
      json_error('Gagal menyimpan file foto.');
    }
  } elseif ($hapus_foto === 1) {
    $fotoBaru = '';
  }

  try {
    if ($id > 0) {
      $stmt = $pdo->prepare("
                UPDATE dosen
                SET nidn=?, nama=?, program_studi=?, jabatan=?, email=?, foto=?, status=?
                WHERE id=?
            ");

      $stmt->execute([
        $nidn,
        $nama,
        $program_studi,
        $jabatan,
        $email,
        $fotoBaru,
        $status,
        $id
      ]);
    } else {
      $stmt = $pdo->prepare("
                INSERT INTO dosen
                (nidn, nama, program_studi, jabatan, email, foto, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

      $stmt->execute([
        $nidn,
        $nama,
        $program_studi,
        $jabatan,
        $email,
        $fotoBaru,
        $status
      ]);

      $id = $pdo->lastInsertId();
    }

    if ($fotoLama && $fotoBaru !== $fotoLama) {
      $oldPath = $uploadDir . basename($fotoLama);
      if (is_file($oldPath)) {
        @unlink($oldPath);
      }
    }

    echo json_encode([
      'success' => true,
      'id' => (int)$id,
      'foto' => $fotoBaru
    ]);
    exit;
  } catch (Exception $e) {
    if (!empty($fotoBaru) && $fotoBaru !== $fotoLama) {
      $newPath = $uploadDir . basename($fotoBaru);
      if (is_file($newPath)) {
        @unlink($newPath);
      }
    }
    json_error('Gagal menyimpan data dosen.');
  }
}

/* LOAD DETAIL */
if ($action === 'list') {

  $type = $_GET['type'] ?? '';
  $dosen_id = (int)($_GET['dosen_id'] ?? 0);

  if (!isset($tables[$type]) || $dosen_id <= 0) {
    json_error('Parameter tidak valid.');
  }

  $table = $tables[$type];

  $stmt = $pdo->prepare("
        SELECT id, nilai
        FROM $table
        WHERE dosen_id=?
        ORDER BY id ASC
    ");

  $stmt->execute([$dosen_id]);

  json_ok($stmt->fetchAll());
}

/* SIMPAN / REPLACE SEMUA DETAIL */
if ($action === 'save') {

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method tidak diizinkan.');
  }

  $input = json_decode(file_get_contents('php://input'), true);

  $type = $input['type'] ?? '';
  $dosen_id = (int)($input['dosen_id'] ?? 0);
  $nilai = $input['nilai'] ?? [];

  if (!isset($tables[$type])) {
    json_error('Jenis data tidak valid.');
  }

  if ($dosen_id <= 0) {
    json_error('ID dosen tidak valid.');
  }

  if (!is_array($nilai)) {
    $nilai = [];
  }

  $table = $tables[$type];

  /*
     * Hapus data lama kemudian masukkan data terbaru.
     * Dengan cara ini edit banyak data menjadi sederhana.
     */
  $pdo->beginTransaction();

  try {

    $stmt = $pdo->prepare("DELETE FROM $table WHERE dosen_id=?");
    $stmt->execute([$dosen_id]);

    $stmt = $pdo->prepare("
            INSERT INTO $table (dosen_id, nilai)
            VALUES (?, ?)
        ");

    foreach ($nilai as $item) {

      $item = trim($item);

      if ($item === '') {
        continue;
      }

      $stmt->execute([
        $dosen_id,
        $item
      ]);
    }

    $pdo->commit();

    json_ok();
  } catch (Exception $e) {

    $pdo->rollBack();
    json_error('Gagal menyimpan data tambahan.');
  }
}

/* HAPUS SATU DETAIL */
if ($action === 'delete') {

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method tidak diizinkan.');
  }

  $input = json_decode(file_get_contents('php://input'), true);

  $type = $input['type'] ?? '';
  $id = (int)($input['id'] ?? 0);

  if (!isset($tables[$type]) || $id <= 0) {
    json_error('Parameter tidak valid.');
  }

  $table = $tables[$type];

  $stmt = $pdo->prepare("DELETE FROM $table WHERE id=?");
  $stmt->execute([$id]);

  json_ok();
}

json_error('Action tidak ditemukan.');
