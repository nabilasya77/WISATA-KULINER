<?php
/**
 * Kumpulan fungsi bantu (helper) yang dipakai di banyak halaman
 */

// Format angka rupiah, contoh: 15000 -> Rp 15.000
function formatRupiah($angka) {
    if ($angka == 0) {
        return 'Gratis';
    }
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Render bintang rating dalam HTML (font awesome / unicode star)
function renderBintang($rating) {
    $rating = (float) $rating;
    $penuh = floor($rating);
    $setengah = ($rating - $penuh) >= 0.5 ? 1 : 0;
    $kosong = 5 - $penuh - $setengah;

    $html = '';
    for ($i = 0; $i < $penuh; $i++) $html .= '<i class="fa-solid fa-star"></i>';
    if ($setengah) $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
    for ($i = 0; $i < $kosong; $i++) $html .= '<i class="fa-regular fa-star"></i>';
    return $html;
}

// Memotong teks deskripsi agar tidak terlalu panjang di tampilan kartu
function potongTeks($teks, $panjang = 90) {
    $teks = trim($teks);
    if (mb_strlen($teks) <= $panjang) {
        return $teks;
    }
    return mb_substr($teks, 0, $panjang) . '...';
}

// Membersihkan input agar aman ditampilkan (anti XSS dasar)
function aman($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
