<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$port = 4000; 
$user = '4Mo4Kxg54E74QBX.root';
$pass = 'tLneNPcw4uT4LSq9'; 
$db   = 'wisatakuliner';

$koneksi = mysqli_init();

// Set flag agar tidak memverifikasi sertifikat SSL lokal yang bermasalah di Vercel
mysqli_options($koneksi, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

$real_connect = @mysqli_real_connect(
    $koneksi, 
    $host, 
    $user, 
    $pass, 
    $db, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$real_connect) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>