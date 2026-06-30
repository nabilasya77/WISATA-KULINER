<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'informasi';
$judul_halaman = 'Informasi';

require_once 'includes/header.php';
?>

<style>
/* ===== INFORMASI PAGE ===== */
.page-hero {
    background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 60%, #40916c 100%);
    color: #fff;
    padding: 5rem 2rem 4rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.page-hero::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.page-hero-eyebrow {
    font-size: 12px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #95d5b2;
    margin-bottom: 12px;
    font-weight: 600;
}
.page-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 12px;
    line-height: 1.2;
}
.page-hero p {
    font-size: 15px;
    color: #d8f3dc;
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.7;
}
.page-hero-tabs {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 2rem;
}
.page-hero-tab {
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    padding: 8px 18px;
    border-radius: 24px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
}
.page-hero-tab:hover {
    background: #fff;
    color: #2d6a4f;
}

/* SECTION */
.info-section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem 2rem;
}
.info-section + .info-section {
    padding-top: 0;
}
.info-section-alt {
    background: #f0faf4;
    padding: 3rem 0;
}
.info-section-alt .info-section {
    padding-top: 0;
    padding-bottom: 0;
}
.info-section-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem 2rem;
}

.info-eyebrow {
    font-size: 11px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: #40916c;
    font-weight: 700;
    margin-bottom: 6px;
}
.info-heading {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1b4332;
    margin-bottom: 6px;
}
.info-subheading {
    font-size: 14px;
    color: #666;
    margin-bottom: 2rem;
}

