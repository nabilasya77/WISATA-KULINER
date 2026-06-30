<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$port = 4000; 
$user = '4Mo4Kxg54E74QBX.root';
$pass = 'tLneNPcw4uT4LSq9'; // <- SUDAH DIGANTI DENGAN YANG BENAR
$db   = 'wisatakuliner';

$koneksi = mysqli_init();

if (!$koneksi) {
    die("mysqli_init gagal");
}

// Pastikan file ca.pem sudah Anda unduh dan letakkan di folder yang sama dengan file ini
$ca_path = __DIR__ . '/ca.pem';
mysqli_ssl_set($koneksi, NULL, NULL, $ca_path, NULL, NULL);

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