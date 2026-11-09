<?php
require __DIR__ . '/../config/helper.php';
wajib_login();

$daftar = db_semua(
    "SELECT id, judul, gambar, created_at, updated_at FROM projek WHERE user_id = ? ORDER BY created_at DESC",
    "i", [$_SESSION['user']['id']]
);

$judul_halaman = 'Projek saya';
$css_halaman = ['tabel'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman">
    <h1>Projek saya</h1>
    <a class="tombol" href="<?= BASE_URL ?>/siswa/tambah.php">Tambah projek</a>
</div>

<?php if (!$daftar): ?>
    <div class="kosong"><p>Kamu belum punya projek. Tambahkan karya pertamamu.</p></div>
<?php else: ?>
<div class="tabel-bungkus">
<table class="tabel">
    <thead><tr><th>Gambar</th><th>Judul</th><th>Ditambahkan</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $p): ?>
        <tr>
            <td><img class="thumb" src="<?= BASE_URL ?>/assets/uploads/<?= e($p['gambar']) ?>" alt=""></td>
            <td><a href="<?= BASE_URL ?>/siswa/detail.php?id=<?= (int)$p['id'] ?>"><?= e($p['judul']) ?></a></td>
            <td><?= e(tanggal_id($p['created_at'])) ?></td>
            <td class="aksi">
                <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/siswa/edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
                <?= form_hapus_projek($p['id'], 'saya') ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
