<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cloudhost_db';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
