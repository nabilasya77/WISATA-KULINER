<?php
require_once 'auth_check.php';
require_once '../includes/koneksi.php';
require_once '../includes/functions.php';

// Update status pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int) $_POST['transaksi_id'];
    $status = $_POST['status_pembayaran'];
    $allowed_status = ['pending', 'sukses', 'batal'];
    if (in_array($status, $allowed_status)) {
        $stmt = mysqli_prepare($koneksi, "UPDATE transaksi SET status_pembayaran=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: transaksi.php?status=update");
    exit;
}

$filter_status = isset($_GET['filter']) ? $_GET['filter'] : '';
$where = '';
if (in_array($filter_status, ['pending', 'sukses', 'batal'])) {
    $where = "WHERE t.status_pembayaran = '$filter_status'";
}

$q_transaksi = mysqli_query($koneksi, "
    SELECT t.*, u.nama AS nama_user, u.email
    FROM transaksi t
    LEFT JOIN users u ON t.user_id = u.id
    $where
    ORDER BY t.id DESC
");

// Ringkasan total pendapatan dari transaksi sukses
$total_sukses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) AS total FROM transaksi WHERE status_pembayaran = 'sukses'"))['total'] ?? 0;
$jml_pending  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE status_pembayaran = 'pending'"))['total'];
$jml_sukses   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE status_pembayaran = 'sukses'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Admin Wonogiri Wisata</title>
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
        .topbar { background: white; height: 70px; display: flex; align-items: center; justify-content: flex-end; padding: 0 30px; border-bottom: 1px solid var(--border-color); }
        .topbar-user { display: flex; align-items: center; gap: 15px; }
        .topbar-user img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light); }
        .topbar-user span { font-size: 0.9rem; font-weight: 600; color: var(--dark); }

        .container { padding: 30px; overflow-y: auto; }
        .page-title { color: var(--dark); font-size: 1.5rem; font-weight: 600; margin-bottom: 25px; }

        .row-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); border-left: 5px solid; }
        .stat-card.primary { border-left-color: var(--primary); }
        .stat-card.accent { border-left-color: var(--accent); }
        .stat-card.info { border-left-color: var(--info); }
        .stat-card-info h4 { font-size: 0.78rem; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; color: var(--text-gray); }
        .stat-card-info h2 { font-size: 1.4rem; color: var(--dark); }
        .stat-card-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .stat-card.primary .stat-card-icon { background: var(--primary-light); color: var(--primary); }
        .stat-card.accent .stat-card-icon { background: #FBEED9; color: var(--accent); }
        .stat-card.info .stat-card-icon { background: #DDEEF2; color: var(--info); }

        .filter-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-bar a { padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 0.85rem; background: white; color: var(--dark); border: 1px solid var(--border-color); }
        .filter-bar a.active { background: var(--primary); color: white; border-color: var(--primary); }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: var(--primary-light); color: var(--primary-dark); border: 1px solid var(--primary); }

        .card-table { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 20px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        th { background: #FAFCFA; color: var(--dark); font-weight: 600; }

        .badge { padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; color: white; white-space: nowrap; }
        .badge-pending { background: var(--accent); }
        .badge-sukses { background: var(--primary); }
        .badge-batal { background: var(--danger); }

        details summary { cursor: pointer; color: var(--info); font-size: 0.85rem; list-style: none; }
        details summary::-webkit-details-marker { display: none; }
        .detail-box { background: #FAFCFA; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-top: 8px; font-size: 0.85rem; }
        .detail-box table { width: 100%; }
        .detail-box th, .detail-box td { padding: 6px 10px; font-size: 0.82rem; }

        select.status-select { padding: 6px 10px; border-radius: 6px; border: 1px solid var(--border-color); font-family: 'Poppins', sans-serif; font-size: 0.8rem; }
        .btn-update { background: var(--primary); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; margin-left: 6px; }
        .btn-update:hover { background: var(--primary-dark); }
        .status-form { display: flex; align-items: center; gap: 4px; }

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
            <li><a href="destinasi.php"><i class="fa-solid fa-map-location-dot"></i> Destinasi Wisata</a></li>
            <li><a href="kuliner.php"><i class="fa-solid fa-utensils"></i> Kuliner Lokal</a></li>
            <li><a href="kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
            <li><a href="buku_tamu.php"><i class="fa-solid fa-book-open"></i> Buku Tamu</a></li>
            <li><a href="transaksi.php" class="active"><i class="fa-solid fa-receipt"></i> Transaksi</a></li>
            <li><a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website</a></li>
            <li><a href="../logout.php" style="margin-top: 20px; color: #ffd6d6;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-user">
                <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <img src="../assets/img/default-avatar.png" alt="Admin" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=2F7B4F&color=fff'">
            </div>
        </header>

        <div class="container">
            <h1 class="page-title">Transaksi Pemesanan Kuliner</h1>

            <div class="row-cards">
                <div class="stat-card primary">
                    <div class="stat-card-info"><h4>Pendapatan Sukses</h4><h2><?= formatRupiah($total_sukses) ?></h2></div>
                    <div class="stat-card-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                </div>
                <div class="stat-card accent">
                    <div class="stat-card-info"><h4>Transaksi Pending</h4><h2><?= $jml_pending ?></h2></div>
                    <div class="stat-card-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                </div>
                <div class="stat-card info">
                    <div class="stat-card-info"><h4>Transaksi Sukses</h4><h2><?= $jml_sukses ?></h2></div>
                    <div class="stat-card-icon"><i class="fa-solid fa-circle-check"></i></div>
                </div>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'update'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Status transaksi berhasil diperbarui.</div>
            <?php endif; ?>

            <div class="filter-bar">
                <a href="transaksi.php" class="<?= $filter_status === '' ? 'active' : '' ?>">Semua</a>
                <a href="transaksi.php?filter=pending" class="<?= $filter_status === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="transaksi.php?filter=sukses" class="<?= $filter_status === 'sukses' ? 'active' : '' ?>">Sukses</a>
                <a href="transaksi.php?filter=batal" class="<?= $filter_status === 'batal' ? 'active' : '' ?>">Batal</a>
            </div>

            <div class="card-table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Total Bayar</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Detail Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($q_transaksi)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>#TRX<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($row['nama_user'] ?? 'Pengguna terhapus') ?></td>
                            <td><?= formatRupiah($row['total_bayar']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $row['status_pembayaran'] ?>"><?= ucfirst($row['status_pembayaran']) ?></span>
                                <form method="POST" class="status-form" style="margin-top:6px;">
                                    <input type="hidden" name="transaksi_id" value="<?= $row['id'] ?>">
                                    <select name="status_pembayaran" class="status-select">
                                        <option value="pending" <?= $row['status_pembayaran'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="sukses" <?= $row['status_pembayaran'] === 'sukses' ? 'selected' : '' ?>>Sukses</option>
                                        <option value="batal" <?= $row['status_pembayaran'] === 'batal' ? 'selected' : '' ?>>Batal</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-update">Ubah</button>
                                </form>
                            </td>
                            <td>
                                <details>
                                    <summary><i class="fa-solid fa-eye"></i> Lihat item</summary>
                                    <div class="detail-box">
                                        <?php
                                        $q_detail = mysqli_query($koneksi, "
                                            SELECT dt.*, k.nama_kuliner AS nama_menu 
                                            FROM detail_transaksi dt 
                                            LEFT JOIN kuliner k ON dt.kuliner_id = k.id 
                                            WHERE dt.transaksi_id = {$row['id']}
                                        ");
                                        if (mysqli_num_rows($q_detail) > 0):
                                        ?>
                                        <table>
                                            <thead><tr><th>Menu</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
                                            <tbody>
                                            <?php while ($d = mysqli_fetch_assoc($q_detail)): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($d['nama_menu'] ?? 'Menu dihapus') ?></td>
                                                    <td><?= $d['jumlah'] ?></td>
                                                    <td><?= formatRupiah($d['subtotal']) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                        <?php else: ?>
                                            <em style="color: var(--text-gray);">Tidak ada item.</em>
                                        <?php endif; ?>
                                    </div>
                                </details>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($q_transaksi) == 0): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-receipt fa-2x" style="margin-bottom:10px;"></i><br>Belum ada transaksi.</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>