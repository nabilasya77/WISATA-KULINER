<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$port = 4000; 
$user = '4Mo4Kxg54E74QBX.root';
$pass = 'NSsVg4rAZf4aOsp1K'; 
$db   = 'wisatakuliner';

$koneksi = mysqli_init();

if (!$koneksi) {
    die("mysqli_init gagal");
}

// Menggunakan URL sertifikat publik DigiCert yang umum digunakan TiDB
$ca_path = 'https://cacerts.digicert.com/DigiCertGlobalRootCA.crt.pem';
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
if (!$real_connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>