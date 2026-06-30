<?php


require_once 'api/includes/koneksi.php';
require_once 'api/includes/functions.php';

$halaman_aktif = 'beranda';
$judul_halaman = 'Beranda';

// Ambil 1 kuliner unggulan untuk kartu "Cita Rasa Nusantara"
$qKuliner = mysqli_query($koneksi, "SELECT * FROM kuliner WHERE is_unggulan = 1 ORDER BY rating DESC LIMIT 1");
$kulinerUnggulan = mysqli_fetch_assoc($qKuliner);

// Ambil 1 destinasi unggulan untuk kartu "Pesona Alam Wonogiri"
$qDestinasi = mysqli_query($koneksi, "SELECT * FROM destinasi_wisata WHERE is_unggulan = 1 ORDER BY rating DESC LIMIT 1 OFFSET 2");
$destinasiUnggulan = mysqli_fetch_assoc($qDestinasi);
if (!$destinasiUnggulan) {
    $qDestinasi = mysqli_query($koneksi, "SELECT * FROM destinasi_wisata WHERE is_unggulan = 1 ORDER BY rating DESC LIMIT 1");
    $destinasiUnggulan = mysqli_fetch_assoc($qDestinasi);
}

// Hitung total destinasi & kuliner aktif untuk bagian "Tahukah Kamu?"
$totalDestinasi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM destinasi_wisata WHERE status='aktif'"))['total'];
$totalKuliner = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kuliner WHERE status='aktif'"))['total'];

