<?php
require __DIR__ . '/../config/helper.php';
wajib_login();

$id = (int)($_GET['id'] ?? 0);
$p = db_satu(
    "SELECT p.*, u.nama, u.kelas
       FROM projek p JOIN users u ON u.id = p.user_id
      WHERE p.id = ?", "i", [$id]
);
if (!$p) {
    http_response_code(404);
    flash('Projek tidak ditemukan.', 'error');
    redirect(BASE_URL . '/siswa/index.php');
}

$judul_halaman = $p['judul'];
$css_halaman = ['detail'];
require __DIR__ . '/../includes/header.php';
?>
<article class="detail">
    <img class="detail-gambar" src="<?= BASE_URL ?>/assets/uploads/<?= e($p['gambar']) ?>" alt="Gambar projek <?= e($p['judul']) ?>">
    <div class="detail-isi">
        <h1><?= e($p['judul']) ?></h1>
        <p class="meta">
            oleh <?= e($p['nama']) ?><?= $p['kelas'] ? ' (' . e($p['kelas']) . ')' : '' ?>
            &middot; ditambahkan <?= e(tanggal_id($p['created_at'])) ?>
        </p>
        <p class="deskripsi"><?= nl2br(e($p['deskripsi'])) ?></p>

        <div class="aksi-form">
            <a class="tombol" href="<?= e($p['link_pendukung']) ?>" target="_blank" rel="noopener noreferrer">Buka link pendukung</a>
            <?php if (!empty($p['link_demo'])): ?>
                <a class="tombol tombol-garis" href="<?= e($p['link_demo']) ?>" target="_blank" rel="noopener noreferrer">Lihat demo</a>
            <?php endif; ?>
        </div>

        <?php if (boleh_kelola_projek($p)): ?>
        <div class="aksi-form pemisah">
            <a class="tombol tombol-kecil tombol-garis" href="<?= BASE_URL ?>/siswa/edit.php?id=<?= (int)$p['id'] ?>">Edit projek</a>
            <?= form_hapus_projek($p['id'], 'etalase') ?>
        </div>
        <?php endif; ?>
    </div>
</article>
<p><a href="<?= BASE_URL ?>/siswa/index.php">&larr; Kembali ke etalase</a></p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
