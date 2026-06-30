<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'tentang';
$judul_halaman = 'Tentang Wonogiri';
require_once 'includes/bps_api.php';

$bps = getBpsWonogiri();



require_once 'includes/header.php';
?>

<style>
/* ===== TENTANG PAGE ===== */
.ttg-hero {
    background: #1b4332;
    color: #fff;
    padding: 5rem 2rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.ttg-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 250px; height: 250px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.ttg-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: -40px;
    width: 180px; height: 180px;
    background: rgba(82,183,136,0.06);
    border-radius: 50%;
}
.ttg-hero-eyebrow {
    font-size: 12px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #52b788;
    font-weight: 700;
    margin-bottom: 12px;
}
.ttg-hero h1 {
    font-size: 2.6rem;
    font-weight: 800;
    margin-bottom: 12px;
    line-height: 1.2;
}
.ttg-hero h1 span { color: #52b788; }
.ttg-hero p {
    font-size: 15px;
    color: #b7e4c7;
    max-width: 500px;
    margin: 0 auto 2.5rem;
    line-height: 1.7;
}
.ttg-stats {
    display: flex;
    justify-content: center;
    gap: 0;
    margin-top: 0.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 0;
    flex-wrap: wrap;
}
.ttg-stat {
    flex: 1;
    min-width: 160px;
    text-align: center;
    padding: 1.5rem 1rem;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.ttg-stat:last-child { border-right: none; }
.ttg-stat-num {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}
.ttg-stat-label {
    font-size: 12px;
    color: #95d5b2;
    margin-top: 4px;
}

/* NAMA SECTION */
.nama-section {
    background: #f0faf4;
    padding: 3rem 2rem;
    text-align: center;
}
.nama-inner { max-width: 680px; margin: 0 auto; }
.nama-eyebrow {
    font-size: 11px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: #40916c;
    font-weight: 700;
    margin-bottom: 6px;
}
.nama-title { font-size: 1.4rem; font-weight: 800; color: #1b4332; margin-bottom: 1.5rem; }
.nama-derivation {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.2rem;
}
.nama-box {
    background: #fff;
    border: 2px solid #b7e4c7;
    border-radius: 14px;
    padding: 1rem 1.8rem;
    min-width: 130px;
}
.nama-box.active {
    background: #2d6a4f;
    border-color: #2d6a4f;
}
.nama-kata {
    font-size: 1.8rem;
    font-weight: 800;
    color: #2d6a4f;
}
.nama-box.active .nama-kata { color: #fff; }
.nama-arti {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}
.nama-box.active .nama-arti { color: #b7e4c7; }
.nama-plus { font-size: 1.8rem; color: #95d5b2; font-weight: 300; }
.nama-desc { font-size: 14px; color: #555; line-height: 1.7; }

/* MAIN */
.ttg-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem 2rem;
}

.ttg-eyebrow {
    font-size: 11px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: #40916c;
    font-weight: 700;
    margin-bottom: 6px;
}
.ttg-heading {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1b4332;
    margin-bottom: 6px;
}
.ttg-subheading {
    font-size: 14px;
    color: #666;
    margin-bottom: 2rem;
}

/* SEJARAH */
.sejarah-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    padding: 2rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.sejarah-left h3 {
    font-size: 16px;
    font-weight: 800;
    color: #1b4332;
    margin-bottom: 10px;
}
.sejarah-left p {
    font-size: 14px;
    color: #555;
    line-height: 1.8;
    margin-bottom: 10px;
}
.sejarah-quote {
    background: #f0faf4;
    border-left: 3px solid #2d6a4f;
    border-radius: 0 10px 10px 0;
    padding: 12px 16px;
    margin-top: 14px;
}
.sejarah-quote p {
    font-size: 15px;
    font-style: italic;
    color: #2d6a4f;
    font-weight: 700;
    margin: 0 0 4px;
}
.sejarah-quote span { font-size: 12px; color: #888; }

/* TIMELINE */
.ttg-timeline { list-style: none; padding: 0; }
.tl-item {
    display: flex;
    gap: 16px;
    margin-bottom: 18px;
    position: relative;
}
.tl-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 18px;
    height: calc(100% + 2px);
    width: 2px;
    background: #d8f3dc;
}
.tl-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    background: #2d6a4f;
    margin-top: 4px;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.tl-content strong {
    font-size: 13px;
    font-weight: 700;
    color: #1b4332;
    display: block;
    margin-bottom: 2px;
}
.tl-content span {
    font-size: 12.5px;
    color: #666;
    line-height: 1.5;
}

/* FAKTA */
.fakta-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2.5rem;
}
.fakta-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 1.3rem;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: transform 0.2s;
}
.fakta-card:hover { transform: translateY(-2px); }
.fakta-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.fakta-icon.green  { background: #d8f3dc; color: #2d6a4f; }
.fakta-icon.amber  { background: #fff8e1; color: #d97706; }
.fakta-icon.blue   { background: #e0f2fe; color: #0284c7; }
.fakta-icon.red    { background: #ffe4e6; color: #e11d48; }
.fakta-icon.purple { background: #f3e8ff; color: #7c3aed; }
.fakta-icon.teal   { background: #ccfbf1; color: #0d9488; }
.fakta-card h4 { font-size: 13.5px; font-weight: 700; color: #1b4332; margin: 0 0 5px; }
.fakta-card p { font-size: 12.5px; color: #666; line-height: 1.5; margin: 0; }

/* BUDAYA */
.budaya-section {
    background: linear-gradient(135deg, #1b4332, #2d6a4f);
    border-radius: 18px;
    padding: 2.5rem;
    margin-bottom: 2.5rem;
    color: #fff;
}
.budaya-section .ttg-eyebrow { color: #52b788; }
.budaya-section .ttg-heading { color: #fff; margin-bottom: 1.5rem; }
.budaya-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.budaya-item {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 12px;
    padding: 1.2rem;
}
.budaya-item-icon { font-size: 1.6rem; margin-bottom: 8px; }
.budaya-item h4 { font-size: 14px; font-weight: 700; color: #fff; margin: 0 0 6px; }
.budaya-item p { font-size: 12.5px; color: #b7e4c7; line-height: 1.5; margin: 0; }

/* BATAS WILAYAH */
.batas-section-alt {
    background: #f8f9fa;
    padding: 3rem 0;
}
.batas-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 2rem;
}
.batas-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 1.8rem;
}
.batas-card h3 {
    font-size: 15px;
    font-weight: 800;
    color: #1b4332;
    margin-bottom: 1.2rem;
}
.batas-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.batas-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f0faf4;
    border: 1px solid #d8f3dc;
    border-radius: 12px;
    padding: 12px 16px;
}
.batas-item i { font-size: 18px; color: #2d6a4f; flex-shrink: 0; }
.batas-item strong { font-size: 13px; color: #1b4332; display: block; }
.batas-item span { font-size: 12.5px; color: #555; }

/* CTA */
.ttg-cta {
    background: #1b4332;
    text-align: center;
    padding: 4rem 2rem;
    color: #fff;
}
.ttg-cta h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 12px; }
.ttg-cta p { color: #95d5b2; font-size: 15px; margin-bottom: 1.8rem; }
.ttg-cta-btns { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }

@media (max-width: 768px) {
    .ttg-hero h1 { font-size: 1.8rem; }
    .sejarah-wrap { grid-template-columns: 1fr; }
    .fakta-grid { grid-template-columns: 1fr 1fr; }
    .budaya-grid { grid-template-columns: 1fr; }
    .batas-grid { grid-template-columns: 1fr; }
    .ttg-stat { min-width: 130px; }
}
@media (max-width: 480px) {
    .fakta-grid { grid-template-columns: 1fr; }
    .nama-derivation { flex-direction: column; align-items: center; }
}
</style>

<main>

<section class="ttg-hero">
    <p class="ttg-hero-eyebrow">Mengenal Lebih Dekat</p>
    <h1>Tentang <span>Wonogiri</span></h1>
    <p>Kota Gaplek yang kaya sejarah, budaya, dan alam — diapit Jawa Tengah dan Yogyakarta di selatan Solo.</p>
    
    <div class="ttg-stats">
        <div class="ttg-stat">
            <div class="ttg-stat-num">182K</div>
            <div class="ttg-stat-label">Hektar Luas Wilayah</div>
        </div>
        <div class="ttg-stat">
            <div class="ttg-stat-num">1,04 Jt</div>
            <div class="ttg-stat-label">Jiwa Penduduk</div>
        </div>
       <div class="ttg-stat">
    <div class="ttg-stat-num">
        <?= htmlspecialchars($bps['tampil']); ?>
    </div>
    <div class="ttg-stat-label">Perjalanan Wisata</div>
</div>
        <div class="ttg-stat">
            <div class="ttg-stat-num">25</div>
            <div class="ttg-stat-label">Kecamatan</div>
        </div>
        <div class="ttg-stat">
            <div class="ttg-stat-num">1723</div>
            <div class="ttg-stat-label">Tahun Berdiri</div>
        </div>
    </div>
</section>

<section class="nama-section">
    <div class="nama-inner">
        <p class="nama-eyebrow">Etimologi Nama</p>
        <h2 class="nama-title">Asal Usul Nama Wonogiri</h2>
        <div class="nama-derivation">
            <div class="nama-box">
                <div class="nama-kata">Wana</div>
                <div class="nama-arti">Hutan / Sawah / Ladang</div>
            </div>
            <span class="nama-plus">+</span>
            <div class="nama-box">
                <div class="nama-kata">Giri</div>
                <div class="nama-arti">Gunung / Pegunungan</div>
            </div>
            <span class="nama-plus">=</span>
            <div class="nama-box active">
                <div class="nama-kata">Wonogiri</div>
                <div class="nama-arti">Daerah Hutan Pegunungan</div>
            </div>
        </div>
        <p class="nama-desc">Nama Wonogiri berasal dari bahasa Jawa yang menggambarkan wilayah ini — didominasi sawah, hutan tropis, dan pegunungan. Nama ini mencerminkan identitas geografis yang kuat dan keindahan alam khas Wonogiri.</p>
    </div>
</section>

<div class="ttg-main">
    <p class="ttg-eyebrow">Latar Belakang</p>
    <h2 class="ttg-heading">Sejarah Singkat Wonogiri</h2>
    <p class="ttg-subheading">Dari perjuangan Pangeran Sambernyowo hingga Kabupaten yang berkembang</p>

    <div class="sejarah-wrap">
        <div class="sejarah-left">
            <h3>Pangeran Sambernyowo & Lahirnya Wonogiri</h3>
            <p>Kabupaten Wonogiri lahir dari perjuangan Raden Mas Said (Pangeran Sambernyowo), yang menjadikan wilayah ini sebagai basis perlawanan terhadap penjajah Belanda pada awal abad ke-18.</p>
            <p>Raden Mas Said lahir di Kartasura pada 8 April 1725. Semasa hidupnya ia memimpin lebih dari 250 pertempuran. Perjuangannya dimulai di Dusun Nglaroh, Kecamatan Selogiri — tempat ia menyusun strategi menggunakan batu yang kini dikenal sebagai <strong>Watu Gilang</strong>.</p>
            <div class="sejarah-quote">
                <p>"Kawulo Gusti" — Pamoring Kawulo Gusti</p>
                <span>Semboyan perang Raden Mas Said, ikrar sehidup semati bersama pengikutnya</span>
            </div>
        </div>
        <div>
            <ul class="ttg-timeline">
                <li class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <strong>8 April 1725</strong>
                        <span>Raden Mas Said (Pangeran Sambernyowo) lahir di Kartasura, kelak menjadi tokoh pendiri Wonogiri</span>
                    </div>
                </li>
                <li class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <strong>Awal 1700-an</strong>
                        <span>Basis perjuangan di Dusun Nglaroh, Selogiri dibentuk. Watu Gilang menjadi simbol perlawanan dan strategi perang</span>
                    </div>
                </li>
                <li class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <strong>250+ Pertempuran</strong>
                        <span>Raden Mas Said dikenal sebagai panglima yang tangguh, memimpin ratusan pertempuran melawan penjajah</span>
                    </div>
                </li>
                <li class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <strong>Perjanjian Salatiga</strong>
                        <span>Raden Mas Said diangkat sebagai KGPAA Mangkunegoro I; wilayah Wonogiri ditetapkan resmi sebagai daerahnya</span>
                    </div>
                </li>
                <li class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <strong>Hari Ini</strong>
                        <span>Wonogiri berkembang menjadi kabupaten wisata dengan 25 kecamatan, 1,04 juta jiwa, dan luas 182.236 hektar</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <p class="ttg-eyebrow">7 Fakta Menarik</p>
    <h2 class="ttg-heading">Hal Unik tentang Wonogiri</h2>
    <p class="ttg-subheading">Fakta-fakta yang membuat Wonogiri begitu istimewa</p>

    <div class="fakta-grid">
        <div class="fakta-card">
            <div class="fakta-icon green"><i class="fa-solid fa-map-location-dot"></i></div>
            <div>
                <h4>Daerah Strategis</h4>
                <p>Diapit Jawa Timur dan DIY Yogyakarta, Wonogiri memungkinkan interaksi budaya dan ekonomi lintas provinsi yang kaya.</p>
            </div>
        </div>
        <div class="fakta-card">
            <div class="fakta-icon amber"><i class="fa-solid fa-wheat-awn"></i></div>
            <div>
                <h4>Kota Gaplek</h4>
                <p>Julukan ikonik dari tradisi menanam singkong di kawasan karst selatan yang tidak cocok untuk padi — melahirkan kuliner khas Nasi Tiwul.</p>
            </div>
        </div>
        <div class="fakta-card">
            <div class="fakta-icon red"><i class="fa-solid fa-masks-theater"></i></div>
            <div>
                <h4>Tari Kethek Ogleng</h4>
                <p>Ikon budaya Wonogiri — tarian meniru gerak kera yang diciptakan Darjino, disempurnakan Suwiryo, kini warisan rakyat Wonogiri.</p>
            </div>
        </div>
        <div class="fakta-card">
            <div class="fakta-icon blue"><i class="fa-solid fa-industry"></i></div>
            <div>
                <h4>Kawasan Industri Selatan</h4>
                <p>Wilayah Giritontro, Giriwoyo, dan Pracimatoro ditetapkan sebagai kawasan industri pertambangan dalam RTRW 2020–2040.</p>
            </div>
        </div>
        <div class="fakta-card">
            <div class="fakta-icon purple"><i class="fa-solid fa-users"></i></div>
            <div>
                <h4>1,04 Juta Penduduk</h4>
                <p>Berdasarkan Sensus BPS 2020. Kecamatan Wonogiri adalah yang terpadat; Paranggupito yang paling sedikit penduduknya.</p>
            </div>
        </div>
        <div class="fakta-card">
            <div class="fakta-icon teal"><i class="fa-solid fa-mountain"></i></div>
            <div>
                <h4>5,52% Luas Jawa Tengah</h4>
                <p>Dengan luas 182.236 hektar, Wonogiri menjadi salah satu kabupaten terluas di Provinsi Jawa Tengah.</p>
            </div>
        </div>
    </div>

    <div class="budaya-section">
        <p class="ttg-eyebrow">Seni & Budaya</p>
        <h2 class="ttg-heading">Warisan Budaya Wonogiri</h2>
        <div class="budaya-grid">
            <div class="budaya-item">
                <div class="budaya-item-icon">🎭</div>
                <h4>Tari Kethek Ogleng</h4>
                <p>Diciptakan Darjino dan disempurnakan Suwiryo — kini ikon pariwisata dan seni rakyat pasca panen yang digemari masyarakat luas.</p>
            </div>
            <div class="budaya-item">
                <div class="budaya-item-icon">🍚</div>
                <h4>Nasi Tiwul</h4>
                <p>Kuliner tradisional dari singkong kering yang dikukus — cerminan ketangguhan masyarakat karst Wonogiri dalam beradaptasi dengan alam.</p>
            </div>
            <div class="budaya-item">
                <div class="budaya-item-icon">🪨</div>
                <h4>Watu Gilang, Selogiri</h4>
                <p>Situs bersejarah tempat Raden Mas Said menyusun strategi perlawanan. Kini menjadi destinasi wisata sejarah yang berharga.</p>
            </div>
            <div class="budaya-item">
                <div class="budaya-item-icon">⚔️</div>
                <h4>Semboyan "Kawulo Gusti"</h4>
                <p>Filosofi hidup yang diwariskan Pangeran Sambernyowo — mencerminkan kesetiaan dan tekad kuat masyarakat Wonogiri hingga hari ini.</p>
            </div>
        </div>
    </div>
</div>

<section class="batas-section-alt">
    <div class="batas-inner">
        <p class="ttg-eyebrow">Letak Geografis</p>
        <h2 class="ttg-heading">Batas Wilayah Wonogiri</h2>
        <p class="ttg-subheading" style="margin-bottom:1.5rem;">Wonogiri berjarak 32 km selatan Kota Solo, luas 182.236 hektar (5,52% Jawa Tengah)</p>
        <div class="batas-card">
            <div class="batas-grid">
                <div class="batas-item">
                    <i class="fa-solid fa-arrow-up"></i>
                    <div>
                        <strong>Utara</strong>
                        <span>Kab. Karanganyar & Kab. Sukoharjo</span>
                    </div>
                </div>
                <div class="batas-item">
                    <i class="fa-solid fa-arrow-right"></i>
                    <div>
                        <strong>Timur</strong>
                        <span>Kab. Ponorogo, Magetan & Pacitan (Jawa Timur)</span>
                    </div>
                </div>
                <div class="batas-item">
                    <i class="fa-solid fa-arrow-down"></i>
                    <div>
                        <strong>Selatan</strong>
                        <span>Samudera Indonesia (Pantai Selatan)</span>
                    </div>
                </div>
                <div class="batas-item">
                    <i class="fa-solid fa-arrow-left"></i>
                    <div>
                        <strong>Barat</strong>
                        <span>Provinsi DIY Yogyakarta</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ttg-cta">
    <h2>Siap Menjelajahi Wonogiri?</h2>
    <p>Temukan destinasi wisata, kuliner lokal, dan informasi perjalanan lengkap di sini.</p>
    <div class="ttg-cta-btns">
        <a href="destinasi.php" class="btn btn-orange">Jelajahi Destinasi <i class="fa-solid fa-mountain"></i></a>
        <a href="kuliner.php" class="btn btn-green-outline" style="border-color:#52b788; color:#52b788;">Jelajahi Kuliner <i class="fa-solid fa-utensils"></i></a>
        <a href="informasi.php" class="btn" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3);">Info Perjalanan <i class="fa-solid fa-circle-info"></i></a>
    </div>
</section>

</main>

<?php require_once 'includes/footer.php'; ?>