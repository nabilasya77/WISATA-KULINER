<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Opsional: Jika ingin mewajibkan login sebelum mengisi buku tamu, aktifkan baris di bawah ini
/*
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
*/

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$halaman_aktif = 'buku_tamu';
$judul_halaman = 'Buku Tamu Digital';

$pesan_sukses = '';
$pesan_error  = '';

// Proses Simpan Data Buku Tamu
if (isset($_POST['kirim_buku_tamu'])) {
    // Ambil data dan bersihkan (jika fungsi aman() Anda hanya untuk output echo, di sini kita gunakan mysqli_real_escape_string)
    $nama_pengunjung  = mysqli_real_escape_string($koneksi, trim($_POST['nama_pengunjung']));
    $asal_kota        = mysqli_real_escape_string($koneksi, trim($_POST['asal_kota']));
    $tujuan_kunjungan = mysqli_real_escape_string($koneksi, trim($_POST['tujuan_kunjungan']));
    $kesan_pesan      = mysqli_real_escape_string($koneksi, trim($_POST['kesan_pesan']));

    if (empty($nama_pengunjung) || empty($asal_kota) || empty($tujuan_kunjungan)) {
        $pesan_error = "Harap isi semua kolom yang wajib ditandai (*).";
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO buku_tamu (nama_pengunjung, asal_kota, tujuan_kunjungan, kesan_pesan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $nama_pengunjung, $asal_kota, $tujuan_kunjungan, $kesan_pesan);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan_sukses = "Terima kasih! Data kunjungan Anda berhasil disimpan.";
        } else {
            $pesan_error = "Gagal menyimpan data buku tamu. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Ambil riwayat buku tamu (15 data terbaru)
$query_tamu = mysqli_query($koneksi, "SELECT * FROM buku_tamu ORDER BY created_at DESC LIMIT 15");

require_once 'includes/header.php';
?>

<style>
/* Styling Tambahan untuk Halaman Buku Tamu */
.buku-tamu-container {
    max-width: 1000px;
    margin: 3rem auto;
    padding: 0 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 2.5rem;
}

@media (max-width: 768px) {
    .buku-tamu-container {
        grid-template-columns: 1fr;
    }
}

.bt-form-box, .bt-list-box {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.bt-form-box h2, .bt-list-box h2 {
    color: #1b4332;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    border-bottom: 2px solid #52b788;
    padding-bottom: 0.5rem;
}

.form-group-bt {
    margin-bottom: 1.2rem;
}

.form-group-bt label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2d6a4f;
    font-size: 0.9rem;
}

.form-group-bt input, .form-group-bt textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-family: inherit;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-group-bt input:focus, .form-group-bt textarea:focus {
    border-color: #52b788;
    outline: none;
    box-shadow: 0 0 0 3px rgba(82, 183, 136, 0.2);
}

.alert {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}
.alert-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
.alert-danger { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

/* Styling List Tamu */
.bt-feed {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.bt-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    border-left: 4px solid #52b788;
}

.bt-card-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.bt-nama {
    font-weight: bold;
    color: #1b4332;
    font-size: 1rem;
}

.bt-meta {
    font-size: 0.85rem;
    color: #495057;
    margin-bottom: 0.5rem;
    background: #e9ecef;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    display: inline-block;
}

.bt-kesan {
    font-style: italic;
    color: #333;
    font-size: 0.9rem;
    line-height: 1.4;
}
</style>

<section class="page-hero" style="background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 60%, #40916c 100%); color: #fff; padding: 4rem 2rem 3rem; text-align: center;">
    <span class="page-hero-eyebrow" style="font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #95d5b2; font-weight:600;">Interaksi Pengunjung</span>
    <h1 style="margin-top: 0.5rem; font-size: 2.5rem;">Buku Tamu Digital Wonogiri</h1>
    <p style="color: #d8f3dc; max-width: 600px; margin: 0.5rem auto 0;">Silakan isi buku tamu di bawah ini untuk mencatatkan riwayat kunjungan atau memberikan kesan pesan berharga Anda.</p>
</section>

<main class="buku-tamu-container">
    
    <section class="bt-form-box">
        <h2>Isi Buku Tamu</h2>
        
        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $pesan_sukses ?></div>
        <?php endif; ?>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $pesan_error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group-bt">
                <label for="nama_pengunjung">Nama Pengunjung *</label>
                <input type="text" id="nama_pengunjung" name="nama_pengunjung" required placeholder="Contoh: Budi Santoso" value="<?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : '' ?>">
            </div>

            <div class="form-group-bt">
                <label for="asal_kota">Asal Kota/Daerah *</label>
                <input type="text" id="asal_kota" name="asal_kota" required placeholder="Contoh: Solo, Jakarta, Wonogiri">
            </div>

            <div class="form-group-bt">
                <label for="tujuan_kunjungan">Tujuan Kunjungan *</label>
                <input type="text" id="tujuan_kunjungan" name="tujuan_kunjungan" required placeholder="Contoh: Liburan Keluarga, Wisata Kuliner, Dinas">
            </div>

            <div class="form-group-bt">
                <label for="kesan_pesan">Kesan & Pesan</label>
                <textarea id="kesan_pesan" name="kesan_pesan" rows="4" placeholder="Bagikan cerita singkat atau kesan Anda tentang Wonogiri..."></textarea>
            </div>

            <button type="submit" name="kirim_buku_tamu" class="btn btn-green btn-block" style="width: 100%; border: none; padding: 0.8rem; font-weight: bold; cursor: pointer;">
                Kirim Data <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </section>

    <section class="bt-list-box">
        <h2>Pengunjung Terbaru</h2>
        <div class="bt-feed">
            <?php if (mysqli_num_rows($query_tamu) > 0): ?>
                <?php while ($tamu = mysqli_fetch_assoc($query_tamu)): ?>
                    <div class="bt-card">
                        <div class="bt-card-header">
                            <span class="bt-nama"><i class="fa-solid fa-user-astronaut"></i> <?= htmlspecialchars($tamu['nama_pengunjung']) ?></span>
                            <span><i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($tamu['created_at'])) ?></span>
                        </div>
                        <div class="bt-meta">
                            <i class="fa-solid fa-city"></i> Dari: <strong><?= htmlspecialchars($tamu['asal_kota']) ?></strong> 
                            | Keperluan: <strong><?= htmlspecialchars($tamu['tujuan_kunjungan']) ?></strong>
                        </div>
                        <?php if (!empty($tamu['kesan_pesan'])): ?>
                            <p class="bt-kesan">"<?= nl2br(htmlspecialchars($tamu['kesan_pesan'])) ?>"</p>
                        <?php else: ?>
                            <p class="bt-kesan" style="color: #aaa; font-style: italic;">Tidak meninggalkan pesan.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #6c757d; font-style: italic; margin-top: 2rem;">Belum ada pengunjung yang mengisi buku tamu.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>