<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['admin', 'super_admin']);

$daftar = db_semua(
    "SELECT u.id, u.nama, u.username, u.kelas, COUNT(p.id) AS jml_projek
       FROM users u LEFT JOIN projek p ON p.user_id = u.id
      WHERE u.role = 'siswa'
      GROUP BY u.id, u.nama, u.username, u.kelas
      ORDER BY u.nama"
);

$judul_halaman = 'Kelola siswa';
$css_halaman = ['tabel'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman">
    <h1>Kelola siswa</h1>
    <a class="tombol" href="<?= BASE_URL ?>/admin/siswa_form.php">Tambah siswa</a>
</div>

<?php if (!$daftar): ?>
    <div class="kosong"><p>Belum ada data siswa. Tambahkan siswa agar mereka bisa masuk.</p></div>
<?php else: ?>
<div class="tabel-bungkus">
<table class="tabel">
    <thead><tr><th>Nama</th><th>Username</th><th>Kelas</th><th>Projek</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $s): ?>
        <tr>
            <td><?= e($s['nama']) ?></td>
            <td><?= e($s['username']) ?></td>
            <td><?= e($s['kelas'] ?: '-') ?></td>
            <td><?= (int)$s['jml_projek'] ?></td>
            <td class="aksi">
                <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/admin/siswa_form.php?id=<?= (int)$s['id'] ?>">Edit</a>
                <form method="post" action="<?= BASE_URL ?>/admin/siswa_hapus.php" class="form-inline"
                      data-konfirm="Hapus siswa ini beserta semua projeknya?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
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
