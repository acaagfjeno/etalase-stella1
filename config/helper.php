<?php
// Fungsi bantu yang dipakai semua halaman.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/koneksi.php';

// Otomatis mengikuti nama folder proyek di htdocs (etalase-stella, etalase1-stella, dst).
define('BASE_URL', '/' . basename(dirname(__DIR__)));
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');

/* ---------- Umum ---------- */
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function flash($pesan, $tipe = 'ok') {
    $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
}

function tampil_flash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="notif notif-' . e($f['tipe']) . '">' . e($f['pesan']) . '</div>';
    }
}

/* ---------- Database (prepared statement) ---------- */
function db_persiapkan($sql, $tipe, $param) {
    global $koneksi;
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt === false) {
        throw new RuntimeException('Query database gagal disiapkan: ' . mysqli_error($koneksi));
    }
    if ($tipe !== '' && !mysqli_stmt_bind_param($stmt, $tipe, ...$param)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Parameter query database gagal dipasang: ' . $error);
    }
    return $stmt;
}

function db_semua($sql, $tipe = '', $param = []) {
    $stmt = db_persiapkan($sql, $tipe, $param);
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Query database gagal dijalankan: ' . $error);
    }
    $hasil = mysqli_stmt_get_result($stmt);
    $baris = $hasil ? mysqli_fetch_all($hasil, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $baris;
}

function db_satu($sql, $tipe = '', $param = []) {
    $baris = db_semua($sql, $tipe, $param);
    return $baris[0] ?? null;
}

function db_jalankan($sql, $tipe = '', $param = []) {
    $stmt = db_persiapkan($sql, $tipe, $param);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Query database gagal dijalankan: ' . $error);
    }
    mysqli_stmt_close($stmt);
    return $ok;
}

/* ---------- Login & hak akses ---------- */
function user_login() {
    return $_SESSION['user'] ?? null;
}

function wajib_login() {
    if (!user_login()) {
        redirect(BASE_URL . '/auth/login.php');
    }
}

// Contoh: wajib_role(['admin', 'super_admin']);
function wajib_role(array $boleh) {
    wajib_login();
    if (!in_array($_SESSION['user']['role'], $boleh, true)) {
        http_response_code(403);
        exit('<!doctype html><meta charset="utf-8"><body style="font-family:sans-serif;padding:40px">'
           . '<h2>403 - Akses ditolak</h2><p>Role kamu tidak punya izin untuk halaman ini.</p>'
           . '<a href="' . BASE_URL . '/index.php">Kembali</a></body>');
    }
}

function is_admin() {
    $u = user_login();
    return $u && in_array($u['role'], ['admin', 'super_admin'], true);
}

function beranda_role($role) {
    return ($role === 'siswa')
        ? BASE_URL . '/siswa/index.php'
        : BASE_URL . '/admin/dashboard.php';
}

function label_role($role) {
    return ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'siswa' => 'Siswa'][$role] ?? $role;
}

/* ---------- CSRF (untuk form POST) ---------- */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_cek() {
    $kirim = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $kirim)) {
        http_response_code(400);
        exit('Permintaan tidak valid (token salah). Muat ulang halaman lalu coba lagi.');
    }
}

/* ---------- Upload gambar ---------- */
function upload_gambar(array $file, &$error) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gambar gagal diunggah. Pilih file lain.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $error = 'Ukuran gambar maksimal 2 MB.';
        return null;
    }
    $info = @getimagesize($file['tmp_name']);
    $boleh = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($boleh[$info[2]])) {
        $error = 'Format gambar harus JPG, PNG, atau WEBP.';
        return null;
    }
    $nama = bin2hex(random_bytes(8)) . '.' . $boleh[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $nama)) {
        $error = 'Gambar tidak bisa disimpan. Cek izin folder assets/uploads.';
        return null;
    }
    return $nama;
}

function hapus_gambar($nama) {
    $path = UPLOAD_DIR . basename((string)$nama);
    if ($nama && is_file($path)) {
        unlink($path);
    }
}

// Hapus semua file gambar milik seorang user (dipanggil sebelum user dihapus)
function hapus_gambar_milik($user_id) {
    $daftar = db_semua("SELECT gambar FROM projek WHERE user_id = ?", "i", [$user_id]);
    foreach ($daftar as $p) {
        hapus_gambar($p['gambar']);
    }
}

function tanggal_id($waktu) {
    return date('d M Y', strtotime($waktu));
}

/* ---------- Projek ---------- */
function url_valid($url) {
    return preg_match('/^https?:\/\//i', $url) && filter_var($url, FILTER_VALIDATE_URL);
}

// Validasi input form projek. Isi $errors jika ada yang salah, kembalikan data bersih.
function validasi_projek(array $in, array &$errors) {
    $data = [
        'judul'          => trim($in['judul'] ?? ''),
        'deskripsi'      => trim($in['deskripsi'] ?? ''),
        'link_pendukung' => trim($in['link_pendukung'] ?? ''),
        'link_demo'      => trim($in['link_demo'] ?? ''),
    ];
    if ($data['judul'] === '') {
        $errors['judul'] = 'Judul projek wajib diisi.';
    } elseif (mb_strlen($data['judul']) > 150) {
        $errors['judul'] = 'Judul maksimal 150 karakter.';
    }
    if ($data['deskripsi'] === '') {
        $errors['deskripsi'] = 'Deskripsi wajib diisi.';
    }
    if ($data['link_pendukung'] === '') {
        $errors['link_pendukung'] = 'Link pendukung wajib diisi.';
    } elseif (!url_valid($data['link_pendukung'])) {
        $errors['link_pendukung'] = 'Link harus diawali http:// atau https://';
    }
    if ($data['link_demo'] !== '' && !url_valid($data['link_demo'])) {
        $errors['link_demo'] = 'Link demo harus diawali http:// atau https://';
    }
    return $data;
}

// Boleh edit/hapus? Admin & super admin: semua projek. Siswa: hanya miliknya.
function boleh_kelola_projek(array $projek) {
    $u = user_login();
    return $u && (is_admin() || (int)$projek['user_id'] === (int)$u['id']);
}

// Tombol hapus (form POST + konfirmasi lewat script.js)
function form_hapus_projek($id, $kembali, $kelas = 'tombol tombol-kecil tombol-bahaya') {
    return '<form method="post" action="' . BASE_URL . '/siswa/hapus.php" class="form-inline" data-konfirm="Hapus projek ini? Tindakan ini tidak bisa dibatalkan.">'
         . csrf_field()
         . '<input type="hidden" name="id" value="' . (int)$id . '">'
         . '<input type="hidden" name="kembali" value="' . e($kembali) . '">'
         . '<button type="submit" class="' . $kelas . '">Hapus</button></form>';
}
