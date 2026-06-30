<?php
require_once 'auth_check.php';
require_once '../includes/koneksi.php';
require_once '../includes/functions.php';

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM buku_tamu WHERE id = $id");
    header("Location: buku_tamu.php?status=hapus");
    exit;
}

$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$where = '';
if ($keyword !== '') {
    $k = mysqli_real_escape_string($koneksi, $keyword);
    $where = "WHERE nama_pengunjung LIKE '%$k%' OR asal_kota LIKE '%$k%'";
}

$q_tamu = mysqli_query($koneksi, "SELECT * FROM buku_tamu $where ORDER BY id DESC");
$total_tamu = mysqli_num_rows($q_tamu);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Digital - Admin Wonogiri Wisata</title>
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
        .badge-count { background: var(--primary-light); color: var(--primary-dark); padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: var(--primary-light); color: var(--primary-dark); border: 1px solid var(--primary); }

        .card-table { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 20px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; vertical-align: top; }
        th { background: #FAFCFA; color: var(--dark); font-weight: 600; }
        .kesan-text { color: var(--text-gray); font-style: italic; max-width: 320px; }
        .tgl-text { font-size: 0.8rem; color: var(--text-gray); white-space: nowrap; }

        .action-btns a { color: white; padding: 6px 9px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; display: inline-block; }
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
            <li><a href="destinasi.php"><i class="fa-solid fa-map-location-dot"></i> Destinasi Wisata</a></li>
            <li><a href="kuliner.php"><i class="fa-solid fa-utensils"></i> Kuliner Lokal</a></li>
            <li><a href="kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
            <li><a href="buku_tamu.php" class="active"><i class="fa-solid fa-book-open"></i> Buku Tamu</a></li>
            <li><a href="transaksi.php"><i class="fa-solid fa-receipt"></i> Transaksi</a></li>
            <li><a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website</a></li>
            <li><a href="../logout.php" style="margin-top: 20px; color: #ffd6d6;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-search">
                <form method="GET" action="buku_tamu.php">
                    <input type="text" name="cari" placeholder="Cari nama / asal kota..." value="<?= htmlspecialchars($keyword) ?>">
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
                <h1 class="page-title">Buku Tamu Digital</h1>
                <span class="badge-count"><i class="fa-solid fa-users"></i> <?= $total_tamu ?> pengunjung tercatat</span>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'hapus'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data buku tamu berhasil dihapus.</div>
            <?php endif; ?>

            <div class="card-table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengunjung</th>
                            <th>Asal Kota</th>
                            <th>Tujuan Kunjungan</th>
                            <th>Kesan & Pesan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($q_tamu)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_pengunjung']) ?></td>
                            <td><?= htmlspecialchars($row['asal_kota']) ?></td>
                            <td><?= htmlspecialchars($row['tujuan_kunjungan']) ?></td>
                            <td class="kesan-text">"<?= htmlspecialchars($row['kesan_pesan']) ?>"</td>
                            <td class="tgl-text"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                            <td class="action-btns">
                                <a href="buku_tamu.php?hapus=<?= $row['id'] ?>" class="btn-delete" title="Hapus" onclick="return confirm('Yakin hapus pesan buku tamu ini?');"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($total_tamu == 0): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-book-open fa-2x" style="margin-bottom:10px;"></i><br>Belum ada pengunjung yang mengisi buku tamu.</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>