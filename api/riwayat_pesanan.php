<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'kuliner';
$judul_halaman = 'Pesanan Saya';
$user_id = (int) $_SESSION['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM transaksi WHERE user_id = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$listTrx = mysqli_stmt_get_result($stmt);

require_once 'includes/header.php';
?>

<style>
.riwayat-container { max-width: 800px; margin: 3rem auto; padding: 0 1.5rem; }
.riwayat-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); margin-bottom: 1.2rem; overflow: hidden; }
.riwayat-head { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.4rem; border-bottom: 1px solid #eee; }
.riwayat-head .invoice { font-weight: 700; color: #1b4332; }
.riwayat-head .tanggal { font-size: 0.8rem; color: #6c757d; }
.riwayat-body { padding: 1rem 1.4rem; }
.riwayat-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; }
.riwayat-item img { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; }
.riwayat-item .nama { font-weight: 600; color: #1b4332; font-size: 0.9rem; }
.riwayat-item .meta { color: #6c757d; font-size: 0.8rem; }
.riwayat-foot { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.4rem; background: #f8f9fa; }
.riwayat-foot .total { font-weight: 700; color: #2d6a4f; }
.badge { padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
.badge-pending { background: #FBEED9; color: #b8860b; }
.badge-sukses { background: #d1e7dd; color: #0f5132; }
.badge-batal { background: #f8d7da; color: #842029; }
.empty-text { text-align: center; color: #6c757d; padding: 3rem 0; font-style: italic; }
</style>

<section class="page-hero" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 60%, #40916c 100%); color: #fff; padding: 3.5rem 2rem 2.5rem; text-align: center;">
    <span style="font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #95d5b2; font-weight:600;">Riwayat Transaksi</span>
    <h1 style="margin-top: 0.5rem; font-size: 2.2rem;">Pesanan Saya</h1>
</section>

<main class="riwayat-container">
    <?php if (mysqli_num_rows($listTrx) === 0): ?>
        <p class="empty-text">Kamu belum pernah memesan kuliner. <a href="kuliner.php">Yuk pesan sekarang!</a></p>
    <?php else: ?>
        <?php while ($trx = mysqli_fetch_assoc($listTrx)): ?>
        <div class="riwayat-card">
            <div class="riwayat-head">
                <span class="invoice">#TRX<?= str_pad($trx['id'], 4, '0', STR_PAD_LEFT) ?></span>
                <span class="tanggal"><i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($trx['created_at'])) ?></span>
            </div>
            <div class="riwayat-body">
                <?php
                $stmtItem = mysqli_prepare($koneksi, "
                    SELECT dt.*, k.nama_kuliner, k.gambar_utama 
                    FROM detail_transaksi dt 
                    LEFT JOIN kuliner k ON dt.kuliner_id = k.id 
                    WHERE dt.transaksi_id = ?
                ");
                mysqli_stmt_bind_param($stmtItem, 'i', $trx['id']);
                mysqli_stmt_execute($stmtItem);
                $items = mysqli_stmt_get_result($stmtItem);
                while ($item = mysqli_fetch_assoc($items)):
                ?>
                <div class="riwayat-item">
                    <img src="<?= aman($item['gambar_utama'] ?? '') ?>" alt="" onerror="this.style.display='none'">
                    <div>
                        <div class="nama"><?= aman($item['nama_kuliner'] ?? 'Menu telah dihapus') ?></div>
                        <div class="meta"><?= $item['jumlah'] ?> porsi &times; <?= formatRupiah($item['subtotal'] / max($item['jumlah'],1)) ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="riwayat-foot">
                <span class="badge badge-<?= $trx['status_pembayaran'] ?>"><?= ucfirst($trx['status_pembayaran']) ?></span>
                <span class="total">Total: <?= formatRupiah($trx['total_bayar']) ?></span>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
