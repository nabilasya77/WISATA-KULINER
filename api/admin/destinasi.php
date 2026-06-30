<?php
require_once 'auth_check.php';
require_once '../includes/koneksi.php';
require_once '../includes/functions.php';

// Hapus data jika ada permintaan hapus
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    // Ambil dulu nama gambar agar bisa dihapus dari folder uploads
    $cek = mysqli_query($koneksi, "SELECT gambar_utama FROM destinasi_wisata WHERE id = $id");
    $data_gambar = mysqli_fetch_assoc($cek);
    if ($data_gambar && $data_gambar['gambar_utama'] && file_exists('../' . $data_gambar['gambar_utama'])) {
        @unlink('../' . $data_gambar['gambar_utama']);
    }

    mysqli_query($koneksi, "DELETE FROM destinasi_wisata WHERE id = $id");
    header("Location: destinasi.php?status=hapus");
    exit;
}

// Pencarian sederhana
$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$where = '';
if ($keyword !== '') {
    $k = mysqli_real_escape_string($koneksi, $keyword);
    $where = "WHERE d.nama_destinasi LIKE '%$k%' OR d.lokasi LIKE '%$k%'";
}

$q_destinasi = mysqli_query($koneksi, "
    SELECT d.*, kw.nama_kategori 
    FROM destinasi_wisata d 
    LEFT JOIN kategori_wisata kw ON d.kategori_id = kw.id 
    $where
    ORDER BY d.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinasi Wisata - Admin Wonogiri Wisata</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #2F7B4F;
            --primary-dark: #1B3B2D;
            --primary-light: #EAF6EE;
            --accent: #E8A33D;
            --info: #3B8EA5;
            --terracotta: #C1666B;
            --danger: #D9534F;
            --dark: #20302A;
            --text-gray: #6B7D74;
            --bg-color: #F5F9F6;
            --border-color: #E1ECE4;
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
        .topbar-search form { display: flex; gap: 8px; }
        .topbar-search input { padding: 8px 15px; border-radius: 20px; border: 1px solid var(--border-color); outline: none; width: 250px; }
        .topbar-search input:focus { border-color: var(--primary); }
        .topbar-search button { border: none; background: var(--primary); color: white; padding: 0 16px; border-radius: 20px; cursor: pointer; }
        .topbar-user { display: flex; align-items: center; gap: 15px; }
        .topbar-user img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light); }
        .topbar-user span { font-size: 0.9rem; font-weight: 600; color: var(--dark); }

        .container { padding: 30px; overflow-y: auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px; }
        .page-title { color: var(--dark); font-size: 1.5rem; font-weight: 600; }
        .btn-add { background: var(--primary); color: white; padding: 10px 20px; border-radius: 20px; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { background: var(--primary-dark); }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: var(--primary-light); color: var(--primary-dark); border: 1px solid var(--primary); }

        .card-table { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 20px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        th { background: #FAFCFA; color: var(--dark); font-weight: 600; }
        td img { width: 60px; height: 40px; object-fit: cover; border-radius: 5px; }

        .badge { padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; color: white; white-space: nowrap; }
        .badge-aktif { background: var(--primary); }
        .badge-draft { background: var(--accent); }
        .badge-unggulan { background: var(--terracotta); }

        .action-btns a { margin-right: 5px; color: white; padding: 6px 9px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; display: inline-block; }
        .btn-edit { background: var(--accent); }
        .btn-delete { background: var(--danger); }

        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-gray); }
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
            <li><a href="destinasi.php" class="active"><i class="fa-solid fa-map-location-dot"></i> Destinasi Wisata</a></li>
            <li><a href="kuliner.php"><i class="fa-solid fa-utensils"></i> Kuliner Lokal</a></li>
            <li><a href="kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
            <li><a href="buku_tamu.php"><i class="fa-solid fa-book-open"></i> Buku Tamu</a></li>
            <li><a href="transaksi.php"><i class="fa-solid fa-receipt"></i> Transaksi</a></li>
            <li><a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website</a></li>
            <li><a href="../logout.php" style="margin-top: 20px; color: #ffd6d6;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-search">
                <form method="GET" action="destinasi.php">
                    <input type="text" name="cari" placeholder="Cari nama / lokasi..." value="<?= htmlspecialchars($keyword) ?>">
                    <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>
            <div class="topbar-user">
                <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <img src="../assets/img/default-avatar.png" alt="Admin" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=2F7B4F&color=fff'">
            </div>
        </header>

        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Destinasi Wisata</h1>
                <a href="destinasi_tambah.php" class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Destinasi</a>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'hapus'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Destinasi berhasil dihapus.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'tambah'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Destinasi baru berhasil ditambahkan.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'edit'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Destinasi berhasil diperbarui.</div>
            <?php endif; ?>

            <div class="card-table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Destinasi</th>
                            <th>Kategori</th>
                            <th>Tiket Masuk</th>
                            <th>Lokasi</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($q_destinasi)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><img src="<?= htmlspecialchars($row['gambar_utama']) ?>" alt="Img" onerror="this.src='https://via.placeholder.com/60x40?text=No+Img'"></td>
                            <td>
                                <?= htmlspecialchars($row['nama_destinasi']) ?>
                                <?php if ($row['is_unggulan']): ?><br><span class="badge badge-unggulan">Unggulan</span><?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum') ?></td>
                            <td><?= formatRupiah($row['tiket_masuk']) ?></td>
                            <td><?= htmlspecialchars($row['lokasi']) ?></td>
                            <td><i class="fa-solid fa-star" style="color: var(--accent);"></i> <?= $row['rating'] ?> (<?= $row['jumlah_ulasan'] ?>)</td>
                            <td>
                                <?php if ($row['status'] === 'aktif'): ?>
                                    <span class="badge badge-aktif">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-draft">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-btns">
                                <a href="destinasi_tambah.php?id=<?= $row['id'] ?>" class="btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a href="destinasi.php?hapus=<?= $row['id'] ?>" class="btn-delete" title="Hapus" onclick="return confirm('Yakin hapus destinasi ini?');"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($q_destinasi) == 0): ?>
                        <tr><td colspan="9"><div class="empty-state"><i class="fa-solid fa-map fa-2x" style="margin-bottom:10px;"></i><br>Belum ada data destinasi.</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>