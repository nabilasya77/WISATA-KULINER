<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'kuliner';
$judul_halaman = 'Kuliner Lokal';

// ---- Ambil parameter filter dari URL ----
$cari       = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kategoriId = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;
$urutkan    = isset($_GET['urutkan']) ? $_GET['urutkan'] : 'rating';

// ---- Bangun query secara dinamis & aman (prepared statement) ----
$sql = "SELECT k.*, kk.nama_kategori 
        FROM kuliner k 
        LEFT JOIN kategori_kuliner kk ON k.kategori_id = kk.id 
        WHERE k.status = 'aktif'";
$params = [];
$types  = '';

if ($cari !== '') {
    $sql .= " AND (k.nama_kuliner LIKE ? OR k.lokasi LIKE ? OR k.deskripsi LIKE ?)";
    $kataKunci = "%$cari%";
    $params[] = $kataKunci; $params[] = $kataKunci; $params[] = $kataKunci;
    $types .= 'sss';
}

if ($kategoriId > 0) {
    $sql .= " AND k.kategori_id = ?";
    $params[] = $kategoriId;
    $types .= 'i';
}

switch ($urutkan) {
    case 'harga_rendah': $sql .= " ORDER BY k.harga_mulai ASC"; break;
    case 'harga_tinggi': $sql .= " ORDER BY k.harga_mulai DESC"; break;
    case 'terbaru':      $sql .= " ORDER BY k.created_at DESC"; break;
    default:             $sql .= " ORDER BY k.rating DESC"; break;
}

$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

// Ambil daftar kategori untuk dropdown filter
$kategoriList = mysqli_query($koneksi, "SELECT * FROM kategori_kuliner ORDER BY nama_kategori");

require_once 'includes/header.php';
?>

<main>
    <!-- ============ PAGE BANNER ============ -->
    <section class="page-banner">
        <div class="page-banner-bg">
            <img src="assets/img/kuliner-khas-wonogiri-banner.jpg" alt="Kuliner Wonogiri">
            <div class="hero-overlay"></div>
        </div>
        <div class="page-banner-content">
            <span class="section-tag"><i class="fa-solid fa-utensils"></i> Kuliner Lokal</span>
            <h1>Cita Rasa Khas Wonogiri</h1>
            <p>Temukan kelezatan kuliner tradisional, jajanan, dan oleh-oleh khas Wonogiri dari berbagai penjuru daerah.</p>
        </div>
    </section>

    <!-- ============ FILTER & SEARCH ============ -->
    <section class="filter-section">
        <form method="get" class="filter-form">
            <div class="filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="cari" placeholder="Cari nama kuliner atau lokasi..." value="<?= aman($cari) ?>">
            </div>

            <select name="kategori" class="filter-select" onchange="this.form.submit()">
                <option value="0">Semua Kategori</option>
                <?php while ($kat = mysqli_fetch_assoc($kategoriList)): ?>
                    <option value="<?= $kat['id'] ?>" <?= $kategoriId == $kat['id'] ? 'selected' : '' ?>>
                        <?= aman($kat['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="urutkan" class="filter-select" onchange="this.form.submit()">
                <option value="rating" <?= $urutkan === 'rating' ? 'selected' : '' ?>>Rating Tertinggi</option>
                <option value="harga_rendah" <?= $urutkan === 'harga_rendah' ? 'selected' : '' ?>>Harga Terendah</option>
                <option value="harga_tinggi" <?= $urutkan === 'harga_tinggi' ? 'selected' : '' ?>>Harga Tertinggi</option>
                <option value="terbaru" <?= $urutkan === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
            </select>

            <button type="submit" class="btn btn-orange">Terapkan</button>
        </form>
    </section>

    <!-- ============ HASIL DAFTAR KULINER ============ -->
    <section class="section">
        <div class="section-header">
            <div>
                <h2>Daftar Kuliner</h2>
                <p class="result-count"><?= mysqli_num_rows($hasil) ?> kuliner ditemukan</p>
            </div>
        </div>

        <?php if (mysqli_num_rows($hasil) === 0): ?>
            <div class="empty-state">
                <i class="fa-solid fa-utensils"></i>
                <p>Tidak ada kuliner yang cocok dengan pencarianmu. Coba kata kunci lain.</p>
            </div>
        <?php else: ?>
        <div class="card-grid card-grid-wide">
            <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
            <a href="kuliner-detail.php?slug=<?= aman($row['slug']) ?>" class="info-card">
                <div class="info-card-img">
                    <img src="<?= aman($row['gambar_utama']) ?>" alt="<?= aman($row['nama_kuliner']) ?>">
                    <span class="badge-price"><?= formatRupiah($row['harga_mulai']) ?></span>
                    <?php if ($row['is_unggulan']): ?><span class="badge-unggulan"><i class="fa-solid fa-fire"></i> Unggulan</span><?php endif; ?>
                </div>
                <div class="info-card-body">
                    <span class="info-card-kategori"><?= aman($row['nama_kategori'] ?? 'Umum') ?></span>
                    <h3><?= aman($row['nama_kuliner']) ?></h3>
                    <p class="info-card-desc"><?= aman(potongTeks($row['deskripsi'], 80)) ?></p>
                    <p class="info-card-loc"><i class="fa-solid fa-location-dot"></i> <?= aman($row['lokasi']) ?></p>
                    <div class="info-card-rating">
                        <?= renderBintang($row['rating']) ?>
                        <span><?= $row['rating'] ?> (<?= $row['jumlah_ulasan'] ?> ulasan)</span>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </section>
</main>

<?php
mysqli_stmt_close($stmt);
require_once 'includes/footer.php';
?>
