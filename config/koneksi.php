<?php
// Pengaturan koneksi MySQL (XAMPP default: user root, password kosong)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "etalase_stella";

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");
