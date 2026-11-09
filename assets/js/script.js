// Etalase Digital Stella - interaksi sisi klien

// 1. Konfirmasi sebelum menghapus: form dengan atribut data-konfirm
document.querySelectorAll('form[data-konfirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!window.confirm(form.dataset.konfirm)) {
            event.preventDefault();
        }
    });
});

// 2. Pratinjau gambar sebelum diunggah
var inputGambar = document.getElementById('gambar');
var preview = document.getElementById('preview');

if (inputGambar && preview) {
    inputGambar.addEventListener('change', function () {
        var file = inputGambar.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar maksimal 2 MB.');
            inputGambar.value = '';
            return;
        }
        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
    });
}

// 3. Pratinjau foto profil sebelum disimpan
var inputFotoProfil = document.getElementById('foto_profil');
var fotoPreview = document.getElementById('foto-preview');

if (inputFotoProfil && fotoPreview) {
    inputFotoProfil.addEventListener('change', function () {
        var file = inputFotoProfil.files[0];
        var inisial = document.getElementById('foto-inisial');
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            inputFotoProfil.value = '';
            return;
        }
        fotoPreview.src = URL.createObjectURL(file);
        fotoPreview.hidden = false;
        if (inisial) inisial.hidden = true;
    });
}
