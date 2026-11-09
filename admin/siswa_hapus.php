<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['admin', 'super_admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/siswa.php');
}
csrf_cek();

$id = (int)($_POST['id'] ?? 0);
// Hanya akun ber-role siswa yang bisa dihapus lewat halaman ini.
$siswa = db_satu("SELECT id FROM users WHERE id = ? AND role = 'siswa'", "i", [$id]);

if (!$siswa) {
    flash('Siswa tidak ditemukan.', 'error');
} else {
    hapus_gambar_milik($id);   // hapus file gambar; baris projek ikut terhapus (ON DELETE CASCADE)
    db_jalankan("DELETE FROM users WHERE id = ? AND role = 'siswa'", "i", [$id]);
    flash('Siswa beserta projeknya berhasil dihapus.');
}
redirect(BASE_URL . '/admin/siswa.php');
