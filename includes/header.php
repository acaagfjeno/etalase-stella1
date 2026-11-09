<?php
// Variabel opsional: $judul_halaman, $css_halaman (daftar CSS tambahan khusus halaman)
$u = user_login();
$judul_halaman = $judul_halaman ?? 'Etalase Digital Stella';
$versi_css = static function ($nama_file) {
    $path = __DIR__ . '/../assets/css/' . $nama_file;
    clearstatcache(true, $path);
    return filemtime($path) . '-' . filesize($path);
};
$css_base_versi = $versi_css('base.css');
$css_layout_versi = $versi_css('layout.css');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman) ?> | Etalase Digital Stella</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css?v=<?= $css_base_versi ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/layout.css?v=<?= $css_layout_versi ?>">
<?php foreach (($css_halaman ?? []) as $css): ?>
<?php $css_versi = $versi_css($css . '.css'); ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= e($css) ?>.css?v=<?= $css_versi ?>">
<?php endforeach; ?>
</head>
<body>
<header class="topbar">
    <div class="topbar-isi">
        <a class="logo" href="<?= $u ? beranda_role($u['role']) : BASE_URL . '/index.php' ?>">
            <span class="logo-mark"><img src="<?= BASE_URL ?>/assets/img/Logo%20%20Telkom.jpg" alt=""></span>
            <span class="logo-teks">
                <strong>SMK Telkom Lampung</strong>
                <small>Etalase Digital Stella</small>
            </span>
        </a>
        <?php if ($u): ?>
        <nav class="menu">
            <a href="<?= BASE_URL ?>/siswa/index.php">Etalase</a>
            <a href="<?= BASE_URL ?>/siswa/projek_saya.php">Projek saya</a>
            <a href="<?= BASE_URL ?>/siswa/tambah.php">Tambah projek</a>
            <?php if (is_admin()): ?>
                <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                <a href="<?= BASE_URL ?>/admin/projek.php">Kelola projek</a>
                <a href="<?= BASE_URL ?>/admin/siswa.php">Kelola siswa</a>
            <?php endif; ?>
            <?php if ($u['role'] === 'super_admin'): ?>
                <a href="<?= BASE_URL ?>/superadmin/admin.php">Kelola admin</a>
            <?php endif; ?>
        </nav>
        <div class="akun">
            <a class="akun-profil" href="<?= BASE_URL ?>/profil.php" aria-label="Buka profil <?= e($u['nama']) ?>">
                <?php if (!empty($u['foto_profil'])): ?>
                    <img src="<?= BASE_URL ?>/assets/uploads/<?= e($u['foto_profil']) ?>" alt="">
                <?php else: ?>
                    <span class="akun-avatar-fallback" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($u['nama'], 0, 1))) ?></span>
                <?php endif; ?>
                <span class="akun-nama"><?= e($u['nama']) ?> <small>(<?= e(label_role($u['role'])) ?>)</small></span>
            </a>
            <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/auth/logout.php">Keluar</a>
        </div>
        <?php endif; ?>
    </div>
</header>
<main class="konten">
<?php tampil_flash(); ?>
