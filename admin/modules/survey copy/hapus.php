<?php

require_once __DIR__ . '/../config/auth.php';
wajib_login();

require_once __DIR__ . '/../config/database.php';

$id = (int)($_POST['id'] ?? 0);

if ($id < 1) {
  header(
    'Location: index.php?err=' .
      rawurlencode('ID survey tidak valid.')
  );
  exit;
}

try {

  $stmt = $pdo->prepare(
    "DELETE FROM survey WHERE id = ?"
  );

  $stmt->execute([$id]);

  header(
    'Location: index.php?ok=' .
      rawurlencode('Survey berhasil dihapus.')
  );

  exit;
} catch (Throwable $e) {

  header(
    'Location: index.php?err=' .
      rawurlencode($e->getMessage())
  );

  exit;
}
