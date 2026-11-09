<?php
require __DIR__ . '/config/helper.php';
wajib_login();

$kolom_foto_profil = db_satu("SHOW COLUMNS FROM users LIKE 'foto_profil'");
if (!$kolom_foto_profil) {
    http_response_code(503);
    $judul_halaman = 'Pembaruan database diperlukan';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="profil-halaman">
        <header class="profil-heading">
            <p class="profil-eyebrow">PEMBARUAN SISTEM</p>
            <h1>Profil belum siap digunakan</h1>
            <p>Database belum memiliki kolom untuk menyimpan foto profil.</p>
        </header>
        <div class="notif notif-error" role="alert">
            Di phpMyAdmin, pilih database <strong>etalase_stella</strong>, buka tab <strong>SQL</strong>, lalu jalankan:
            <code>ALTER TABLE users ADD COLUMN foto_profil VARCHAR(255) NULL;</code>
            Setelah berhasil, muat ulang halaman ini.
        </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$user_id = (int)$_SESSION['user']['id'];
$user = db_satu(
    "SELECT id, nama, username, password, role, foto_profil FROM users WHERE id = ?",
    "i",
    [$user_id]
);

if (!$user) {
    $_SESSION = [];
    session_regenerate_id(true);
    flash('Akun tidak ditemukan. Silakan masuk kembali.', 'error');
    redirect(BASE_URL . '/auth/login.php');
}

$errors = [];
$nama = $user['nama'];
$username = $user['username'];
$foto_profil_baru = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password_sekarang = $_POST['password_sekarang'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if ($nama === '' || mb_strlen($nama) > 100) {
        $errors['nama'] = 'Nama wajib diisi (maksimal 100 karakter).';
    }
    if (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $username)) {
        $errors['username'] = 'Username 3-50 karakter: huruf, angka, titik, atau garis bawah.';
    } else {
        $username_sama = db_satu(
            "SELECT id FROM users WHERE username = ? AND id <> ?",
            "si",
            [$username, $user_id]
        );
        if ($username_sama) {
            $errors['username'] = 'Username sudah dipakai akun lain.';
        }
    }

    if ($password_baru !== '') {
        if ($password_sekarang === '' || !password_verify($password_sekarang, $user['password'])) {
            $errors['password_sekarang'] = 'Masukkan password saat ini dengan benar untuk menggantinya.';
        }
        if (mb_strlen($password_baru) < 6) {
            $errors['password_baru'] = 'Password baru minimal 6 karakter.';
        }
        if ($password_baru !== $konfirmasi_password) {
            $errors['konfirmasi_password'] = 'Konfirmasi password belum sama.';
        }
    } elseif ($password_sekarang !== '' || $konfirmasi_password !== '') {
        $errors['password_baru'] = 'Isi password baru dan konfirmasinya jika ingin mengganti password.';
    }

    $ada_foto = isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE;
    if ($ada_foto) {
        $error_upload = '';
        $foto_profil_baru = upload_gambar($_FILES['foto_profil'], $error_upload);
        if ($foto_profil_baru === null) {
            $errors['foto_profil'] = $error_upload;
        }
    }

    if (!$errors) {
        if ($password_baru !== '') {
            $berhasil = db_jalankan(
                "UPDATE users SET nama = ?, username = ?, foto_profil = ?, password = ? WHERE id = ?",
                "ssssi",
                [$nama, $username, $foto_profil_baru ?? $user['foto_profil'], password_hash($password_baru, PASSWORD_DEFAULT), $user_id]
            );
        } else {
            $berhasil = db_jalankan(
                "UPDATE users SET nama = ?, username = ?, foto_profil = ? WHERE id = ?",
                "sssi",
                [$nama, $username, $foto_profil_baru ?? $user['foto_profil'], $user_id]
            );
        }

        if ($berhasil) {
            if ($foto_profil_baru && $user['foto_profil']) {
                hapus_gambar($user['foto_profil']);
            }
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['foto_profil'] = $foto_profil_baru ?? $user['foto_profil'];
            flash('Profil berhasil diperbarui.');
            redirect(BASE_URL . '/profil.php');
        }

        $errors['umum'] = 'Profil gagal disimpan. Periksa koneksi database lalu coba lagi.';
    }

    if ($foto_profil_baru) {
        hapus_gambar($foto_profil_baru);
        $foto_profil_baru = null;
    }
}

