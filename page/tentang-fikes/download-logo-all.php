<?php
$dir = __DIR__ . "/../../admin/uploads/upload-logo/";
$allowed = ["jpg","jpeg","png","gif","svg","webp","ico","bmp"];
$files = [];

if (!is_dir($dir)) die("Folder logo tidak ditemukan.");

foreach (scandir($dir) as $f) {
    if ($f == "." || $f == ".." || !is_file($dir.$f)) continue;
    if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allowed)) $files[] = $f;
}

if (!$files) die("Belum ada file logo.");

$tmp = tempnam(sys_get_temp_dir(), "fikes_logo_");
$zip = new ZipArchive();

if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) die("Gagal membuat ZIP.");

foreach ($files as $f) $zip->addFile($dir.$f, $f);
$zip->close();

$name = "Logo-FIKES-Lengkap-" . date("YmdHis") . ".zip";
header("Content-Type: application/zip");
header('Content-Disposition: attachment; filename="'.$name.'"');
header("Content-Length: ".filesize($tmp));
readfile($tmp);
unlink($tmp);
exit;
?>