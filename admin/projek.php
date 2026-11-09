<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['admin', 'super_admin']);

$daftar = db_semua(
    "SELECT p.id, p.judul, p.gambar, p.created_at, u.nama
       FROM projek p JOIN users u ON u.id = p.user_id
      ORDER BY p.created_at DESC"
);

$judul_halaman = 'Kelola projek';
$css_halaman = ['tabel'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman">
    <h1>Kelola projek</h1>
    <a class="tombol" href="<?= BASE_URL ?>/siswa/tambah.php">Tambah karya</a>
</div>

<?php if (!$daftar): ?>
    <div class="kosong"><p>Belum ada projek. Tambahkan karya pertama.</p></div>
<?php else: ?>
<div class="tabel-bungkus">
<table class="tabel">
    <thead><tr><th>Gambar</th><th>Judul</th><th>Pembuat</th><th>Ditambahkan</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $p): ?>
        <tr>
            <td><img class="thumb" src="<?= BASE_URL ?>/assets/uploads/<?= e($p['gambar']) ?>" alt=""></td>
            <td><?= e($p['judul']) ?></td>
            <td><?= e($p['nama']) ?></td>
            <td><?= e(tanggal_id($p['created_at'])) ?></td>
            <td class="aksi">
                <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/siswa/detail.php?id=<?= (int)$p['id'] ?>">Lihat</a>
                <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/siswa/edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
                <?= form_hapus_projek($p['id'], 'admin') ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
