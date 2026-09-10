<?php


$dir = __DIR__ . "/../../uploads/upload-logo/";
if (!is_dir($dir)) die("Folder upload-logo tidak ditemukan.");

$zip_file = tempnam(sys_get_temp_dir(), "fikes_logo_");
$zip = new ZipArchive();

if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
  die("Gagal membuat ZIP.");
}

$jumlah = 0;
foreach (scandir($dir) as $file) {
  if ($file == "." || $file == ".." || !is_file($dir . $file)) continue;
  $zip->addFile($dir . $file, $file);
  $jumlah++;
}
$zip->close();

if ($jumlah == 0) {
  unlink($zip_file);
  die("Belum ada logo untuk didownload.");
}

$nama_zip = "semua-logo-fikes-" . date("YmdHis") . ".zip";
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename='$nama_zip'");
header("Content-Length: " . filesize($zip_file));
readfile($zip_file);
unlink($zip_file);
exit;
