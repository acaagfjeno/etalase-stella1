<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['super_admin']);

$daftar = db_semua("SELECT id, nama, username, created_at FROM users WHERE role = 'admin' ORDER BY nama");

$judul_halaman = 'Kelola admin';
$css_halaman = ['tabel'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman">
    <h1>Kelola admin</h1>
    <a class="tombol" href="<?= BASE_URL ?>/superadmin/admin_form.php">Tambah admin</a>
</div>

<?php if (!$daftar): ?>
    <div class="kosong"><p>Belum ada akun admin.</p></div>
<?php else: ?>
<div class="tabel-bungkus">
<table class="tabel">
    <thead><tr><th>Nama</th><th>Username</th><th>Dibuat</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $a): ?>
        <tr>
            <td><?= e($a['nama']) ?></td>
            <td><?= e($a['username']) ?></td>
            <td><?= e(tanggal_id($a['created_at'])) ?></td>
            <td class="aksi">
                <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/superadmin/admin_form.php?id=<?= (int)$a['id'] ?>">Edit</a>
                <form method="post" action="<?= BASE_URL ?>/superadmin/admin_hapus.php" class="form-inline"
                      data-konfirm="Hapus akun admin ini beserta projek yang ia buat?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <button type="submit" class="tombol tombol-kecil tombol-bahaya">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
