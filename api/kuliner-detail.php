<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'kuliner';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$stmt = mysqli_prepare($koneksi, "SELECT k.*, kk.nama_kategori 
        FROM kuliner k LEFT JOIN kategori_kuliner kk ON k.kategori_id = kk.id 
        WHERE k.slug = ? AND k.status = 'aktif' LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    header('Location: kuliner.php');
    exit;
}

$judul_halaman = $data['nama_kuliner'];

// Ambil galeri tambahan
$stmtGaleri = mysqli_prepare($koneksi, "SELECT url_gambar FROM galeri_kuliner WHERE kuliner_id = ?");
mysqli_stmt_bind_param($stmtGaleri, 'i', $data['id']);
mysqli_stmt_execute($stmtGaleri);
$galeri = mysqli_stmt_get_result($stmtGaleri);

// Ambil ulasan
$stmtUlasan = mysqli_prepare($koneksi, "SELECT * FROM ulasan WHERE tipe = 'kuliner' AND item_id = ? ORDER BY created_at DESC LIMIT 5");
mysqli_stmt_bind_param($stmtUlasan, 'i', $data['id']);
mysqli_stmt_execute($stmtUlasan);
$ulasanList = mysqli_stmt_get_result($stmtUlasan);

// Kuliner terkait (kategori sama)
$stmtTerkait = mysqli_prepare($koneksi, "SELECT * FROM kuliner WHERE kategori_id = ? AND id != ? AND status='aktif' LIMIT 3");
mysqli_stmt_bind_param($stmtTerkait, 'ii', $data['kategori_id'], $data['id']);
mysqli_stmt_execute($stmtTerkait);
$terkait = mysqli_stmt_get_result($stmtTerkait);

require_once 'includes/header.php';
?>

<main>
    <section class="detail-hero">
        <img src="<?= aman($data['gambar_utama']) ?>" alt="<?= aman($data['nama_kuliner']) ?>">
        <div class="hero-overlay"></div>
        <div class="detail-hero-content">
            <a href="kuliner.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Kuliner Lokal</a>
            <span class="section-tag"><?= aman($data['nama_kategori'] ?? 'Kuliner') ?></span>
            <h1><?= aman($data['nama_kuliner']) ?></h1>
            <div class="detail-rating">
                <?= renderBintang($data['rating']) ?>
                <span><?= $data['rating'] ?> (<?= $data['jumlah_ulasan'] ?> ulasan)</span>
            </div>
        </div>
    </section>

    <section class="detail-section">
        <div class="detail-grid">
            <div class="detail-main">
                <h2>Tentang Kuliner Ini</h2>
                <p><?= nl2br(aman($data['deskripsi'])) ?></p>

                <?php
                // Menyiapkan variabel untuk komponen peta & rute
                $namaTempat   = $data['nama_kuliner'];
                $latTempat    = $data['latitude'];
                $lngTempat    = $data['longitude'];
                $alamatTempat = $data['alamat_lengkap'] ?: $data['lokasi'];
                require 'includes/maps.php';
                ?>

                <?php if (mysqli_num_rows($galeri) > 0): ?>
                <h2>Galeri</h2>
                <div class="gallery-grid">
                    <?php while ($g = mysqli_fetch_assoc($galeri)): ?>
                        <img src="<?= aman($g['url_gambar']) ?>" alt="Galeri <?= aman($data['nama_kuliner']) ?>">
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>

                <h2>Ulasan Pengunjung</h2>
                <?php if (mysqli_num_rows($ulasanList) === 0): ?>
                    <p class="empty-text">Belum ada ulasan. Jadilah yang pertama memberi ulasan!</p>
                <?php else: ?>
                    <div class="review-list">
                        <?php while ($u = mysqli_fetch_assoc($ulasanList)): ?>
                        <div class="review-item">
                            <div class="review-head">
                                <span class="review-name"><?= aman($u['nama_pengulas']) ?></span>
                                <span class="review-stars"><?= renderBintang($u['rating']) ?></span>
                            </div>
                            <p><?= aman($u['komentar']) ?></p>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="detail-sidebar">
                <div class="info-box">
                    <h3>Informasi</h3>
                    <ul class="info-list">
                        <li><i class="fa-solid fa-tag"></i> <span>Mulai dari <b><?= formatRupiah($data['harga_mulai']) ?></b></span></li>
                        <li><i class="fa-solid fa-location-dot"></i> <span><?= aman($data['alamat_lengkap'] ?: $data['lokasi']) ?></span></li>
                        <li><i class="fa-regular fa-clock"></i> <span><?= aman($data['jam_operasional']) ?></span></li>
                        <li><i class="fa-solid fa-phone"></i> <span><?= aman($data['kontak']) ?></span></li>
                    </ul>
                    <a href="pesan.php?slug=<?= aman($data['slug']) ?>" class="btn btn-green btn-block">
                        <i class="fa-solid fa-cart-shopping"></i> Pesan Sekarang
                    </a>
                    <a href="kuliner.php" class="btn btn-orange btn-block">Lihat Kuliner Lainnya</a>
                </div>
            </aside>
        </div>

        <?php if (mysqli_num_rows($terkait) > 0): ?>
        <div class="related-section">
            <h2>Kuliner Lainnya</h2>
            <div class="card-grid">
                <?php while ($t = mysqli_fetch_assoc($terkait)): ?>
                <a href="kuliner-detail.php?slug=<?= aman($t['slug']) ?>" class="info-card">
                    <div class="info-card-img">
                        <img src="<?= aman($t['gambar_utama']) ?>" alt="<?= aman($t['nama_kuliner']) ?>">
                        <span class="badge-price"><?= formatRupiah($t['harga_mulai']) ?></span>
                    </div>
                    <div class="info-card-body">
                        <h3><?= aman($t['nama_kuliner']) ?></h3>
                        <p class="info-card-loc"><i class="fa-solid fa-location-dot"></i> <?= aman($t['lokasi']) ?></p>
                        <div class="info-card-rating"><?= renderBintang($t['rating']) ?> <span><?= $t['rating'] ?></span></div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
