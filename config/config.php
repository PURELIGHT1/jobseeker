<?php
$host = "localhost";     // host database (default XAMPP)
$user = "root";          // user default MySQL
$pass = "";              // password default (kosong di XAMPP)
$db   = "mini_project2";    // nama database (ganti sesuai kebutuhan)

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
