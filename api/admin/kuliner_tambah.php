<?php
require_once 'auth_check.php';
require_once '../includes/koneksi.php';
require_once '../includes/functions.php';

if (!function_exists('buatSlug')) {
    function buatSlug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);
        return trim($string, '-');
    }
}

$mode = 'tambah';
$data = [
    'id' => '', 'nama_kuliner' => '', 'deskripsi' => '', 'harga_mulai' => 0,
    'lokasi' => '', 'alamat_lengkap' => '', 'latitude' => '', 'longitude' => '',
    'jam_operasional' => '', 'kontak' => '', 'gambar_utama' => '', 'status' => 'aktif'
];

if (isset($_GET['id'])) {
    $mode = 'edit';
    $id = (int) $_GET['id'];
    $cek = mysqli_query($koneksi, "SELECT * FROM kuliner WHERE id = $id");
    if ($row = mysqli_fetch_assoc($cek)) {
        $data = $row;
    } else {
        header("Location: kuliner.php");
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kuliner     = trim($_POST['nama_kuliner']);
    $deskripsi        = trim($_POST['deskripsi']);
    $harga_mulai      = (float) $_POST['harga_mulai'];
    $lokasi           = trim($_POST['lokasi']);
    $alamat_lengkap   = trim($_POST['alamat_lengkap']);
    $latitude         = $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
    $longitude        = $_POST['longitude'] !== '' ? $_POST['longitude'] : null;
    $jam_operasional  = trim($_POST['jam_operasional']);
    $kontak           = trim($_POST['kontak']);
    $status           = $_POST['status'];
    $slug             = buatSlug($nama_kuliner) . '-' . time();
    $id_post          = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($nama_kuliner === '' || $lokasi === '') {
        $error = 'Nama menu dan lokasi wajib diisi.';
    } else {
        // Gambar diisi lewat URL langsung (link gambar dari internet)
        $gambar_utama = isset($_POST['gambar_utama']) ? trim($_POST['gambar_utama']) : $data['gambar_utama'];

        if ($error === '') {
            if ($mode === 'edit' && $id_post > 0) {
                $stmt = mysqli_prepare($koneksi, "UPDATE kuliner SET nama_kuliner=?, slug=?, deskripsi=?, harga_mulai=?, lokasi=?, alamat_lengkap=?, latitude=?, longitude=?, jam_operasional=?, kontak=?, gambar_utama=?, status=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sssdssssssssi",
                    $nama_kuliner, $slug, $deskripsi, $harga_mulai, $lokasi, $alamat_lengkap,
                    $latitude, $longitude, $jam_operasional, $kontak, $gambar_utama, $status, $id_post
                );
                mysqli_stmt_execute($stmt);
                header("Location: kuliner.php?status=edit");
                exit;
            } else {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO kuliner (nama_kuliner, slug, deskripsi, harga_mulai, lokasi, alamat_lengkap, latitude, longitude, jam_operasional, kontak, gambar_utama, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "sssdsssssss",
                    $nama_kuliner, $slug, $deskripsi, $harga_mulai, $lokasi, $alamat_lengkap,
                    $latitude, $longitude, $jam_operasional, $kontak, $gambar_utama, $status
                );
                mysqli_stmt_execute($stmt);
                header("Location: kuliner.php?status=tambah");
                exit;
            }
        }
    }

    $data = array_merge($data, $_POST);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'edit' ? 'Edit' : 'Tambah' ?> Kuliner - Admin Wonogiri Wisata</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #2F7B4F; --primary-dark: #1B3B2D; --primary-light: #EAF6EE;
            --accent: #E8A33D; --info: #3B8EA5; --terracotta: #C1666B; --danger: #D9534F;
            --dark: #20302A; --text-gray: #6B7D74; --bg-color: #F5F9F6; --border-color: #E1ECE4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-color); color: #333; display: flex; min-height: 100vh; }

        .sidebar { width: 250px; background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%); color: white; display: flex; flex-direction: column; }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 22px 20px; border-bottom: 1px solid rgba(255,255,255,0.15); text-decoration: none; color: white; }
        .sidebar-brand .logo-icon { width: 38px; height: 38px; min-width: 38px; background: white; color: var(--primary-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .sidebar-brand-text { display: flex; flex-direction: column; line-height: 1.2; }
        .sidebar-brand-text .brand-main { font-weight: 700; font-size: 1.1rem; }
        .sidebar-brand-text .brand-sub { font-size: 0.68rem; opacity: 0.8; letter-spacing: 1px; text-transform: uppercase; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu a { display: flex; align-items: center; color: rgba(255,255,255,0.85); text-decoration: none; padding: 12px 20px; font-size: 0.9rem; }
        .sidebar-menu a i { margin-right: 15px; width: 20px; text-align: center; }
        .sidebar-menu a:hover, .sidebar-menu a.active { color: white; background: rgba(255,255,255,0.12); border-left: 4px solid white; }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background: white; height: 70px; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; border-bottom: 1px solid var(--border-color); }
        .topbar-user { display: flex; align-items: center; gap: 15px; }
        .topbar-user img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light); }
        .topbar-user span { font-size: 0.9rem; font-weight: 600; color: var(--dark); }

        .container { padding: 30px; overflow-y: auto; max-width: 900px; }
        .page-title { color: var(--dark); font-size: 1.5rem; font-weight: 600; margin-bottom: 5px; }
        .breadcrumb { color: var(--text-gray); font-size: 0.85rem; margin-bottom: 20px; }
        .breadcrumb a { color: var(--primary); text-decoration: none; }

        .form-card { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); padding: 30px; }
        .alert-error { background: #FBEAEA; color: var(--danger); border: 1px solid var(--danger); padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-group { margin-bottom: 18px; }
        .form-group.full { grid-column: 1 / -1; }
        label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--dark); margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px;
            font-size: 0.9rem; font-family: 'Poppins', sans-serif; outline: none; background: #FAFCFA;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); background: white; }
        textarea { resize: vertical; min-height: 90px; }

        .img-preview { width: 120px; height: 80px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; display: block; border: 1px solid var(--border-color); }

        .form-actions { display: flex; gap: 12px; margin-top: 10px; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 12px 28px; border-radius: 20px; font-size: 0.9rem; cursor: pointer; font-weight: 500; }
        .btn-save:hover { background: var(--primary-dark); }
        .btn-cancel { background: #eee; color: var(--dark); padding: 12px 28px; border-radius: 20px; font-size: 0.9rem; text-decoration: none; font-weight: 500; }

        @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="index.php" class="sidebar-brand">
            <span class="logo-icon"><i class="fa-solid fa-mountain"></i></span>
            <span class="sidebar-brand-text">
                <span class="brand-main">Wonogiri</span>
                <span class="brand-sub">Admin Panel</span>
            </span>
        </a>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
            <li><a href="destinasi.php"><i class="fa-solid fa-map-location-dot"></i> Destinasi Wisata</a></li>
            <li><a href="kuliner.php" class="active"><i class="fa-solid fa-utensils"></i> Kuliner Lokal</a></li>
            <li><a href="kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
            <li><a href="buku_tamu.php"><i class="fa-solid fa-book-open"></i> Buku Tamu</a></li>
            <li><a href="transaksi.php"><i class="fa-solid fa-receipt"></i> Transaksi</a></li>
            <li><a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website</a></li>
            <li><a href="../logout.php" style="margin-top: 20px; color: #ffd6d6;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div></div>
            <div class="topbar-user">
                <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <img src="../assets/img/default-avatar.png" alt="Admin" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=2F7B4F&color=fff'">
            </div>
        </header>

        <div class="container">
            <div class="breadcrumb"><a href="kuliner.php">Kuliner Lokal</a> / <?= $mode === 'edit' ? 'Edit' : 'Tambah' ?></div>
            <h1 class="page-title"><?= $mode === 'edit' ? 'Edit Menu Kuliner' : 'Tambah Menu Kuliner Baru' ?></h1>
            <br>

            <div class="form-card">
                <?php if ($error): ?>
                    <div class="alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Menu / Kuliner *</label>
                            <input type="text" name="nama_kuliner" required value="<?= htmlspecialchars($data['nama_kuliner']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Harga Mulai (Rp) *</label>
                            <input type="number" step="0.01" name="harga_mulai" required value="<?= htmlspecialchars($data['harga_mulai']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Lokasi / Kecamatan *</label>
                            <input type="text" name="lokasi" required value="<?= htmlspecialchars($data['lokasi']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Jam Operasional</label>
                            <input type="text" name="jam_operasional" value="<?= htmlspecialchars($data['jam_operasional']) ?>" placeholder="08.00 - 21.00 WIB">
                        </div>

                        <div class="form-group full">
                            <label>Alamat Lengkap</label>
                            <input type="text" name="alamat_lengkap" value="<?= htmlspecialchars($data['alamat_lengkap']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="text" name="latitude" value="<?= htmlspecialchars($data['latitude']) ?>" placeholder="-7.812...">
                        </div>

                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="text" name="longitude" value="<?= htmlspecialchars($data['longitude']) ?>" placeholder="110.920...">
                        </div>

                        <div class="form-group">
                            <label>Kontak</label>
                            <input type="text" name="kontak" value="<?= htmlspecialchars($data['kontak']) ?>" placeholder="0812-xxxx-xxxx">
                        </div>

                        <div class="form-group full">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                        </div>

                        <div class="form-group full">
                            <label>Link Gambar Menu</label>
                            <?php if (!empty($data['gambar_utama'])): ?>
                                <img src="<?= htmlspecialchars($data['gambar_utama']) ?>" class="img-preview" onerror="this.style.display='none'">
                            <?php endif; ?>
                            <input type="text" name="gambar_utama" value="<?= htmlspecialchars($data['gambar_utama']) ?>" placeholder="Masukkan atau paste URL gambar dari internet (contoh: https://website.com/foto.jpg)">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="aktif" <?= $data['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $data['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        <a href="kuliner.php" class="btn-cancel">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>