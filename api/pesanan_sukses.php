<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'kuliner';
$transaksi_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = (int) $_SESSION['id'];

// Pastikan transaksi ini benar-benar milik user yang login
$stmt = mysqli_prepare($koneksi, "SELECT * FROM transaksi WHERE id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'ii', $transaksi_id, $user_id);
mysqli_stmt_execute($stmt);
$trx = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$trx) {
    header('Location: kuliner.php');
    exit;
}

$stmtDetail = mysqli_prepare($koneksi, "
    SELECT dt.*, k.nama_kuliner, k.gambar_utama 
    FROM detail_transaksi dt 
    LEFT JOIN kuliner k ON dt.kuliner_id = k.id 
    WHERE dt.transaksi_id = ?
");
mysqli_stmt_bind_param($stmtDetail, 'i', $transaksi_id);
mysqli_stmt_execute($stmtDetail);
$itemList = mysqli_stmt_get_result($stmtDetail);

$judul_halaman = "Pesanan Berhasil";
require_once 'includes/header.php';
?>

<style>
.sukses-container { max-width: 650px; margin: 3rem auto; padding: 0 1.5rem; }
.sukses-card { background: #fff; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.07); padding: 2.5rem; text-align: center; }
.sukses-icon { font-size: 3.5rem; color: #2d6a4f; margin-bottom: 1rem; }
.sukses-card h1 { color: #1b4332; margin: 0 0 8px; font-size: 1.6rem; }
.sukses-card p.sub { color: #6c757d; margin-bottom: 1.8rem; }
.invoice-box { background: #f8f9fa; border-radius: 12px; padding: 1.2rem 1.5rem; text-align: left; margin-bottom: 1.5rem; }
.invoice-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.92rem; }
.invoice-row span:first-child { color: #6c757d; }
.invoice-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #eee; text-align: left; }
.invoice-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
.invoice-item .nama { font-weight: 600; color: #1b4332; font-size: 0.92rem; }
.invoice-item .meta { color: #6c757d; font-size: 0.82rem; }
.badge-status { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: #FBEED9; color: #b8860b; }
.action-row { display: flex; gap: 12px; margin-top: 1.5rem; }
.action-row a { flex: 1; }
</style>

<main class="sukses-container">
    <div class="sukses-card">
        <div class="sukses-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h1>Pesanan Berhasil Dibuat!</h1>
        <p class="sub">Invoice #TRX<?= str_pad($trx['id'], 4, '0', STR_PAD_LEFT) ?> sedang menunggu konfirmasi dari admin.</p>

        <div class="invoice-box">
            <?php while ($item = mysqli_fetch_assoc($itemList)): ?>
            <div class="invoice-item">
                <img src="<?= aman($item['gambar_utama']) ?>" alt="<?= aman($item['nama_kuliner']) ?>">
                <div>
                    <div class="nama"><?= aman($item['nama_kuliner'] ?? 'Menu') ?></div>
                    <div class="meta"><?= $item['jumlah'] ?> porsi &times; <?= formatRupiah($item['subtotal'] / max($item['jumlah'],1)) ?></div>
                </div>
            </div>
            <?php endwhile; ?>

            <div class="invoice-row" style="margin-top:10px;">
                <span>Status</span>
                <span class="badge-status"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
            </div>
            <div class="invoice-row" style="font-weight:700; font-size:1.05rem; color:#1b4332;">
                <span>Total Bayar</span>
                <span><?= formatRupiah($trx['total_bayar']) ?></span>
            </div>
        </div>

        <div class="action-row">
            <a href="riwayat_pesanan.php" class="btn btn-green btn-block" style="margin:0;">Lihat Pesanan Saya</a>
            <a href="kuliner.php" class="btn btn-orange btn-block" style="margin:0;">Pesan Lagi</a>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
