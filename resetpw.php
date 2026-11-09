<?php
require __DIR__ . '/config/helper.php';

// username => password baru
$akun = [
    'superadmin' => 'admin123',
    'admin'      => 'Admin@123',
    'siswa1'     => 'Siswa@123',
];

foreach ($akun as $username => $pw) {
    db_jalankan(
        "UPDATE users SET password = ? WHERE username = ?",
        "ss",
        [password_hash($pw, PASSWORD_DEFAULT), $username]
    );
    echo "Password $username sudah diganti.<br>";
}