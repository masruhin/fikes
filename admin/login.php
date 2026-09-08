<?php
require_once __DIR__ . '/config/auth.php';

$project_url = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/admin/'));

if (is_login()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = md5($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && hash_equals($user['password'], $password)) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_nama'] = $user['nama'];
        $_SESSION['admin_role'] = $user['role'];
        header('Location: index.php');
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin FIKES</title>
<link rel="stylesheet" href="<?= $project_url ?>/admin/assets/css/admin.css">
</head>
<body class="login-page">
<div class="login-card">
    <div class="brand-mark">F</div>
    <h1>Admin FIKES</h1>
    <p>Fakultas Ilmu Kesehatan</p>
    <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required>
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
        <button class="btn primary full">Masuk ke Dashboard</button>
    </form>
    <small>Demo: admin / admin123</small>
</div>
</body>
</html>
