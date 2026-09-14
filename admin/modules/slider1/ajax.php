<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json; charset=utf-8');

$uploadDir = __DIR__ . '/../../uploads/slider/';
$uploadWeb = '../../uploads/slider/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
function out($success, $message = '', $data = [])
{
  echo json_encode(['success' => $success, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
  exit;
}
function safeName($name)
{
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  return date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
}
function uploadGambar($file, $dir)
{
  if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
  if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload gambar gagal.');
  if ($file['size'] > 5 * 1024 * 1024) throw new Exception('Ukuran gambar maksimal 5 MB.');
  $allowed = ['jpg', 'jpeg', 'png', 'webp'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed, true)) throw new Exception('Format gambar harus JPG, JPEG, PNG, atau WEBP.');
  $info = @getimagesize($file['tmp_name']);
  if (!$info) throw new Exception('File bukan gambar yang valid.');
  $name = safeName($file['name']);
  $target = $dir . $name;
  if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception('Gambar gagal disimpan.');
  return $name;
}
$action = $_GET['action'] ?? $_POST['action'] ?? '';
try {
  if ($action === 'list') {
    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $sql = "SELECT * FROM slider_beranda WHERE 1=1";
    $p = [];
    if ($search !== '') {
      $sql .= " AND (judul LIKE :s1 OR label LIKE :s2)";
      $p[':s1'] = "%$search%";
      $p[':s2'] = "%$search%";
    }
    if ($status !== '') {
      $sql .= " AND status=:st";
      $p[':st'] = $status;
    }
    $sql .= " ORDER BY nomor_urut ASC,id DESC";
    $q = $pdo->prepare($sql);
    $q->execute($p);
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) {
      $r['gambar_url'] = $uploadWeb . rawurlencode(basename($r['gambar']));
    }
    out(true, '', $rows);
  }
  if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    $q = $pdo->prepare('SELECT * FROM slider_beranda WHERE id=?');
    $q->execute([$id]);
    $r = $q->fetch(PDO::FETCH_ASSOC);
    if (!$r) out(false, 'Slider tidak ditemukan.');
    $r['gambar_url'] = $uploadWeb . rawurlencode(basename($r['gambar']));
    out(true, '', $r);
  }
  if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    if ($judul === '') out(false, 'Judul slider wajib diisi.');
    $fields = ['highlight', 'label', 'deskripsi', 'link_utama', 'teks_tombol_utama', 'link_kedua', 'teks_tombol_kedua'];
    foreach ($fields as $f) $$f = trim($_POST[$f] ?? '');
    $nomor = max(1, (int)($_POST['nomor_urut'] ?? 1));
    $status = ($_POST['status'] ?? 'aktif') === 'nonaktif' ? 'nonaktif' : 'aktif';
    $q = $pdo->prepare('SELECT gambar FROM slider_beranda WHERE id=?');
    $q->execute([$id]);
    $old = $q->fetchColumn();
    $new = null;
    if (isset($_FILES['gambar'])) $new = uploadGambar($_FILES['gambar'], $uploadDir);
    if ($id) {
      if (!$old) {
      }
      $sql = 'UPDATE slider_beranda SET judul=?,highlight=?,label=?,deskripsi=?,link_utama=?,teks_tombol_utama=?,link_kedua=?,teks_tombol_kedua=?,nomor_urut=?,status=?';
      $params = [$judul, $highlight, $label, $deskripsi, $link_utama, $teks_tombol_utama, $link_kedua, $teks_tombol_kedua, $nomor, $status];
      if ($new) {
        $sql .= ',gambar=?';
        $params[] = $new;
      }
      $sql .= ' WHERE id=?';
      $params[] = $id;
      $st = $pdo->prepare($sql);
      $st->execute($params);
      if ($new && $old && is_file($uploadDir . basename($old))) @unlink($uploadDir . basename($old));
      out(true, 'Data slider berhasil diperbarui.');
    } else {
      if (!$new) out(false, 'Gambar slider wajib diupload.');
      $st = $pdo->prepare('INSERT INTO slider_beranda (judul,highlight,label,deskripsi,gambar,link_utama,teks_tombol_utama,link_kedua,teks_tombol_kedua,nomor_urut,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
      $st->execute([$judul, $highlight, $label, $deskripsi, $new, $link_utama, $teks_tombol_utama, $link_kedua, $teks_tombol_kedua, $nomor, $status]);
      out(true, 'Slider berhasil ditambahkan.');
    }
  }
  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $q = $pdo->prepare('SELECT gambar FROM slider_beranda WHERE id=?');
    $q->execute([$id]);
    $old = $q->fetchColumn();
    if (!$old) out(false, 'Slider tidak ditemukan.');
    $pdo->prepare('DELETE FROM slider_beranda WHERE id=?')->execute([$id]);
    if (is_file($uploadDir . basename($old))) @unlink($uploadDir . basename($old));
    out(true, 'Slider berhasil dihapus.');
  }
  out(false, 'Aksi tidak dikenal.');
} catch (Throwable $e) {
  out(false, $e->getMessage());
}