$judul_halaman = 'Profil saya';
$css_halaman = ['profil'];
require __DIR__ . '/includes/header.php';
?>
<section class="profil-halaman">
    <header class="profil-heading">
        <p class="profil-eyebrow">AKUN ETALASE DIGITAL STELLA</p>
        <h1>Profil saya</h1>
        <p>Perbarui identitas dan keamanan akunmu.</p>
    </header>

    <?php if (isset($errors['umum'])): ?>
        <div class="notif notif-error" role="alert"><?= e($errors['umum']) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="profil-form" novalidate>
        <?= csrf_field() ?>
        <section class="profil-identitas" aria-labelledby="judul-identitas">
            <div class="profil-foto">
                <?php if (!empty($user['foto_profil'])): ?>
                    <img id="foto-preview" src="<?= BASE_URL ?>/assets/uploads/<?= e($user['foto_profil']) ?>" alt="Foto profil saat ini">
                <?php else: ?>
                    <span id="foto-inisial" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['nama'], 0, 1))) ?></span>
                    <img id="foto-preview" alt="Pratinjau foto profil" hidden>
                <?php endif; ?>
            </div>
            <div class="profil-identitas-teks">
                <p class="profil-eyebrow">IDENTITAS AKUN</p>
                <h2 id="judul-identitas"><?= e($user['nama']) ?></h2>
                <p><?= e(label_role($user['role'])) ?> · @<?= e($user['username']) ?></p>
                <label class="profil-pilih-foto" for="foto_profil">Ganti foto profil</label>
                <input class="profil-file" type="file" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/webp" aria-describedby="foto-petunjuk">
                <small id="foto-petunjuk">JPG, PNG, atau WEBP, maksimal 2 MB.</small>
                <?php if (isset($errors['foto_profil'])): ?><p class="galat"><?= e($errors['foto_profil']) ?></p><?php endif; ?>
            </div>
        </section>

        <div class="profil-grid">
            <section class="profil-bagian" aria-labelledby="judul-data">
                <div class="profil-bagian-heading">
                    <span class="profil-bagian-nomor">01</span>
                    <div><h2 id="judul-data">Informasi akun</h2><p>Nama dan username yang tampil di etalase.</p></div>
                </div>

                <label for="nama">Nama lengkap</label>
                <input type="text" id="nama" name="nama" maxlength="100" value="<?= e($nama) ?>" required>
                <?php if (isset($errors['nama'])): ?><p class="galat"><?= e($errors['nama']) ?></p><?php endif; ?>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="50" value="<?= e($username) ?>" required>
                <small>3-50 karakter: huruf, angka, titik, atau garis bawah.</small>
                <?php if (isset($errors['username'])): ?><p class="galat"><?= e($errors['username']) ?></p><?php endif; ?>
            </section>

            <section class="profil-bagian" aria-labelledby="judul-password">
                <div class="profil-bagian-heading">
                    <span class="profil-bagian-nomor">02</span>
                    <div><h2 id="judul-password">Keamanan</h2><p>Kosongkan jika tidak ingin mengganti password.</p></div>
                </div>

                <label for="password_sekarang">Password saat ini</label>
                <input type="password" id="password_sekarang" name="password_sekarang" autocomplete="current-password">
                <?php if (isset($errors['password_sekarang'])): ?><p class="galat"><?= e($errors['password_sekarang']) ?></p><?php endif; ?>

                <label for="password_baru">Password baru</label>
                <input type="password" id="password_baru" name="password_baru" autocomplete="new-password">
                <small>Minimal 6 karakter.</small>
                <?php if (isset($errors['password_baru'])): ?><p class="galat"><?= e($errors['password_baru']) ?></p><?php endif; ?>

                <label for="konfirmasi_password">Ulangi password baru</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" autocomplete="new-password">
                <?php if (isset($errors['konfirmasi_password'])): ?><p class="galat"><?= e($errors['konfirmasi_password']) ?></p><?php endif; ?>
            </section>
        </div>

        <div class="profil-aksi">
            <button type="submit" class="tombol">Simpan profil</button>
            <a class="tombol tombol-garis" href="<?= e(beranda_role($user['role'])) ?>">Batal</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
