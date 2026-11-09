<?php
// Form projek yang dipakai tambah.php dan edit.php.
// Variabel: $data (array nilai), $errors (array), $mode ('tambah' | 'edit')
?>
<div class="editor-projek-layout">
    <aside class="editor-catatan editor-catatan-kiri" aria-label="Panduan mengisi projek">
        <span class="editor-catatan-nomor">01 — CERITAKAN</span>
        <h2>Ide kamu layak dilihat.</h2>
        <p>Mulai dari judul yang mudah diingat, lalu jelaskan proses dan manfaat projekmu.</p>
        <div class="editor-garis"></div>
        <span class="editor-catatan-poin"><i>01</i> Judul yang jelas</span>
        <span class="editor-catatan-poin"><i>02</i> Cerita yang singkat</span>
    </aside>

<form method="post" enctype="multipart/form-data" class="form<?= $mode === 'tambah' ? ' form-projek-tambah' : ' form-projek-edit' ?>" novalidate>
    <?= csrf_field() ?>

    <div class="field field-judul">
        <label for="judul">Judul projek</label>
        <input type="text" id="judul" name="judul" maxlength="150" value="<?= e($data['judul']) ?>" required>
        <?php if (isset($errors['judul'])): ?><p class="galat"><?= e($errors['judul']) ?></p><?php endif; ?>
    </div>

    <div class="field field-deskripsi">
        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="6" required><?= e($data['deskripsi']) ?></textarea>
        <?php if (isset($errors['deskripsi'])): ?><p class="galat"><?= e($errors['deskripsi']) ?></p><?php endif; ?>
    </div>

    <div class="field field-gambar">
        <label for="gambar">Gambar projek <?= $mode === 'edit' ? '(kosongkan jika tidak diganti)' : '' ?></label>
        <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp">
        <small>JPG, PNG, atau WEBP, maksimal 2 MB.</small>
        <?php if (isset($errors['gambar'])): ?><p class="galat"><?= e($errors['gambar']) ?></p><?php endif; ?>
        <?php if ($mode === 'edit' && !empty($data['gambar'])): ?>
            <img id="preview" class="preview" src="<?= BASE_URL ?>/assets/uploads/<?= e($data['gambar']) ?>" alt="Gambar saat ini">
        <?php else: ?>
            <img id="preview" class="preview" alt="" hidden>
        <?php endif; ?>
    </div>

    <div class="field field-link-pendukung">
        <label for="link_pendukung">Link pendukung</label>
        <input type="url" id="link_pendukung" name="link_pendukung" maxlength="255"
               placeholder="https://github.com/..." value="<?= e($data['link_pendukung']) ?>" required>
        <?php if (isset($errors['link_pendukung'])): ?><p class="galat"><?= e($errors['link_pendukung']) ?></p><?php endif; ?>
    </div>

    <div class="field field-link-demo">
        <label for="link_demo">Link demo <span class="opsional">(opsional)</span></label>
        <input type="url" id="link_demo" name="link_demo" maxlength="255"
               placeholder="https://..." value="<?= e($data['link_demo']) ?>">
        <?php if (isset($errors['link_demo'])): ?><p class="galat"><?= e($errors['link_demo']) ?></p><?php endif; ?>
    </div>

    <div class="aksi-form">
        <button type="submit" class="tombol"><?= $mode === 'edit' ? 'Simpan perubahan' : 'Simpan projek' ?></button>
        <a class="tombol tombol-garis" href="<?= BASE_URL ?>/siswa/<?= is_admin() ? 'index.php' : 'projek_saya.php' ?>">Batal</a>
    </div>
</form>

    <aside class="editor-catatan editor-catatan-kanan" aria-label="Checklist sebelum menyimpan">
        <span class="editor-catatan-nomor">02 — SIAPKAN</span>
        <div class="editor-cap" aria-hidden="true">✓</div>
        <h2>Sentuhan akhir</h2>
        <p>Lengkapi detail supaya teman-teman bisa melihat dan mencoba karyamu.</p>
        <div class="editor-garis"></div>
        <span class="editor-check"><i></i> Gambar projek</span>
        <span class="editor-check"><i></i> Link pendukung</span>
        <span class="editor-check"><i></i> Link demo opsional</span>
    </aside>
</div>
