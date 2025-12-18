<?php
// Konfigurasi koneksi database
$host = 'localhost';
$user = 'root';          // sesuaikan dengan XAMPP/hosting
$pass = '';              // password MySQL
$db   = 'easyroom';   // nama database (sesuai file SQL)

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
