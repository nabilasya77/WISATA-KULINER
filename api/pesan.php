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

$stmt = mysqli_prepare($koneksi, "SELECT * FROM kuliner WHERE slug = ? AND status = 'aktif' LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    header('Location: kuliner.php');
    exit;
}

$error = '';

// ---- Proses Simpan Pesanan ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah = isset($_POST['jumlah']) ? (int) $_POST['jumlah'] : 1;
    if ($jumlah < 1) $jumlah = 1;

    $user_id  = (int) $_SESSION['id'];
    $harga    = (float) $data['harga_mulai'];
    $subtotal = $harga * $jumlah;

    mysqli_begin_transaction($koneksi);
    try {
        // 1. Simpan header transaksi
        $stmtTrx = mysqli_prepare($koneksi, "INSERT INTO transaksi (user_id, total_bayar, status_pembayaran) VALUES (?, ?, 'pending')");
        mysqli_stmt_bind_param($stmtTrx, 'id', $user_id, $subtotal);
        mysqli_stmt_execute($stmtTrx);
        $transaksi_id = mysqli_insert_id($koneksi);

        if (!$transaksi_id) {
            throw new Exception('Gagal membuat transaksi');
        }

        // 2. Simpan detail item yang dipesan
        $stmtDetail = mysqli_prepare($koneksi, "INSERT INTO detail_transaksi (transaksi_id, kuliner_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmtDetail, 'iiid', $transaksi_id, $data['id'], $jumlah, $subtotal);
        mysqli_stmt_execute($stmtDetail);

        mysqli_commit($koneksi);
        header("Location: pesanan_sukses.php?id=" . $transaksi_id);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $error = "Gagal memproses pesanan. Silakan coba lagi.";
    }
}

$judul_halaman = "Pesan " . $data['nama_kuliner'];
require_once 'includes/header.php';
?>

<style>
.pesan-container { max-width: 720px; margin: 3rem auto; padding: 0 1.5rem; }
.pesan-card { background: #fff; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.07); overflow: hidden; }
.pesan-item { display: flex; gap: 18px; padding: 1.8rem; border-bottom: 1px solid #eee; }
.pesan-item img { width: 110px; height: 110px; object-fit: cover; border-radius: 12px; flex-shrink: 0; }
.pesan-item h2 { margin: 0 0 6px; color: #1b4332; font-size: 1.2rem; }
.pesan-item p { margin: 0; color: #6c757d; font-size: 0.9rem; }
.pesan-item .harga-satuan { margin-top: 8px; font-weight: 700; color: #2d6a4f; }
.pesan-form { padding: 1.8rem; }
.jumlah-box { display: flex; align-items: center; gap: 14px; margin-bottom: 1.5rem; }
.jumlah-box label { font-weight: 600; color: #1b4332; }
.qty-control { display: flex; align-items: center; border: 1.5px solid #d8e6dd; border-radius: 10px; overflow: hidden; }
.qty-control button { width: 38px; height: 38px; border: none; background: #eaf6ee; color: #1b4332; font-size: 1.1rem; cursor: pointer; }
.qty-control button:hover { background: #d8e6dd; }
.qty-control input { width: 55px; text-align: center; border: none; font-size: 1rem; font-weight: 600; -moz-appearance: textfield; }
.qty-control input::-webkit-outer-spin-button, .qty-control input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.ringkasan-box { background: #f8f9fa; border-radius: 12px; padding: 1.2rem 1.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
.ringkasan-box span:first-child { color: #6c757d; font-size: 0.95rem; }
.ringkasan-box .total-harga { font-size: 1.4rem; font-weight: 800; color: #2d6a4f; }
.catatan-info { font-size: 0.82rem; color: #888; margin-top: 10px; text-align: center; }
.alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.2rem; font-size: 0.9rem; }
.alert-danger { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
</style>

<section class="page-hero" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 60%, #40916c 100%); color: #fff; padding: 3.5rem 2rem 2.5rem; text-align: center;">
    <span style="font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #95d5b2; font-weight:600;">Pemesanan Kuliner</span>
    <h1 style="margin-top: 0.5rem; font-size: 2.2rem;">Lengkapi Pesananmu</h1>
</section>

<main class="pesan-container">
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>

    <div class="pesan-card">
        <div class="pesan-item">
            <img src="<?= aman($data['gambar_utama']) ?>" alt="<?= aman($data['nama_kuliner']) ?>">
            <div>
                <h2><?= aman($data['nama_kuliner']) ?></h2>
                <p><i class="fa-solid fa-location-dot"></i> <?= aman($data['lokasi']) ?></p>
                <p class="harga-satuan"><?= formatRupiah($data['harga_mulai']) ?> / porsi</p>
            </div>
        </div>

        <form method="POST" class="pesan-form" id="formPesan">
            <div class="jumlah-box">
                <label for="jumlah">Jumlah Porsi</label>
                <div class="qty-control">
                    <button type="button" onclick="ubahJumlah(-1)">-</button>
                    <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="50" oninput="hitungTotal()">
                    <button type="button" onclick="ubahJumlah(1)">+</button>
                </div>
            </div>

            <div class="ringkasan-box">
                <span>Total Pembayaran</span>
                <span class="total-harga" id="totalHarga"><?= formatRupiah($data['harga_mulai']) ?></span>
            </div>

            <button type="submit" class="btn btn-green btn-block" style="border:none; cursor:pointer;">
                <i class="fa-solid fa-bag-shopping"></i> Buat Pesanan
            </button>
            <p class="catatan-info">Pesanan akan berstatus <b>Pending</b> sampai dikonfirmasi oleh admin.</p>
        </form>
    </div>
</main>

<script>
const hargaSatuan = <?= (float) $data['harga_mulai'] ?>;

function formatRupiahJS(angka) {
    if (angka == 0) return 'Gratis';
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function hitungTotal() {
    const input = document.getElementById('jumlah');
    let jumlah = parseInt(input.value) || 1;
    if (jumlah < 1) jumlah = 1;
    if (jumlah > 50) jumlah = 50;
    input.value = jumlah;
    document.getElementById('totalHarga').textContent = formatRupiahJS(hargaSatuan * jumlah);
}

function ubahJumlah(delta) {
    const input = document.getElementById('jumlah');
    input.value = (parseInt(input.value) || 1) + delta;
    hitungTotal();
}
</script>

<?php require_once 'includes/footer.php'; ?>
