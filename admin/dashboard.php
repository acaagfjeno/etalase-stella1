<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['admin', 'super_admin']);

$jml_projek = db_satu("SELECT COUNT(*) AS n FROM projek")['n'];
$jml_siswa  = db_satu("SELECT COUNT(*) AS n FROM users WHERE role = 'siswa'")['n'];
$jml_admin  = db_satu("SELECT COUNT(*) AS n FROM users WHERE role = 'admin'")['n'];
$terbaru = db_semua(
    "SELECT p.id, p.judul, p.created_at, u.nama
       FROM projek p JOIN users u ON u.id = p.user_id
      ORDER BY p.created_at DESC LIMIT 5"
);

$judul_halaman = 'Dashboard';
$css_halaman = ['dashboard', 'tabel'];
require __DIR__ . '/../includes/header.php';
?>
<div class="judul-halaman"><h1>Dashboard</h1></div>

<div class="statistik">
    <div class="stat"><strong><?= (int)$jml_projek ?></strong><span>Projek</span></div>
    <div class="stat"><strong><?= (int)$jml_siswa ?></strong><span>Siswa</span></div>
    <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
    <div class="stat"><strong><?= (int)$jml_admin ?></strong><span>Admin</span></div>
    <?php endif; ?>
</div>

<h2>Projek terbaru</h2>
<?php if (!$terbaru): ?>
    <div class="kosong"><p>Belum ada projek yang masuk.</p></div>
<?php else: ?>
<div class="tabel-bungkus">
<table class="tabel">
    <thead><tr><th>Judul</th><th>Pembuat</th><th>Ditambahkan</th></tr></thead>
    <tbody>
    <?php foreach ($terbaru as $p): ?>
        <tr>
            <td><a href="<?= BASE_URL ?>/siswa/detail.php?id=<?= (int)$p['id'] ?>"><?= e($p['judul']) ?></a></td>
            <td><?= e($p['nama']) ?></td>
            <td><?= e(tanggal_id($p['created_at'])) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>