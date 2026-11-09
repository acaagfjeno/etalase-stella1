<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['super_admin']);

$role_target = 'admin';
$nama_peran  = 'Admin';
$id          = (int)($_GET['id'] ?? 0);
$url_daftar  = BASE_URL . '/superadmin/admin.php';

require __DIR__ . '/../includes/proses_pengguna.php';

$judul_halaman = $mode === 'edit' ? 'Edit admin' : 'Tambah admin';
$css_halaman = ['form'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman"><h1><?= e($judul_halaman) ?></h1></div>
<?php require __DIR__ . '/../includes/form_pengguna.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
