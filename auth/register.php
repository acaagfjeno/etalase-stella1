<?php
require __DIR__ . '/../config/helper.php';

if (user_login()) {
    redirect(beranda_role($_SESSION['user']['role']));
}

$errors = [];
$data = ['nama' => '', 'username' => '', 'kelas' => ''];
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $data['nama'] = trim($_POST['nama'] ?? '');
    $data['username'] = trim($_POST['username'] ?? '');
    $data['kelas'] = trim($_POST['kelas'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) {
        $errors['nama'] = 'Nama wajib diisi (maksimal 100 karakter).';
    }
    if (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $data['username'])) {
        $errors['username'] = 'Username 3-50 karakter: huruf, angka, titik, atau garis bawah.';
    } elseif (db_satu("SELECT id FROM users WHERE username = ?", "s", [$data['username']])) {
        $errors['username'] = 'Username sudah dipakai.';
    }
    if (mb_strlen($data['kelas']) > 20) {
        $errors['kelas'] = 'Kelas maksimal 20 karakter.';
    }
    if (mb_strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $errors['konfirmasi_password'] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $kelas = $data['kelas'] !== '' ? $data['kelas'] : null;
        db_jalankan(
            "INSERT INTO users (nama, username, password, role, kelas) VALUES (?, ?, ?, 'siswa', ?)",
            "ssss",
            [$data['nama'], $data['username'], password_hash($password, PASSWORD_DEFAULT), $kelas]
        );
        $sukses = true;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Akun | Etalase Digital Stella</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/login.css">
</head>
<body class="halaman-daftar">
    <main class="login">
        <div class="login-brand">
            <img src="<?= BASE_URL ?>/assets/img/Logo%20%20Telkom.jpg" alt="">
            <span><strong>SMK Telkom Lampung</strong><small>Etalase Digital Stella</small></span>
        </div>
        <h1>Buat akun</h1>
        <p class="sub">Daftar untuk mulai memajang karya di etalase.</p>

        <?php if ($sukses): ?>
            <p class="berhasil" role="status">Akun berhasil dibuat. Silakan masuk dengan username dan password kamu.</p>
            <a class="login-tombol" href="<?= BASE_URL ?>/auth/login.php">Ke halaman masuk</a>
        <?php else: ?>
            <?php if ($errors): ?>
                <div class="galat" role="alert">
                    <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <?= csrf_field() ?>
                <label for="nama">Nama lengkap</label>
                <input type="text" id="nama" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" autocomplete="name" required autofocus>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="50" value="<?= e($data['username']) ?>" autocomplete="username" required>

                <label for="kelas">Kelas <span class="opsional">(opsional)</span></label>
                <input type="text" id="kelas" name="kelas" maxlength="20" placeholder="XI RPL 2" value="<?= e($data['kelas']) ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" autocomplete="new-password" required>
                <small class="bantuan">Minimal 6 karakter.</small>

                <label for="konfirmasi_password">Konfirmasi password</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" minlength="6" autocomplete="new-password" required>

                <button type="submit">Buat akun</button>
            </form>
            <p class="login-link">Sudah punya akun? <a href="<?= BASE_URL ?>/auth/login.php">Masuk</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
