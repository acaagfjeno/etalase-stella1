<?php
require __DIR__ . '/../config/helper.php';
wajib_login();

$id = (int)($_GET['id'] ?? 0);
$projek = db_satu("SELECT * FROM projek WHERE id = ?", "i", [$id]);

if (!$projek) {
    flash('Projek tidak ditemukan.', 'error');
    redirect(BASE_URL . '/siswa/index.php');
}
// Hak akses: siswa hanya boleh mengedit projek miliknya sendiri.
if (!boleh_kelola_projek($projek)) {
    http_response_code(403);
    flash('Kamu hanya bisa mengedit projek milikmu sendiri.', 'error');
    redirect(BASE_URL . '/siswa/index.php');
}

$errors = [];
$data = $projek;
$data['link_demo'] = (string)$projek['link_demo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $data = validasi_projek($_POST, $errors) + ['gambar' => $projek['gambar']];

    $gambar_baru = null;
    $ada_file = isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE;
    if ($ada_file) {
        $err = '';
        $gambar_baru = upload_gambar($_FILES['gambar'], $err);
        if ($gambar_baru === null) $errors['gambar'] = $err;
    }

    if (!$errors) {
        $demo = $data['link_demo'] !== '' ? $data['link_demo'] : null;
        $gambar_final = $gambar_baru ?? $projek['gambar'];
        db_jalankan(
            "UPDATE projek SET judul = ?, deskripsi = ?, gambar = ?, link_pendukung = ?, link_demo = ? WHERE id = ?",
            "sssssi",
            [$data['judul'], $data['deskripsi'], $gambar_final, $data['link_pendukung'], $demo, $id]
        );
        if ($gambar_baru) hapus_gambar($projek['gambar']);
        flash('Projek berhasil diperbarui.');
        redirect(BASE_URL . '/siswa/detail.php?id=' . $id);
    } elseif ($gambar_baru) {
        hapus_gambar($gambar_baru);
    }
    $data['gambar'] = $projek['gambar'];
}

$mode = 'edit';
$judul_halaman = 'Edit projek';
$css_halaman = ['form'];
require __DIR__ . '/../includes/header.php';
?>
<div class="edit-projek-halaman">
    <div class="judul-halaman"><h1>Edit projek</h1></div>
    <?php require __DIR__ . '/../includes/form_projek.php'; ?>
    <p class="label-kredit">Dari aku, Annisa Afidah Syaqib</p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