require_once 'includes/header.php';
?>
<main>
    <!-- ============ HERO SECTION ============ -->
    <section class="hero">
        <div class="hero-bg">
            <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=1600" alt="Pemandangan Waduk Gajah Mungkur Wonogiri">
            <div class="hero-overlay"></div>
        </div>

        <div class="hero-badge">
            <span>Wonogiri Sejuta Cerita</span>
            <i class="fa-regular fa-heart"></i>
        </div>

        <div class="hero-content">
            <h1 class="hero-title">
                Jelajahi <span class="script-text">Wonogiri</span>
            </h1>
            <p class="hero-tagline">Rasa yang Menggoda, Pesona yang Tak Terlupa</p>
            <p class="hero-desc">Temukan kelezatan kuliner lokal dan keindahan destinasi wisata terbaik di Wonogiri dalam satu perjalanan.</p>

            <form class="search-box" action="kuliner.php" method="get">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="cari" placeholder="Cari kuliner, tempat wisata, atau kegiatan...">
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                <div class="search-divider"></div>
                <div class="search-location">
                    <i class="fa-solid fa-location-dot"></i> Wonogiri <i class="fa-solid fa-chevron-down"></i>
                </div>
            </form>

            <div class="quick-links">
                <a href="kuliner.php" class="quick-link">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Kuliner Lokal</span>
                </a>
                <a href="destinasi.php" class="quick-link">
                    <i class="fa-solid fa-mountain"></i>
                    <span>Destinasi Wisata</span>
                </a>
                <a href="#" class="quick-link">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Peta Wisata</span>
                </a>
                <a href="#" class="quick-link">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Panduan Perjalanan</span>
                </a>
            </div>
        </div>

        <!-- ===== Kartu highlight di dalam hero ===== -->
        <div class="hero-cards">
            <?php if ($kulinerUnggulan): ?>
            <div class="highlight-card">
                <div class="highlight-tag tag-kuliner"><i class="fa-solid fa-utensils"></i> Kuliner Khas Wonogiri</div>
                <div class="highlight-body">
                    <img src="<?= aman($kulinerUnggulan['gambar_utama']) ?>" alt="<?= aman($kulinerUnggulan['nama_kuliner']) ?>">
                    <div class="highlight-text">
                        <h3>Cita Rasa Nusantara</h3>
                        <p>Nikmati aneka kuliner tradisional yang menggugah selera.</p>
                        <a href="kuliner.php" class="btn btn-orange">Lihat Kuliner <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($destinasiUnggulan): ?>
            <div class="highlight-card">
                <div class="highlight-tag tag-destinasi"><i class="fa-solid fa-mountain-sun"></i> Destinasi Pilihan</div>
                <div class="highlight-body">
                    <img src="<?= aman($destinasiUnggulan['gambar_utama']) ?>" alt="<?= aman($destinasiUnggulan['nama_destinasi']) ?>">
                    <div class="highlight-text">
                        <h3>Pesona Alam Wonogiri</h3>
                        <p>Jelajahi keindahan alam, waduk, bukit, dan air terjun yang memukau.</p>
                        <a href="destinasi.php" class="btn btn-green">Lihat Destinasi <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="highlight-card fact-card">
                <h4>Tahukah Kamu? <i class="fa-regular fa-lightbulb"></i></h4>
                <p>Wonogiri memiliki <?= (int)$totalDestinasi ?> destinasi wisata alam dan <?= (int)$totalKuliner ?> kuliner khas yang wajib dicoba!</p>
                <a href="#" class="link-arrow">Selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- ============ FITUR UTAMA ============ -->
    <section class="features">
        <div class="features-container">
            <div class="feature-item">
                <i class="fa-solid fa-map-location-dot"></i>
                <div>
                    <h4>Peta Interaktif</h4>
                    <p>Temukan lokasi kuliner dan wisata dengan mudah</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-ticket"></i>
                <div>
                    <h4>Rekomendasi</h4>
                    <p>Dapatkan rekomendasi terbaik sesuai minatmu</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-regular fa-comment-dots"></i>
                <div>
                    <h4>Ulasan &amp; Rating</h4>
                    <p>Lihat pengalaman traveler lain sebelum berkunjung</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-camera"></i>
                <div>
                    <h4>Galeri Wonogiri</h4>
                    <p>Abadikan momen terbaikmu di Wonogiri</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-circle-info"></i>
                <div>
                    <h4>Info Praktis</h4>
                    <p>Informasi lengkap untuk perjalananmu</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ KULINER POPULER ============ -->
    <section class="section" id="kuliner-populer">
        <div class="section-header">
            <div>
                <span class="section-tag"><i class="fa-solid fa-utensils"></i> Kuliner Lokal</span>
                <h2>Kuliner Populer di Wonogiri</h2>
            </div>
            <a href="kuliner.php" class="link-arrow">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="card-grid">
            <?php
            $qKulinerList = mysqli_query($koneksi, "SELECT * FROM kuliner WHERE status='aktif' ORDER BY rating DESC LIMIT 4");
            while ($row = mysqli_fetch_assoc($qKulinerList)):
            ?>
            <a href="kuliner-detail.php?slug=<?= aman($row['slug']) ?>" class="info-card">
                <div class="info-card-img">
                    <img src="<?= aman($row['gambar_utama']) ?>" alt="<?= aman($row['nama_kuliner']) ?>">
                    <span class="badge-price"><?= formatRupiah($row['harga_mulai']) ?></span>
                </div>
                <div class="info-card-body">
                    <h3><?= aman($row['nama_kuliner']) ?></h3>
                    <p class="info-card-loc"><i class="fa-solid fa-location-dot"></i> <?= aman($row['lokasi']) ?></p>
                    <div class="info-card-rating">
                        <?= renderBintang($row['rating']) ?>
                        <span><?= $row['rating'] ?> (<?= $row['jumlah_ulasan'] ?>)</span>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- ============ DESTINASI POPULER ============ -->
    <section class="section section-alt" id="destinasi-populer">
        <div class="section-header">
            <div>
                <span class="section-tag tag-green"><i class="fa-solid fa-mountain"></i> Destinasi Wisata</span>
                <h2>Destinasi Favorit Wisatawan</h2>
            </div>
            <a href="destinasi.php" class="link-arrow">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="card-grid">
            <?php
            $qDestinasiList = mysqli_query($koneksi, "SELECT * FROM destinasi_wisata WHERE status='aktif' ORDER BY rating DESC LIMIT 4");
            while ($row = mysqli_fetch_assoc($qDestinasiList)):
            ?>
            <a href="destinasi-detail.php?slug=<?= aman($row['slug']) ?>" class="info-card">
                <div class="info-card-img">
                    <img src="<?= aman($row['gambar_utama']) ?>" alt="<?= aman($row['nama_destinasi']) ?>">
                    <span class="badge-price badge-green"><?= formatRupiah($row['tiket_masuk']) ?></span>
                </div>
                <div class="info-card-body">
                    <h3><?= aman($row['nama_destinasi']) ?></h3>
                    <p class="info-card-loc"><i class="fa-solid fa-location-dot"></i> <?= aman($row['lokasi']) ?></p>
                    <div class="info-card-rating">
                        <?= renderBintang($row['rating']) ?>
                        <span><?= $row['rating'] ?> (<?= $row['jumlah_ulasan'] ?>)</span>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- ============ CTA ============ -->
    <section class="cta">
        <div class="cta-container">
            <h2>Siap Menjelajahi Wonogiri?</h2>
            <p>Mulai rencanakan perjalananmu sekarang dan temukan pengalaman tak terlupakan.</p>
            <div class="cta-buttons">
                <a href="kuliner.php" class="btn btn-orange">Eksplor Kuliner <i class="fa-solid fa-utensils"></i></a>
                <a href="destinasi.php" class="btn btn-green-outline">Eksplor Wisata <i class="fa-solid fa-mountain"></i></a>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
