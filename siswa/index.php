<?php
require __DIR__ . '/../config/helper.php';
wajib_login();

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$daftar = db_semua(
    "SELECT p.id, p.judul, p.deskripsi, p.gambar, p.created_at, u.nama
       FROM projek p JOIN users u ON u.id = p.user_id
      WHERE p.judul LIKE ? OR u.nama LIKE ?
      ORDER BY p.created_at DESC",
    "ss", [$like, $like]
);

$judul_halaman = 'Etalase';
$css_halaman = ['etalase'];
require __DIR__ . '/../includes/header.php';
?>
<section class="etalase-intro" aria-labelledby="judul-etalase">
    <div class="etalase-heading">
        <p class="etalase-label">ETALASE DIGITAL STELLA</p>
        <h1 id="judul-etalase">LIHAT <span>KARYA</span></h1>
        <p>Jelajahi ide dan karya kreatif siswa. Temukan inspirasi dari setiap projek.</p>
        <a class="etalase-jelajah" href="#daftar-karya">Jelajahi etalase <span aria-hidden="true">↓</span></a>
    </div>
    <form method="get" class="cari" role="search">
        <div class="cari-keterangan">
            <p class="cari-label">TEMUKAN KARYA FAVORITMU</p>
            <label for="cari-karya">Cari berdasarkan judul atau nama siswa</label>
        </div>
        <div class="cari-baris">
            <div class="cari-input">
                <input id="cari-karya" type="text" name="q" value="<?= e($q) ?>" placeholder="Contoh: aplikasi, desain, atau nama siswa" aria-describedby="cari-bantuan">
            </div>
            <button type="submit" class="tombol">
                <span>Cari karya</span>
                <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                    <path d="M3.5 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <small id="cari-bantuan"><?= count($daftar) ?> karya tersedia untuk dijelajahi</small>
    </form>
</section>

<?php if (!$daftar): ?>
    <section class="kosong etalase-kosong" id="daftar-karya">
        <p class="etalase-kosong-label"><?= $q !== '' ? 'COBA KATA KUNCI LAIN' : 'RUANG UNTUK KARYA BARU' ?></p>
        <h2><?= $q !== '' ? 'Karya belum ditemukan' : 'Etalase masih menunggu karya' ?></h2>
        <p><?= $q !== '' ? 'Belum ada karya yang cocok dengan “' . e($q) . '”. Coba kata kunci lain atau lihat semua karya.' : 'Jadilah yang pertama memajang karya dan berbagi inspirasi dengan teman-teman.' ?></p>
        <?php if ($q !== ''): ?>
            <a class="tombol tombol-garis" href="<?= BASE_URL ?>/siswa/index.php">Lihat semua karya</a>
        <?php else: ?>
            <a class="tombol" href="<?= BASE_URL ?>/siswa/tambah.php">Tambah karya pertama</a>
        <?php endif; ?>
    </section>
<?php else: ?>
<div class="etalase-hasil" id="daftar-karya" aria-live="polite">
    <div>
        <p class="etalase-label"><?= $q !== '' ? 'HASIL PENCARIAN' : 'JELAJAHI ETALASE' ?></p>
        <h2><?= $q !== '' ? 'Karya yang cocok' : 'Karya terbaru' ?></h2>
    </div>
    <span class="jumlah-hasil"><?= count($daftar) ?> karya</span>
</div>
<div class="grid">
    <?php foreach ($daftar as $nomor => $p): ?>
    <a class="kartu" href="<?= BASE_URL ?>/siswa/detail.php?id=<?= (int)$p['id'] ?>">
        <div class="kartu-gambar">
            <img src="<?= BASE_URL ?>/assets/uploads/<?= e($p['gambar']) ?>" alt="Gambar projek <?= e($p['judul']) ?>" loading="lazy">
            <span class="kartu-nomor"><?= sprintf('%02d', $nomor + 1) ?> / ETALASE</span>
            <span class="kartu-panah" aria-hidden="true">
                <svg viewBox="0 0 20 20" fill="none">
                    <path d="M5 15 15 5M6 5h9v9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
        <div class="kartu-isi">
            <h2><?= e($p['judul']) ?></h2>
            <p><?= e(mb_strimwidth($p['deskripsi'], 0, 90, '...')) ?></p>
            <small><span>oleh <?= e($p['nama']) ?></span><span><?= e(tanggal_id($p['created_at'])) ?></span></small>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
