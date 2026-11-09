<?php
// Variabel: $data, $errors, $mode, $role_target, $url_daftar
?>
<form method="post" class="form" autocomplete="off" novalidate>
    <?= csrf_field() ?>

    <label for="nama">Nama lengkap</label>
    <input type="text" id="nama" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" required>
    <?php if (isset($errors['nama'])): ?><p class="galat"><?= e($errors['nama']) ?></p><?php endif; ?>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" maxlength="50" value="<?= e($data['username']) ?>" required>
    <?php if (isset($errors['username'])): ?><p class="galat"><?= e($errors['username']) ?></p><?php endif; ?>

    <?php if ($role_target === 'siswa'): ?>
    <label for="kelas">Kelas <span class="opsional">(opsional)</span></label>
    <input type="text" id="kelas" name="kelas" maxlength="20" placeholder="XI RPL 2" value="<?= e($data['kelas']) ?>">
    <?php if (isset($errors['kelas'])): ?><p class="galat"><?= e($errors['kelas']) ?></p><?php endif; ?>
    <?php endif; ?>

    <label for="password">Password <?= $mode === 'edit' ? '(kosongkan jika tidak diganti)' : '' ?></label>
    <input type="password" id="password" name="password" <?= $mode === 'tambah' ? 'required' : '' ?>>
    <small>Minimal 6 karakter.</small>
    <?php if (isset($errors['password'])): ?><p class="galat"><?= e($errors['password']) ?></p><?php endif; ?>

    <div class="aksi-form">
        <button type="submit" class="tombol"><?= $mode === 'edit' ? 'Simpan perubahan' : 'Simpan' ?></button>
        <a class="tombol tombol-garis" href="<?= e($url_daftar) ?>">Batal</a>
    </div>
</form>
