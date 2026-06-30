<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fa-solid fa-mountain-sun"></i>
        </div>
        <div class="brand-text">Admin Panel</div>
    </div>
    
    <div class="sidebar-heading">Menu Utama</div>
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-border-all"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="kategori.php" class="<?= $current_page == 'kategori.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-tags"></i> <span>Kategori Wisata</span>
            </a>
        </li>
        <li>
            <a href="destinasi.php" class="<?= $current_page == 'destinasi.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-map-location-dot"></i> <span>Destinasi Wisata</span>
            </a>
        </li>
        <li>
            <a href="kuliner.php" class="<?= $current_page == 'kuliner.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-utensils"></i> <span>Kuliner & Resto</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-heading">Interaksi & Transaksi</div>
    <ul class="sidebar-menu">
        <li>
            <a href="buku_tamu.php" class="<?= $current_page == 'buku_tamu.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-address-book"></i> <span>Buku Tamu</span>
            </a>
        </li>
        <li>
            <a href="transaksi.php" class="<?= $current_page == 'transaksi.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> <span>Transaksi</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="../index.php" target="_blank" class="btn-view-web" title="Lihat Tampilan Website">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website
        </a>
        <a href="../logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fa-solid fa-power-off"></i> Keluar
        </a>
    </div>
</div>