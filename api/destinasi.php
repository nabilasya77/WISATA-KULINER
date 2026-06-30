<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'destinasi';
$judul_halaman = 'Destinasi Wisata';

$cari       = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kategoriId = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;
$urutkan    = isset($_GET['urutkan']) ? $_GET['urutkan'] : 'rating';

$sql = "SELECT d.*, kw.nama_kategori 
        FROM destinasi_wisata d 
        LEFT JOIN kategori_wisata kw ON d.kategori_id = kw.id 
        WHERE d.status = 'aktif'";
$params = [];
$types  = '';

if ($cari !== '') {
    $sql .= " AND (d.nama_destinasi LIKE ? OR d.lokasi LIKE ? OR d.deskripsi LIKE ?)";
    $kataKunci = "%$cari%";
    $params[] = $kataKunci; $params[] = $kataKunci; $params[] = $kataKunci;
    $types .= 'sss';
}

if ($kategoriId > 0) {
    $sql .= " AND d.kategori_id = ?";
    $params[] = $kategoriId;
    $types .= 'i';
}

switch ($urutkan) {
    case 'tiket_rendah': $sql .= " ORDER BY d.tiket_masuk ASC"; break;
    case 'tiket_tinggi':  $sql .= " ORDER BY d.tiket_masuk DESC"; break;
    case 'terbaru':       $sql .= " ORDER BY d.created_at DESC"; break;
    default:              $sql .= " ORDER BY d.rating DESC"; break;
}

$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$kategoriList = mysqli_query($koneksi, "SELECT * FROM kategori_wisata ORDER BY nama_kategori");

require_once 'includes/header.php';
?>

<main>
    <!-- ============ PAGE BANNER ============ -->
    <section class="page-banner">
        <div class="page-banner-bg">
            <img src="https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=1600" alt="Destinasi Wisata Wonogiri">
            <div class="hero-overlay"></div>
        </div>
        <div class="page-banner-content">
            <span class="section-tag tag-green"><i class="fa-solid fa-mountain"></i> Destinasi Wisata</span>
            <h1>Pesona Alam Wonogiri</h1>
            <p>Jelajahi keindahan waduk, bukit, air terjun, goa, dan wisata budaya yang tersembunyi di Wonogiri.</p>
        </div>
    </section>

    <!-- ============ FILTER & SEARCH ============ -->
    <section class="filter-section">
        <form method="get" class="filter-form">
            <div class="filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="cari" placeholder="Cari nama destinasi atau lokasi..." value="<?= aman($cari) ?>">
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
                <option value="tiket_rendah" <?= $urutkan === 'tiket_rendah' ? 'selected' : '' ?>>Tiket Termurah</option>
                <option value="tiket_tinggi" <?= $urutkan === 'tiket_tinggi' ? 'selected' : '' ?>>Tiket Termahal</option>
                <option value="terbaru" <?= $urutkan === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
            </select>

            <button type="submit" class="btn btn-green">Terapkan</button>
        </form>
    </section>

    <!-- ============ HASIL DAFTAR DESTINASI ============ -->
    <section class="section">
        <div class="section-header">
            <div>
                <h2>Daftar Destinasi</h2>
                <p class="result-count"><?= mysqli_num_rows($hasil) ?> destinasi ditemukan</p>
            </div>
        </div>

        <?php if (mysqli_num_rows($hasil) === 0): ?>
            <div class="empty-state">
                <i class="fa-solid fa-mountain"></i>
                <p>Tidak ada destinasi yang cocok dengan pencarianmu. Coba kata kunci lain.</p>
            </div>
        <?php else: ?>
        <div class="card-grid card-grid-wide">
            <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
            <a href="destinasi-detail.php?slug=<?= aman($row['slug']) ?>" class="info-card">
                <div class="info-card-img">
                    <img src="<?= aman($row['gambar_utama']) ?>" alt="<?= aman($row['nama_destinasi']) ?>">
                    <span class="badge-price badge-green"><?= formatRupiah($row['tiket_masuk']) ?></span>
                    <?php if ($row['is_unggulan']): ?><span class="badge-unggulan"><i class="fa-solid fa-fire"></i> Unggulan</span><?php endif; ?>
                </div>
                <div class="info-card-body">
                    <span class="info-card-kategori"><?= aman($row['nama_kategori'] ?? 'Umum') ?></span>
                    <h3><?= aman($row['nama_destinasi']) ?></h3>
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
