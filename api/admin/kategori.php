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

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM kategori_wisata WHERE id = $id");
    header("Location: kategori.php?status=hapus");
    exit;
}

// Mode edit: load data untuk ditampilkan di form
$edit_data = ['id' => '', 'nama_kategori' => ''];
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $cek = mysqli_query($koneksi, "SELECT * FROM kategori_wisata WHERE id = $id");
    if ($row = mysqli_fetch_assoc($cek)) {
        $edit_data = $row;
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori']);
    $id_post = (int) $_POST['id'];
    $slug = buatSlug($nama_kategori);

    if ($nama_kategori === '') {
        $error = 'Nama kategori wajib diisi.';
    } else {
        if ($id_post > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE kategori_wisata SET nama_kategori=?, slug=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssi", $nama_kategori, $slug, $id_post);
            mysqli_stmt_execute($stmt);
            header("Location: kategori.php?status=edit");
            exit;
        } else {
            $slug = $slug . '-' . time();
            $stmt = mysqli_prepare($koneksi, "INSERT INTO kategori_wisata (nama_kategori, slug) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $nama_kategori, $slug);
            mysqli_stmt_execute($stmt);
            header("Location: kategori.php?status=tambah");
            exit;
        }
    }
}

$q_kategori = mysqli_query($koneksi, "
    SELECT kw.*, (SELECT COUNT(*) FROM destinasi_wisata dw WHERE dw.kategori_id = kw.id) AS jumlah_destinasi
    FROM kategori_wisata kw
    ORDER BY kw.nama_kategori ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Wisata - Admin Wonogiri Wisata</title>
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

        .layout-grid { display: grid; grid-template-columns: 320px 1fr; gap: 24px; align-items: start; }

        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; grid-column: 1 / -1; }
        .alert-success { background: var(--primary-light); color: var(--primary-dark); border: 1px solid var(--primary); }
        .alert-error { background: #FBEAEA; color: var(--danger); border: 1px solid var(--danger); grid-column: 1 / -1; }

        .form-card { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); padding: 24px; }
        .form-card h3 { font-size: 1rem; color: var(--primary-dark); margin-bottom: 16px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--dark); margin-bottom: 6px; }
        input { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; font-family: 'Poppins', sans-serif; outline: none; background: #FAFCFA; margin-bottom: 14px; }
        input:focus { border-color: var(--primary); background: white; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 10px 24px; border-radius: 20px; font-size: 0.9rem; cursor: pointer; font-weight: 500; width: 100%; }
        .btn-save:hover { background: var(--primary-dark); }
        .btn-cancel-link { display: block; text-align: center; margin-top: 10px; color: var(--text-gray); font-size: 0.85rem; text-decoration: none; }

        .card-table { background: white; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(31, 61, 43, 0.08); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 20px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
        th { background: #FAFCFA; color: var(--dark); font-weight: 600; }
        .action-btns a { margin-right: 5px; color: white; padding: 6px 9px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; display: inline-block; }
        .btn-edit { background: var(--accent); }
        .btn-delete { background: var(--danger); }
        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-gray); }

        @media (max-width: 800px) { .layout-grid { grid-template-columns: 1fr; } }
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
            <li><a href="kategori.php" class="active"><i class="fa-solid fa-tags"></i> Kategori</a></li>
            <li><a href="buku_tamu.php"><i class="fa-solid fa-book-open"></i> Buku Tamu</a></li>
            <li><a href="transaksi.php"><i class="fa-solid fa-receipt"></i> Transaksi</a></li>
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
            <h1 class="page-title">Kategori Wisata</h1>

            <div class="layout-grid">
                <?php if (isset($_GET['status']) && $_GET['status'] === 'hapus'): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Kategori berhasil dihapus.</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'tambah'): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Kategori baru berhasil ditambahkan.</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'edit'): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Kategori berhasil diperbarui.</div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="form-card">
                    <h3><?= $edit_data['id'] ? 'Edit Kategori' : 'Tambah Kategori' ?></h3>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_data['id']) ?>">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" required value="<?= htmlspecialchars($edit_data['nama_kategori']) ?>" placeholder="Contoh: Wisata Alam, Wisata Religi">
                        <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        <?php if ($edit_data['id']): ?>
                            <a href="kategori.php" class="btn-cancel-link">Batal edit</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="card-table">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Jumlah Destinasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($q_kategori)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                <td><?= $row['jumlah_destinasi'] ?> destinasi</td>
                                <td class="action-btns">
                                    <a href="kategori.php?edit=<?= $row['id'] ?>" class="btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="kategori.php?hapus=<?= $row['id'] ?>" class="btn-delete" title="Hapus" onclick="return confirm('Yakin hapus kategori ini? Destinasi terkait akan jadi tanpa kategori.');"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($q_kategori) == 0): ?>
                            <tr><td colspan="4"><div class="empty-state"><i class="fa-solid fa-tags fa-2x" style="margin-bottom:10px;"></i><br>Belum ada kategori.</div></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>
</html>