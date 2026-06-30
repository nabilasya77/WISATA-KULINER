<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com'; 
$port = 4000; 
$user = '4V8VSYyX9oHGkdj.root';
$pass = 'NXj2m2Ptm46Lhlbj'; 
$db   = 'sembakoku';

$koneksi = mysqli_init();

$ca_path = '/etc/ssl/certs/ca-certificates.crt';
mysqli_ssl_set($koneksi, NULL, NULL, $ca_path, NULL, NULL);

$real_connect = mysqli_real_connect(
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
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>