<?php
require __DIR__ . '/../config/helper.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/siswa/index.php');
}
csrf_cek();

$id = (int)($_POST['id'] ?? 0);
$projek = db_satu("SELECT * FROM projek WHERE id = ?", "i", [$id]);

// Tujuan setelah hapus (daftar putih supaya tidak bisa diarahkan ke sembarang URL)
$tujuan = [
    'saya'   => BASE_URL . '/siswa/projek_saya.php',
    'admin'  => BASE_URL . '/admin/projek.php',
    'etalase'=> BASE_URL . '/siswa/index.php',
];
$kembali = $tujuan[$_POST['kembali'] ?? ''] ?? $tujuan['etalase'];

if (!$projek) {
    flash('Projek tidak ditemukan.', 'error');
    redirect($kembali);
}
if (!boleh_kelola_projek($projek)) {
    http_response_code(403);
    flash('Kamu hanya bisa menghapus projek milikmu sendiri.', 'error');
    redirect($kembali);
}

hapus_gambar($projek['gambar']);
db_jalankan("DELETE FROM projek WHERE id = ?", "i", [$id]);
flash('Projek berhasil dihapus.');
redirect($kembali);
