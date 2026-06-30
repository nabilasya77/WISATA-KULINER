<?php
require_once 'auth_check.php';
require_once '../includes/koneksi.php';
require_once '../includes/functions.php';

// Ambil data statistik untuk Dashboard
$jml_destinasi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM destinasi_wisata"))['total'];
$jml_kuliner   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kuliner"))['total'];
$jml_user      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users"))['total'];
$jml_ulasan    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ulasan"))['total'];

// Ambil data terbaru untuk tabel (5 destinasi terbaru)
$q_destinasi_baru = mysqli_query($koneksi, "SELECT d.*, kw.nama_kategori FROM destinasi_wisata d LEFT JOIN kategori_wisata kw ON d.kategori_id = kw.id ORDER BY d.id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Wonogiri Wisata</title>
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

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            text-decoration: none;
            color: white;
        }
        .sidebar-brand .logo-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            background: white;
            color: var(--primary-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .sidebar-brand-text { display: flex; flex-direction: column; line-height: 1.2; }
        .sidebar-brand-text .brand-main { font-weight: 700; font-size: 1.1rem; }
        .sidebar-brand-text .brand-sub { font-size: 0.68rem; opacity: 0.8; letter-spacing: 1px; text-transform: uppercase; }

        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 12px 20px;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .sidebar-menu a i { margin-right: 15px; width: 20px; text-align: center; }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: white;
            background: rgba(255,255,255,0.12);
            border-left: 4px solid white;
        }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        .topbar {
            background: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-bottom: 1px solid var(--border-color);
            z-index: 10;
        }
        .topbar-search input {
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            outline: none;
            width: 250px;
        }
        .topbar-search input:focus { border-color: var(--primary); }
        .topbar-user { display: flex; align-items: center; gap: 15px; }
        .topbar-user img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light); }
        .topbar-user span { font-size: 0.9rem; font-weight: 600; color: var(--dark); }

        .container { padding: 30px; overflow-y: auto; }
        .page-title { margin-bottom: 25px; color: var(--dark); font-size: 1.5rem; font-weight: 600; }

        .row-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08);
            border-left: 5px solid;
        }
        .stat-card.primary { border-left-color: var(--primary); }
        .stat-card.accent { border-left-color: var(--accent); }
        .stat-card.info { border-left-color: var(--info); }
        .stat-card.terracotta { border-left-color: var(--terracotta); }

        .stat-card-info h4 { font-size: 0.78rem; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
        .stat-card.primary h4 { color: var(--primary); }
        .stat-card.accent h4 { color: var(--accent); }
        .stat-card.info h4 { color: var(--info); }
        .stat-card.terracotta h4 { color: var(--terracotta); }

        .stat-card-info h2 { font-size: 1.6rem; color: var(--dark); }
        .stat-card-icon {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .stat-card.primary .stat-card-icon { background: var(--primary-light); color: var(--primary); }
        .stat-card.accent .stat-card-icon { background: #FBEED9; color: var(--accent); }
        .stat-card.info .stat-card-icon { background: #DDEEF2; color: var(--info); }
        .stat-card.terracotta .stat-card-icon { background: #F3E0E1; color: var(--terracotta); }

        .card-table { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); overflow: hidden; }
        .card-header { padding: 15px 20px; background: var(--primary-light); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 1rem; color: var(--primary-dark); }
        .btn-sm { padding: 6px 14px; font-size: 0.8rem; border-radius: 20px; color: white; background: var(--primary); text-decoration: none; }
        .btn-sm:hover { background: var(--primary-dark); }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 20px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        th { background: #FAFCFA; color: var(--dark); font-weight: 600; }
        td img { width: 60px; height: 40px; object-fit: cover; border-radius: 5px; }

        .badge { padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; color: white; }
        .badge-aktif { background: var(--primary); }
        .badge-draft { background: var(--accent); }

        .action-btns a { margin-right: 5px; color: white; padding: 5px 8px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; }
        .btn-edit { background: var(--accent); }
        .btn-delete { background: var(--danger); }

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
            <li><a href="index.php" class="active"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
            <li><a href="destinasi.php"><i class="fa-solid fa-map-location-dot"></i> Destinasi Wisata</a></li>
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
                <input type="text" placeholder="Cari data...">
            </div>
            <div class="topbar-user">
                <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <img src="../assets/img/default-avatar.png" alt="Admin Profile" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=2F7B4F&color=fff'">
            </div>
        </header>

        <div class="container">
            <h1 class="page-title">Dashboard</h1>

            <div class="row-cards">
                <div class="stat-card primary">
                    <div class="stat-card-info">
                        <h4>Total Destinasi</h4>
                        <h2><?= $jml_destinasi ?></h2>
                    </div>
                    <div class="stat-card-icon"><i class="fa-solid fa-map"></i></div>
                </div>
                <div class="stat-card accent">
                    <div class="stat-card-info">
                        <h4>Total Kuliner</h4>
                        <h2><?= $jml_kuliner ?></h2>
                    </div>
                    <div class="stat-card-icon"><i class="fa-solid fa-bowl-food"></i></div>
                </div>
                <div class="stat-card info">
                    <div class="stat-card-info">
                        <h4>Total Pengguna</h4>
                        <h2><?= $jml_user ?></h2>
                    </div>
                    <div class="stat-card-icon"><i class="fa-solid fa-users"></i></div>
                </div>
                <div class="stat-card terracotta">
                    <div class="stat-card-info">
                        <h4>Total Ulasan</h4>
                        <h2><?= $jml_ulasan ?></h2>
                    </div>
                    <div class="stat-card-icon"><i class="fa-solid fa-star"></i></div>
                </div>
            </div>

            <div class="card-table">
                <div class="card-header">
                    <h3>Data Destinasi Terbaru</h3>
                    <a href="destinasi_tambah.php" class="btn-sm"><i class="fa-solid fa-plus"></i> Tambah Destinasi</a>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Destinasi</th>
                                <th>Kategori</th>
                                <th>Harga Tiket</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($q_destinasi_baru)): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><img src="<?= htmlspecialchars($row['gambar_utama']) ?>" alt="Img" onerror="this.src='https://via.placeholder.com/60x40'"></td>
                                <td><?= htmlspecialchars($row['nama_destinasi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum') ?></td>
                                <td><?= formatRupiah($row['tiket_masuk']) ?></td>
                                <td>
                                    <?php if($row['status'] == 'aktif'): ?>
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
                            <?php if(mysqli_num_rows($q_destinasi_baru) == 0): ?>
                            <tr><td colspan="7" style="text-align:center;">Belum ada data destinasi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>