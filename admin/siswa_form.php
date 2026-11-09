<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['admin', 'super_admin']);

$role_target = 'siswa';
$nama_peran  = 'Siswa';
$id          = (int)($_GET['id'] ?? 0);
$url_daftar  = BASE_URL . '/admin/siswa.php';

require __DIR__ . '/../includes/proses_pengguna.php';

$judul_halaman = $mode === 'edit' ? 'Edit siswa' : 'Tambah siswa';
$css_halaman = ['form'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman"><h1><?= e($judul_halaman) ?></h1></div>
<?php require __DIR__ . '/../includes/form_pengguna.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
