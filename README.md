# Etalase Digital Stella (UKK Level 3: CRUD + Hak Akses)

## Cara menjalankan
1. Pasang XAMPP, lalu start **Apache** dan **MySQL**.
2. Salin folder `etalase-stella` ke `C:\xampp\htdocs\`.
3. Buka http://localhost/phpmyadmin, tab **SQL**, jalankan isi file `database.sql`.
4. Buka http://localhost/etalase1-stella/

`BASE_URL` otomatis mengikuti nama folder proyek, jadi tidak perlu diubah.
Jika MySQL kamu memakai password, ubah `config/koneksi.php`.

`database.sql` membuat struktur tabel `users` dan `projek`, tetapi tidak berisi akun atau karya lama. Data asli yang sudah terhapus tidak bisa dipulihkan dari file ini; akun dan projek perlu dimasukkan kembali, kecuali kamu memiliki backup database.

## Profil pengguna
Fitur profil (edit nama/username, foto profil, dan ganti password) tersedia untuk siswa, admin, dan super admin. Kolom `foto_profil` sudah termasuk dalam `database.sql`. Untuk database lama yang tabel `users`-nya sudah ada, jalankan migrasi `database/migrations/2026_01_add_foto_profil.sql` hanya jika kolom `foto_profil` belum ada.

## Akun pengguna
Pengunjung dapat membuat akun siswa sendiri melalui tautan **Buat akun** di halaman masuk. Pendaftaran mandiri selalu membuat role siswa; akun admin dan super admin tetap dikelola melalui proses administrasi. Jika seluruh akun admin hilang, pulihkan dari backup database.

## Hak akses
| Fitur                          | Siswa | Admin | Super Admin |
|--------------------------------|:-----:|:-----:|:-----------:|
| Lihat etalase & detail projek  | ya    | ya    | ya          |
| Tambah projek                  | ya    | ya    | ya          |
| Edit/hapus projek sendiri      | ya    | ya    | ya          |
| Edit/hapus projek siapa pun    | tidak | ya    | ya          |
| Kelola data siswa              | tidak | ya    | ya          |
| Kelola data admin              | tidak | tidak | ya          |

## Struktur
- `config/` koneksi.php (MySQL) dan helper.php (login, role, CSRF, upload)
- `auth/` login dan logout
- `siswa/` etalase, detail, tambah, edit, hapus, projek saya
- `admin/` dashboard, kelola projek, kelola siswa
- `superadmin/` kelola admin
- `includes/` header, footer, form, proses pengguna
- `assets/css/` CSS dipisah per bagian:
  - `login.css` khusus halaman login (berdiri sendiri)
  - `base.css` dan `layout.css` dasar semua halaman setelah login
  - `etalase.css`, `detail.css`, `form.css`, `tabel.css`, `dashboard.css` khusus halaman terkait
- `assets/` css, js, uploads
