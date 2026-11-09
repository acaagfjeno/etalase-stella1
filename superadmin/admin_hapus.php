<?php
require __DIR__ . '/../config/helper.php';
wajib_role(['super_admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/superadmin/admin.php');
}
csrf_cek();

$id = (int)($_POST['id'] ?? 0);
$admin = db_satu("SELECT id FROM users WHERE id = ? AND role = 'admin'", "i", [$id]);

if (!$admin) {
    flash('Admin tidak ditemukan.', 'error');
} else {
    hapus_gambar_milik($id);
    db_jalankan("DELETE FROM users WHERE id = ? AND role = 'admin'", "i", [$id]);
    flash('Akun admin berhasil dihapus.');
}
redirect(BASE_URL . '/superadmin/admin.php');