/* TRANSPORT */
.transport-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem;
    margin-bottom: 1rem;
}
.transport-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.transport-header {
    background: #1b4332;
    color: #fff;
    padding: 1rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.transport-header i {
    font-size: 22px;
    color: #52b788;
}
.transport-header h3 {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 2px;
}
.transport-header p {
    font-size: 12px;
    color: #95d5b2;
    margin: 0;
}
.transport-body {
    padding: 1.3rem;
}
.transport-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 12px;
}
.transport-item:last-child { margin-bottom: 0; }
.t-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #2d6a4f;
    margin-top: 7px;
    flex-shrink: 0;
}
.transport-item p {
    font-size: 13.5px;
    color: #444;
    line-height: 1.6;
    margin: 0;
}
.transport-item strong { color: #1b4332; }
.price-badge {
    display: inline-block;
    background: #d8f3dc;
    color: #2d6a4f;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 12px;
    margin-left: 4px;
    vertical-align: middle;
}

/* TIPS */
.tips-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 16px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}
.tips-box-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.2rem;
}
.tips-box-header i { font-size: 20px; color: #d97706; }
.tips-box-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: #92400e;
    margin: 0;
}
.tips-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fff;
    border-radius: 10px;
    padding: 10px 12px;
    border: 1px solid #fde68a;
}
.tip-item i { font-size: 16px; color: #d97706; margin-top: 2px; flex-shrink: 0; }
.tip-item p { font-size: 13px; color: #555; line-height: 1.5; margin: 0; }
.tip-item strong { color: #92400e; }

/* KULINER */
.kuliner-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
.kuliner-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 1.2rem;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.kuliner-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(45,106,79,0.1);
}
.kuliner-emoji { font-size: 2.2rem; flex-shrink: 0; }
.kuliner-card h4 { font-size: 14px; font-weight: 700; color: #1b4332; margin: 0 0 4px; }
.kuliner-card p { font-size: 12.5px; color: #666; line-height: 1.5; margin: 0; }
.kuliner-tag {
    display: inline-block;
    background: #fff8e1;
    color: #d97706;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 10px;
    margin-top: 8px;
}

/* PENGALAMAN */
.pengalaman-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.pengalaman-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.pengalaman-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(45,106,79,0.12);
}
.pengalaman-img {
    height: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}
.pg-alam     { background: linear-gradient(135deg, #d8f3dc, #b7e4c7); }
.pg-budaya   { background: linear-gradient(135deg, #fff8e1, #fde68a); }
.pg-kuliner  { background: linear-gradient(135deg, #ffe4e6, #fecdd3); }
.pg-petualangan { background: linear-gradient(135deg, #e0f2fe, #bae6fd); }
.pengalaman-body { padding: 1rem; }
.pengalaman-body h4 { font-size: 14px; font-weight: 700; color: #1b4332; margin: 0 0 5px; }
.pengalaman-body p { font-size: 12.5px; color: #666; line-height: 1.5; margin: 0; }
.star-row { display: flex; align-items: center; gap: 4px; margin-top: 8px; }
.star-row i { font-size: 12px; color: #f59e0b; }
.star-row span { font-size: 12px; color: #888; }
.pg-tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 10px;
    margin-top: 6px;
}
.pg-tag.green  { background: #d8f3dc; color: #2d6a4f; }
.pg-tag.amber  { background: #fff8e1; color: #d97706; }
.pg-tag.red    { background: #ffe4e6; color: #e11d48; }
.pg-tag.blue   { background: #e0f2fe; color: #0284c7; }

/* INFO PRAKTIS */
.praktis-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
.praktis-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 1.3rem;
    transition: transform 0.2s;
}
.praktis-card:hover { transform: translateY(-2px); }
.praktis-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}
.praktis-icon.green  { background: #d8f3dc; color: #2d6a4f; }
.praktis-icon.amber  { background: #fff8e1; color: #d97706; }
.praktis-icon.blue   { background: #e0f2fe; color: #0284c7; }
.praktis-icon.red    { background: #ffe4e6; color: #e11d48; }
.praktis-icon.purple { background: #f3e8ff; color: #7c3aed; }
.praktis-icon.teal   { background: #ccfbf1; color: #0d9488; }
.praktis-card h4 { font-size: 14px; font-weight: 700; color: #1b4332; margin: 0 0 6px; }
.praktis-card p { font-size: 13px; color: #555; line-height: 1.6; margin: 0; }

/* FOOTER CTA */
.info-cta {
    background: #1b4332;
    color: #fff;
    text-align: center;
    padding: 3rem 2rem;
}
.info-cta h2 { font-size: 1.6rem; font-weight: 800; margin-bottom: 10px; }
.info-cta p { color: #95d5b2; font-size: 15px; margin-bottom: 1.5rem; }
.info-cta-buttons { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }

@media (max-width: 768px) {
    .transport-grid { grid-template-columns: 1fr; }
    .kuliner-grid { grid-template-columns: 1fr 1fr; }
    .pengalaman-grid { grid-template-columns: 1fr 1fr; }
    .tips-grid { grid-template-columns: 1fr; }
    .praktis-grid { grid-template-columns: 1fr 1fr; }
    .page-hero h1 { font-size: 1.8rem; }
}
@media (max-width: 480px) {
    .kuliner-grid { grid-template-columns: 1fr; }
    .pengalaman-grid { grid-template-columns: 1fr; }
    .praktis-grid { grid-template-columns: 1fr; }
}
</style>

<main>

<!-- HERO -->
<section class="page-hero">
    <p class="page-hero-eyebrow">Panduan Lengkap Wisata</p>
    <h1>Informasi Wonogiri</h1>
    <p>Semua yang perlu kamu tahu sebelum mengunjungi Wonogiri — transportasi, kuliner, tips, dan jenis pengalaman wisata.</p>
    <div class="page-hero-tabs">
        <a href="#transportasi" class="page-hero-tab"><i class="fa-solid fa-train"></i> Transportasi</a>
        <a href="#kuliner" class="page-hero-tab"><i class="fa-solid fa-utensils"></i> Kuliner Wajib</a>
        <a href="#pengalaman" class="page-hero-tab"><i class="fa-solid fa-map-location-dot"></i> Pengalaman Wisata</a>
        <a href="#praktis" class="page-hero-tab"><i class="fa-solid fa-circle-info"></i> Info Praktis</a>
    </div>
</section>

<!-- TRANSPORTASI -->
<section id="transportasi">
<div class="info-section">
    <p class="info-eyebrow">Cara Menuju Wonogiri</p>
    <h2 class="info-heading">Pilihan Transportasi</h2>
    <p class="info-subheading">Wonogiri mudah dijangkau dari Solo dan kota-kota sekitarnya</p>

    <div class="transport-grid">
        <!-- Kereta -->
        <div class="transport-card">
            <div class="transport-header">
                <i class="fa-solid fa-train"></i>
                <div>
                    <h3>Kereta Batara Kresna</h3>
                    <p>Dari Stasiun Purwosari, Solo</p>
                </div>
            </div>
            <div class="transport-body">
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Harga tiket <strong>Rp4.000</strong> <span class="price-badge">Sangat Terjangkau</span></p>
                </div>
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Durasi perjalanan sekitar <strong>1 jam 45 menit</strong></p>
                </div>
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Berangkat dari <strong>Stasiun Purwosari</strong>, tiba di <strong>Stasiun Wonogiri</strong></p>
                </div>
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Pasar kuliner Nasi Tiwul tepat di depan <strong>Stasiun Wonogiri</strong></p>
                </div>
            </div>
        </div>

        <!-- Bus -->
        <div class="transport-card">
            <div class="transport-header">
                <i class="fa-solid fa-bus"></i>
                <div>
                    <h3>Bus & Transportasi Lokal</h3>
                    <p>Dari Terminal Tirtonadi, Solo</p>
                </div>
            </div>
            <div class="transport-body">
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Bus antar kota dari <strong>Terminal Tirtonadi</strong> tersedia setiap hari</p>
                </div>
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p><strong>Batik Solo Trans (BST)</strong> menghubungkan kawasan Solo Raya</p>
                </div>
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Di dalam kota: <strong>angkutan umum lokal</strong> dan ojek tersedia luas</p>
                </div>
                <div class="transport-item">
                    <div class="t-dot"></div>
                    <p>Jarak dari Solo: <strong>±32 km</strong> ke arah selatan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="tips-box">
        <div class="tips-box-header">
            <i class="fa-solid fa-lightbulb"></i>
            <h3>Tips Perjalanan ke Wonogiri</h3>
        </div>
        <div class="tips-grid">
            <div class="tip-item">
                <i class="fa-solid fa-calendar-days"></i>
                <p>Waktu terbaik berkunjung: <strong>April–Oktober</strong> (musim kemarau, cuaca cerah)</p>
            </div>
            <div class="tip-item">
                <i class="fa-solid fa-camera"></i>
                <p>Bawa kamera untuk spot foto epik di <strong>Waduk Gajah Mungkur</strong> dan perbukitan karst</p>
            </div>
            <div class="tip-item">
                <i class="fa-solid fa-wallet"></i>
                <p>Siapkan <strong>uang tunai</strong> — beberapa warung dan objek wisata belum menerima QRIS</p>
            </div>
            <div class="tip-item">
                <i class="fa-solid fa-shoe-prints"></i>
                <p>Gunakan <strong>alas kaki nyaman</strong> untuk trekking ke destinasi alam dan goa</p>
            </div>
            <div class="tip-item">
                <i class="fa-solid fa-clock"></i>
                <p>Berangkat <strong>pagi dari Solo</strong> agar punya waktu penuh menjelajahi Wonogiri</p>
            </div>
            <div class="tip-item">
                <i class="fa-solid fa-signal"></i>
                <p>Sinyal di <strong>daerah selatan</strong> bisa lemah — unduh peta offline sebelum pergi</p>
            </div>
        </div>
    </div>
</div>
</section>

<!-- KULINER WAJIB -->
<section id="kuliner" class="info-section-alt">
<div class="info-section-inner">
    <p class="info-eyebrow">Kuliner Khas Wonogiri</p>
    <h2 class="info-heading">Wajib Dicoba Saat Berkunjung</h2>
    <p class="info-subheading">Cita rasa otentik yang hanya bisa kamu temukan di sini</p>

    <div class="kuliner-grid">
        <div class="kuliner-card">
            <span class="kuliner-emoji">🍚</span>
            <div>
                <h4>Nasi Tiwul</h4>
                <p>Makanan khas dari singkong kering yang dikukus, disajikan dengan parutan kelapa atau pecel. Ikon kuliner Wonogiri yang tak boleh dilewati.</p>
                <span class="kuliner-tag">📍 Pasar Wonogiri</span>
            </div>
        </div>
        <div class="kuliner-card">
            <span class="kuliner-emoji">🍜</span>
            <div>
                <h4>Bakso Titoti</h4>
                <p>Terkenal dengan kuah kaldu sapi yang gurih dan berbagai pilihan bakso. Favorit warga lokal yang sudah legendaris.</p>
                <span class="kuliner-tag">⭐ Favorit Lokal</span>
            </div>
        </div>
        <div class="kuliner-card">
            <span class="kuliner-emoji">🥮</span>
            <div>
                <h4>Olahan Gaplek</h4>
                <p>Beragam kudapan berbahan singkong khas kawasan karst selatan — camilan tradisional unik yang mencerminkan kearifan lokal Wonogiri.</p>
                <span class="kuliner-tag">🌿 Tradisional</span>
            </div>
        </div>
    </div>

    <div style="text-align:center; margin-top: 1.5rem;">
        <a href="kuliner.php" class="btn btn-green">Lihat Semua Kuliner <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
</section>

<!-- PENGALAMAN WISATA -->
<section id="pengalaman">
<div class="info-section">
    <p class="info-eyebrow">Sub-kluster Pengalaman</p>
    <h2 class="info-heading">Jenis Pengalaman Wisata</h2>
    <p class="info-subheading">Pilih pengalaman yang paling cocok dengan gaya perjalananmu</p>

    <div class="pengalaman-grid">
        <div class="pengalaman-card">
            <div class="pengalaman-img pg-alam">🏞️</div>
            <div class="pengalaman-body">
                <h4>Wisata Alam</h4>
                <p>Waduk Gajah Mungkur, perbukitan karst, Pantai Sembukan, goa eksotis di selatan.</p>
                <div class="star-row">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                    <span>4.5 · Populer</span>
                </div>
                <span class="pg-tag green">Alam Terbuka</span>
            </div>
        </div>
        <div class="pengalaman-card">
            <div class="pengalaman-img pg-budaya">🎭</div>
            <div class="pengalaman-body">
                <h4>Wisata Budaya</h4>
                <p>Tari Kethek Ogleng, Museum Sewu Rai, situs bersejarah Pangeran Sambernyowo.</p>
                <div class="star-row">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>
                    <span>4.0 · Edukatif</span>
                </div>
                <span class="pg-tag amber">Budaya & Sejarah</span>
            </div>
        </div>
        <div class="pengalaman-card">
            <div class="pengalaman-img pg-kuliner">🍽️</div>
            <div class="pengalaman-body">
                <h4>Wisata Kuliner</h4>
                <p>Pasar Wonogiri, warung tiwul otentik, bakso lokal, jajanan pasar tradisional.</p>
                <div class="star-row">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    <span>5.0 · Must Try</span>
                </div>
                <span class="pg-tag red">Kuliner Lokal</span>
            </div>
        </div>
        <div class="pengalaman-card">
            <div class="pengalaman-img pg-petualangan">🧗</div>
            <div class="pengalaman-body">
                <h4>Petualangan</h4>
                <p>Hiking perbukitan, susur goa karst, body rafting sungai bawah tanah yang mendebarkan.</p>
                <div class="star-row">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>
                    <span>4.2 · Seru</span>
                </div>
                <span class="pg-tag blue">Petualangan</span>
            </div>
        </div>
    </div>
</div>
</section>

<!-- INFO PRAKTIS -->
<section id="praktis" class="info-section-alt">
<div class="info-section-inner">
    <p class="info-eyebrow">Sebelum Kamu Pergi</p>
    <h2 class="info-heading">Informasi Praktis</h2>
    <p class="info-subheading">Hal-hal penting yang perlu kamu ketahui</p>

    <div class="praktis-grid">
        <div class="praktis-card">
            <div class="praktis-icon green"><i class="fa-solid fa-cloud-sun"></i></div>
            <h4>Cuaca & Musim</h4>
            <p>Musim kemarau (April–Oktober) adalah waktu terbaik. Suhu rata-rata 24–32°C. Hindari musim hujan (Nov–Mar) untuk destinasi alam terbuka.</p>
        </div>
        <div class="praktis-card">
            <div class="praktis-icon amber"><i class="fa-solid fa-money-bill-wave"></i></div>
            <h4>Anggaran Perjalanan</h4>
            <p>Budget harian mulai Rp100.000–250.000 sudah cukup untuk makan, transportasi lokal, dan tiket masuk destinasi wisata.</p>
        </div>
        <div class="praktis-card">
            <div class="praktis-icon blue"><i class="fa-solid fa-hospital"></i></div>
            <h4>Fasilitas Kesehatan</h4>
            <p>RSUD Wonogiri dan beberapa puskesmas tersebar di kecamatan. Simpan nomor darurat: 119 (ambulans) dan 110 (polisi).</p>
        </div>
        <div class="praktis-card">
            <div class="praktis-icon red"><i class="fa-solid fa-bed"></i></div>
            <h4>Penginapan</h4>
            <p>Tersedia hotel, guest house, dan homestay mulai Rp150.000/malam. Direkomendasikan pesan lebih awal saat akhir pekan dan libur panjang.</p>
        </div>
        <div class="praktis-card">
            <div class="praktis-icon purple"><i class="fa-solid fa-wifi"></i></div>
            <h4>Sinyal & Internet</h4>
            <p>Sinyal 4G cukup baik di pusat kota. Daerah perbukitan selatan dan goa bisa blank spot — unduh peta & informasi offline terlebih dahulu.</p>
        </div>
        <div class="praktis-card">
            <div class="praktis-icon teal"><i class="fa-solid fa-ticket"></i></div>
            <h4>Tiket Masuk</h4>
            <p>Tiket wisata alam mulai Rp5.000–25.000. Beberapa destinasi gratis. Bawa uang tunai karena belum semua menyediakan pembayaran digital.</p>
        </div>
    </div>
</div>
</section>

<!-- CTA -->
<section class="info-cta">
    <h2>Siap Menjelajahi Wonogiri?</h2>
    <p>Temukan destinasi wisata dan kuliner terbaik Wonogiri sekarang juga.</p>
    <div class="info-cta-buttons">
        <a href="destinasi.php" class="btn btn-orange">Eksplor Wisata <i class="fa-solid fa-mountain"></i></a>
        <a href="kuliner.php" class="btn btn-green-outline" style="border-color:#52b788; color:#52b788;">Eksplor Kuliner <i class="fa-solid fa-utensils"></i></a>
    </div>
</section>

</main>

<?php require_once 'includes/footer.php'; ?>