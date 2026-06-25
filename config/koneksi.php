<?php
$host = "localhost";
$user = "root";
$pass = "rijal123";
$db   = "alat_berat";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
