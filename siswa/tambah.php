<?php
require __DIR__ . '/../config/helper.php';
wajib_login();   // siswa, admin, dan super admin boleh menambah projek

$errors = [];
$data = ['judul' => '', 'deskripsi' => '', 'link_pendukung' => '', 'link_demo' => '', 'gambar' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $data = validasi_projek($_POST, $errors) + ['gambar' => ''];

    $nama_gambar = null;
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['gambar'] = 'Gambar projek wajib diunggah.';
    } else {
        $err = '';
        $nama_gambar = upload_gambar($_FILES['gambar'], $err);
        if ($nama_gambar === null) $errors['gambar'] = $err;
    }

    if (!$errors) {
        $demo = $data['link_demo'] !== '' ? $data['link_demo'] : null;
        db_jalankan(
            "INSERT INTO projek (user_id, judul, deskripsi, gambar, link_pendukung, link_demo) VALUES (?, ?, ?, ?, ?, ?)",
            "isssss",
            [$_SESSION['user']['id'], $data['judul'], $data['deskripsi'], $nama_gambar, $data['link_pendukung'], $demo]
        );
        flash('Projek berhasil ditambahkan.');
        redirect(BASE_URL . '/siswa/' . (is_admin() ? 'index.php' : 'projek_saya.php'));
    } elseif ($nama_gambar) {
        hapus_gambar($nama_gambar); // form gagal, jangan tinggalkan file yatim
    }
}

$mode = 'tambah';
$judul_halaman = 'Tambah projek';
$css_halaman = ['form'];
require __DIR__ . '/../includes/header.php';
?>
<div class="tambah-projek-halaman">
    <div class="judul-halaman"><h1>Tambah projek</h1></div>
    <?php require __DIR__ . '/../includes/form_projek.php'; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
