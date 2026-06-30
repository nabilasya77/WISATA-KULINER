<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$halaman_aktif = $halaman_aktif ?? 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($judul_halaman) ? htmlspecialchars($judul_halaman) . ' - Wonogiri' : 'Wonogiri - Rasa, Budaya, Alam yang Menginspirasi' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Memaksa elemen Navbar sejajar 1 baris (mengabaikan CSS bawaan jika ada bentrok) */
        .navbar-container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important; /* Kunci agar tidak turun ke bawah */
            width: 100%;
            gap: 20px;
        }

        .nav-menu {
            display: flex !important;
            align-items: center !important;
            gap: 20px;
            white-space: nowrap !important; /* Kunci agar teks menu tidak patah */
        }

        .navbar-actions {
            display: flex !important;
            align-items: center !important;
            gap: 15px;
            white-space: nowrap !important;
        }

        /* Menyembunyikan elemen bawaan Google Translate */
        .goog-te-banner-frame, .skiptranslate { display: none !important; }
        body { top: 0 !important; }
        .goog-tooltip, .goog-tooltip:hover { display: none !important; }
        .goog-text-highlight { background: none !important; box-shadow: none !important; }
        
        /* Styling Profil User & Button */
        .auth-btn { background: #2e7d32; color: white; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.3s; display: inline-flex; align-items: center; gap: 5px; }
        .auth-btn:hover { background: #1b5e20; color: white; }
        .user-greeting { font-weight: 600; font-size: 14px; color: #2e7d32; display: inline-flex; align-items: center; gap: 6px; }
        .user-menu-block { display: flex; align-items: center; gap: 12px; border-left: 2px solid #e2e8f0; padding-left: 15px; }
        .admin-btn { background: #2563eb; color: white; padding: 7px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; transition: 0.3s; }
        .admin-btn:hover { background: #1d4ed8; color: white; }
    </style>
</head>
<body>

<div id="google_translate_element" style="display:none;"></div>

<header class="navbar" id="navbar">
    <div class="navbar-container">
        <a href="index.php" class="brand" style="white-space: nowrap;">
            <div class="brand-logo">
                <i class="fa-solid fa-mountain-sun"></i>
            </div>
            <div class="brand-text">
                <span class="brand-title">Wonogiri</span>
            </div>
        </a>

        <nav class="nav-menu" id="navMenu">
    <a href="index.php" class="<?= $halaman_aktif === 'beranda' ? 'active' : '' ?>">Beranda</a>
    <a href="kuliner.php" class="<?= $halaman_aktif === 'kuliner' ? 'active' : '' ?>">Kuliner Lokal</a>
    <a href="destinasi.php" class="<?= $halaman_aktif === 'destinasi' ? 'active' : '' ?>">Destinasi Wisata</a>
    <a href="informasi.php" class="<?= $halaman_aktif === 'informasi' ? 'active' : '' ?>">Informasi</a>
    <a href="tentang.php" class="<?= $halaman_aktif === 'tentang' ? 'active' : '' ?>">Tentang Wonogiri</a>
    <a href="buku_tamu.php" class="<?= $halaman_aktif === 'buku_tamu' ? 'active' : '' ?>">Buku Tamu</a>
</nav>
        <div class="navbar-actions">
            <div class="weather-widget">
                <i class="fa-solid fa-cloud-sun"></i>
                <div class="weather-text">
                    <span class="weather-temp">28°C</span>
                </div>
            </div>

            <div class="lang-switcher" id="langSwitcher">
                <button class="lang-btn" id="langBtn" type="button">
                    <i class="fa-solid fa-language"></i>
                    <span id="langCurrentLabel">ID</span>
                    <i class="fa-solid fa-chevron-down lang-caret"></i>
                </button>
                <div class="lang-dropdown" id="langDropdown">
                    <button type="button" class="lang-option active" data-lang="id">🇮🇩 Bahasa Indonesia</button>
                    <button type="button" class="lang-option" data-lang="en">🇬🇧 English</button>
                    <button type="button" class="lang-option" data-lang="zh">🇨🇳 中文 (Mandarin)</button>
                    <button type="button" class="lang-option" data-lang="ja">🇯🇵 日本語 (Jepang)</button>
                    <button type="button" class="lang-option" data-lang="ar">🇸🇦 العربية (Arab)</button>
                    <button type="button" class="lang-option" data-lang="ko">🇰🇷 한국어 (Korea)</button>
                </div>
            </div>

            <div class="user-menu-block">
                <?php if (isset($_SESSION['id'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin/index.php" class="admin-btn" title="Kembali ke Dashboard Admin">
                            <i class="fa-solid fa-user-gear"></i> Dashboard Admin
                        </a>
                    <?php endif; ?>
                    <a href="riwayat_pesanan.php" class="icon-btn" title="Pesanan Saya" style="color: #2e7d32;"><i class="fa-solid fa-receipt"></i></a>
                    <span class="user-greeting">
                        <i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($_SESSION['nama']) ?>
                    </span>
                    <a href="logout.php" class="icon-btn" title="Logout" style="color: #d32f2f;"><i class="fa-solid fa-right-from-bracket"></i></a>
                <?php else: ?>
                    <a href="login.php" class="auth-btn"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <?php endif; ?>
            </div>

            <button class="icon-btn menu-btn" id="menuToggle" title="Menu"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
</header>

<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'id,en,zh-CN,ja,ar,ko',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>