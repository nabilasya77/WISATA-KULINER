<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah user sudah login DAN memiliki role sebagai 'admin'
if (!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Jika bukan admin, tendang keluar ke halaman login
    header("Location: ../login.php");
    exit;
}