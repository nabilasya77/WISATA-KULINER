<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'destinasi';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$stmt = mysqli_prepare($koneksi, "SELECT d.*, kw.nama_kategori 
        FROM destinasi_wisata d LEFT JOIN kategori_wisata kw ON d.kategori_id = kw.id 
        WHERE d.slug = ? AND d.status = 'aktif' LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    header('Location: destinasi.php');
    exit;
}

$judul_halaman = $data['nama_destinasi'];

$stmtGaleri = mysqli_prepare($koneksi, "SELECT url_gambar FROM galeri_destinasi WHERE destinasi_id = ?");
mysqli_stmt_bind_param($stmtGaleri, 'i', $data['id']);
mysqli_stmt_execute($stmtGaleri);
$galeri = mysqli_stmt_get_result($stmtGaleri);

$stmtUlasan = mysqli_prepare($koneksi, "SELECT * FROM ulasan WHERE tipe = 'destinasi' AND item_id = ? ORDER BY created_at DESC LIMIT 5");
mysqli_stmt_bind_param($stmtUlasan, 'i', $data['id']);
mysqli_stmt_execute($stmtUlasan);
$ulasanList = mysqli_stmt_get_result($stmtUlasan);

$stmtTerkait = mysqli_prepare($koneksi, "SELECT * FROM destinasi_wisata WHERE kategori_id = ? AND id != ? AND status='aktif' LIMIT 3");
mysqli_stmt_bind_param($stmtTerkait, 'ii', $data['kategori_id'], $data['id']);
mysqli_stmt_execute($stmtTerkait);
$terkait = mysqli_stmt_get_result($stmtTerkait);

require_once 'includes/header.php';
?>

<main>
    <section class="detail-hero">
        <img src="<?= aman($data['gambar_utama']) ?>" alt="<?= aman($data['nama_destinasi']) ?>">
        <div class="hero-overlay"></div>
        <div class="detail-hero-content">
            <a href="destinasi.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Destinasi Wisata</a>
            <span class="section-tag tag-green"><?= aman($data['nama_kategori'] ?? 'Wisata') ?></span>
            <h1><?= aman($data['nama_destinasi']) ?></h1>
            <div class="detail-rating">
                <?= renderBintang($data['rating']) ?>
                <span><?= $data['rating'] ?> (<?= $data['jumlah_ulasan'] ?> ulasan)</span>
            </div>
        </div>
    </section>

    <section class="detail-section">
        <div class="detail-grid">
            <div class="detail-main">
                <h2>Tentang Destinasi Ini</h2>
                <p><?= nl2br(aman($data['deskripsi'])) ?></p>

                <?php if (!empty($data['fasilitas'])): ?>
                <h2>Fasilitas</h2>
                <p><?= nl2br(aman($data['fasilitas'])) ?></p>
                <?php endif; ?>

                <?php
                // Menyiapkan variabel untuk komponen peta & rute
                $namaTempat   = $data['nama_destinasi'];
                $latTempat    = $data['latitude'];
                $lngTempat    = $data['longitude'];
                $alamatTempat = $data['alamat_lengkap'] ?: $data['lokasi'];
                require 'includes/maps.php';
                ?>

                <?php if (mysqli_num_rows($galeri) > 0): ?>
                <h2>Galeri</h2>
                <div class="gallery-grid">
                    <?php while ($g = mysqli_fetch_assoc($galeri)): ?>
                        <img src="<?= aman($g['url_gambar']) ?>" alt="Galeri <?= aman($data['nama_destinasi']) ?>">
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
                        <li><i class="fa-solid fa-ticket"></i> <span>Tiket masuk <b><?= formatRupiah($data['tiket_masuk']) ?></b></span></li>
                        <li><i class="fa-solid fa-location-dot"></i> <span><?= aman($data['alamat_lengkap'] ?: $data['lokasi']) ?></span></li>
                        <li><i class="fa-regular fa-clock"></i> <span><?= aman($data['jam_operasional']) ?></span></li>
                        <li><i class="fa-solid fa-phone"></i> <span><?= aman($data['kontak']) ?></span></li>
                    </ul>
                    <a href="destinasi.php" class="btn btn-green btn-block">Lihat Destinasi Lainnya</a>
                </div>
            </aside>
        </div>

        <?php if (mysqli_num_rows($terkait) > 0): ?>
        <div class="related-section">
            <h2>Destinasi Lainnya</h2>
            <div class="card-grid">
                <?php while ($t = mysqli_fetch_assoc($terkait)): ?>
                <a href="destinasi-detail.php?slug=<?= aman($t['slug']) ?>" class="info-card">
                    <div class="info-card-img">
                        <img src="<?= aman($t['gambar_utama']) ?>" alt="<?= aman($t['nama_destinasi']) ?>">
                        <span class="badge-price badge-green"><?= formatRupiah($t['tiket_masuk']) ?></span>
                    </div>
                    <div class="info-card-body">
                        <h3><?= aman($t['nama_destinasi']) ?></h3>
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
