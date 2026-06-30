<?php
/**
 * File Koneksi Database
 * Sistem Informasi Kuliner Lokal & Destinasi Wisata Wonogiri
 */

// ==== KONFIGURASI DATABASE ====
define('DB_HOST', getenv('DB_HOST') ?: 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com');
define('DB_USER', getenv('DB_USER') ?: '4Mo4Kxg54E74QBX.root');
define('DB_PASS', getenv('DB_PASS') ?: 'tLneNPcw4uT4LSq9');
define('DB_NAME', getenv('DB_NAME') ?: 'wisatakuliner');
define('DB_PORT', getenv('DB_PORT') ?: 4000);

// Inisialisasi koneksi untuk mendukung SSL TiDB
$koneksi = mysqli_init();

// Abaikan verifikasi sertifikat server agar tidak error SSL di lingkungan serverless Vercel
mysqli_options($koneksi, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

// Proses penyambungan database
$real_connect = @mysqli_real_connect(
    $koneksi, 
    DB_HOST, 
    DB_USER, 
    DB_PASS, 
    DB_NAME, 
    DB_PORT, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

// Cek koneksi
if (!$real_connect) {
    die('<div style="font-family:sans-serif;padding:30px;background:#fff3f3;color:#a33;border:1px solid #f5c2c2;border-radius:8px;margin:30px;">
            <h2>Koneksi Database Gagal</h2>
            <p>' . mysqli_connect_error() . '</p>
            <p>Pastikan kredensial database sudah sesuai dan IP Access List di TiDB Cloud sudah dibuka (0.0.0.0/0).</p>
         </div>');
}

mysqli_set_charset($koneksi, 'utf8mb4');

// Base URL untuk asset
define('BASE_URL', '');

// ==== GOOGLE MAPS API KEY ====
define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: 'TEMPEL_API_KEY_GOOGLE_MAPS_DI_SINI');
?>