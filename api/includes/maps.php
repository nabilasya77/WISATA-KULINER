<?php
/**
 * Komponen Peta Lokasi & Tombol Rute
 * Dipanggil dari kuliner-detail.php dan destinasi-detail.php
 *
 * Variabel yang harus disiapkan sebelum include file ini:
 * $namaTempat   (string) - nama kuliner/destinasi
 * $latTempat    (float)  - latitude
 * $lngTempat    (float)  - longitude
 * $alamatTempat (string) - alamat lengkap untuk fallback pencarian
 */

$adaKoordinat = !empty($latTempat) && !empty($lngTempat);
$apiKeyTerisi = defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY !== '' && GOOGLE_MAPS_API_KEY !== 'TEMPEL_API_KEY_GOOGLE_MAPS_DI_SINI';

// Query pencarian: pakai koordinat kalau ada, kalau tidak fallback ke nama + alamat
$queryLokasi = $adaKoordinat
    ? $latTempat . ',' . $lngTempat
    : urlencode($namaTempat . ' ' . $alamatTempat . ' Wonogiri');

// Link untuk tombol "Lihat Rute" -> buka Google Maps Directions ke lokasi tujuan
$urlRute = 'https://www.google.com/maps/dir/?api=1&destination=' . $queryLokasi;

// Link untuk tombol "Buka di Google Maps" (lihat lokasi saja tanpa rute)
$urlLihatPeta = 'https://www.google.com/maps/search/?api=1&query=' . $queryLokasi;
?>

<h2>Lokasi &amp; Rute</h2>
<div class="map-box">
    <?php if ($apiKeyTerisi): ?>
        <iframe
            class="map-frame"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed/v1/place?key=<?= aman(GOOGLE_MAPS_API_KEY) ?>&q=<?= $queryLokasi ?>&zoom=15">
        </iframe>
    <?php else: ?>
        <!-- Fallback tanpa API key: tetap menampilkan peta walau tanpa key (mode terbatas) -->
        <iframe
            class="map-frame"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="https://maps.google.com/maps?q=<?= $queryLokasi ?>&z=15&output=embed">
        </iframe>
        <div class="map-key-notice">
            <i class="fa-solid fa-circle-info"></i>
            Mode peta dasar aktif. Tambahkan Google Maps API key di <code>includes/koneksi.php</code> untuk tampilan peta penuh.
        </div>
    <?php endif; ?>
</div>

<div class="map-actions">
    <a href="<?= aman($urlRute) ?>" target="_blank" rel="noopener" class="btn btn-orange">
        <i class="fa-solid fa-route"></i> Lihat Rute ke Sini
    </a>
    <a href="<?= aman($urlLihatPeta) ?>" target="_blank" rel="noopener" class="btn btn-outline">
        <i class="fa-solid fa-map-location-dot"></i> Buka di Google Maps
    </a>
</div>
