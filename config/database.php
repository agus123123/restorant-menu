<?php
$host     = getenv("DB_HOST"); // Ganti dengan Host Aiven Anda
$port     = getenv("DB_PORT"); // Port Aiven
$user     = getenv("DB_USER"); // Username Aiven
$password = getenv("DB_PASSWORD"); // Password Aiven
$dbname   = getenv("DB_NAME"); // Database Aiven

// Inisialisasi koneksi MySQLi
$koneksi = mysqli_init();

// Memaksa penggunaan SSL (Wajib untuk Aiven)
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

// Mengeksekusi koneksi remote
mysqli_real_connect($koneksi, $host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>