<?php
require __DIR__ . '/../config/helper.php';

if (user_login()) {
    redirect(beranda_role($_SESSION['user']['role']));
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $kolom_foto_profil = db_satu("SHOW COLUMNS FROM users LIKE 'foto_profil'");
    $kolom_foto = $kolom_foto_profil ? ', foto_profil' : '';
    $user = db_satu("SELECT id, nama, username, password, role{$kolom_foto} FROM users WHERE username = ?", "s", [$username]);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'       => (int)$user['id'],
            'nama'     => $user['nama'],
            'username' => $user['username'],
            'role'     => $user['role'],
            'foto_profil' => $user['foto_profil'] ?? null,
        ];
        redirect(beranda_role($user['role']));
    }
    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | Etalase Digital Stella</title>
    <!-- Halaman login hanya memakai login.css (berdiri sendiri, tidak memakai CSS halaman lain) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/login.css">
</head>
<body>
    <main class="login">
        <div class="login-brand">
            <img src="<?= BASE_URL ?>/assets/img/Logo%20%20Telkom.jpg" alt="">
            <span><strong>SMK Telkom Lampung</strong><small>Etalase Digital Stella</small></span>
        </div>
        <h1>Masuk ke Etalase</h1>
        <p class="sub">Lihat dan pajang karya siswa RPL.</p>

        <?php if ($error): ?><p class="galat" role="alert"><?= e($error) ?></p><?php endif; ?>

        <form method="post" autocomplete="off">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= e($username) ?>" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Masuk</button>
        </form>
        <p class="login-link">Belum punya akun? <a href="<?= BASE_URL ?>/auth/register.php">Buat akun</a></p>
    </main>
</body>
</html>
