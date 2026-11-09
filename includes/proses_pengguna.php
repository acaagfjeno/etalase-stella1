<?php
// Logika tambah/edit pengguna, dipakai admin/siswa_form.php dan superadmin/admin_form.php.
// Variabel yang harus ada: $role_target ('siswa' | 'admin'), $id (0 = tambah), $url_daftar, $nama_peran
$mode = $id ? 'edit' : 'tambah';
$errors = [];
$data = ['nama' => '', 'username' => '', 'kelas' => ''];

if ($id) {
    // Filter role: admin tidak bisa membuka akun admin/super admin lewat halaman siswa.
    $lama = db_satu("SELECT id, nama, username, kelas FROM users WHERE id = ? AND role = ?", "is", [$id, $role_target]);
    if (!$lama) {
        flash($nama_peran . ' tidak ditemukan.', 'error');
        redirect($url_daftar);
    }
    $data = ['nama' => $lama['nama'], 'username' => $lama['username'], 'kelas' => (string)$lama['kelas']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $data['nama']     = trim($_POST['nama'] ?? '');
    $data['username'] = trim($_POST['username'] ?? '');
    $data['kelas']    = $role_target === 'siswa' ? trim($_POST['kelas'] ?? '') : '';
    $password         = $_POST['password'] ?? '';

    if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) {
        $errors['nama'] = 'Nama wajib diisi (maksimal 100 karakter).';
    }
    if (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $data['username'])) {
        $errors['username'] = 'Username 3-50 karakter: huruf, angka, titik, atau garis bawah.';
    } else {
        $sama = db_satu("SELECT id FROM users WHERE username = ? AND id <> ?", "si", [$data['username'], $id]);
        if ($sama) $errors['username'] = 'Username sudah dipakai.';
    }
    if ($mode === 'tambah' && $password === '') {
        $errors['password'] = 'Password wajib diisi.';
    } elseif ($password !== '' && mb_strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }
    if (mb_strlen($data['kelas']) > 20) {
        $errors['kelas'] = 'Kelas maksimal 20 karakter.';
    }

    if (!$errors) {
        $kelas = $data['kelas'] !== '' ? $data['kelas'] : null;
        if ($mode === 'tambah') {
            db_jalankan(
                "INSERT INTO users (nama, username, password, role, kelas) VALUES (?, ?, ?, ?, ?)",
                "sssss",
                [$data['nama'], $data['username'], password_hash($password, PASSWORD_DEFAULT), $role_target, $kelas]
            );
            flash($nama_peran . ' berhasil ditambahkan.');
        } elseif ($password !== '') {
            db_jalankan(
                "UPDATE users SET nama = ?, username = ?, kelas = ?, password = ? WHERE id = ? AND role = ?",
                "ssssis",
                [$data['nama'], $data['username'], $kelas, password_hash($password, PASSWORD_DEFAULT), $id, $role_target]
            );
            flash($nama_peran . ' berhasil diperbarui (password diganti).');
        } else {
            db_jalankan(
                "UPDATE users SET nama = ?, username = ?, kelas = ? WHERE id = ? AND role = ?",
                "sssis",
                [$data['nama'], $data['username'], $kelas, $id, $role_target]
            );
            flash($nama_peran . ' berhasil diperbarui.');
        }
        redirect($url_daftar);
    }
}
