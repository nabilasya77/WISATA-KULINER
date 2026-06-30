<?php
session_start();
require_once 'includes/koneksi.php';

// Jika sudah login, langsung arahkan ke halaman yang sesuai berdasarkan role
if (isset($_SESSION['id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            // Menyimpan data user ke dalam Session
            $_SESSION['id']   = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role']; // Mengambil nilai 'admin' atau 'user' dari database
            
            // Alur Pengalihan (Redirect) berdasarkan Role
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else { $error = "Password salah!"; }
    } else { $error = "Username tidak ditemukan!"; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Wonogiri Wisata</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/img/jembatan-kaca.webp');
            background-size: cover; background-position: center;
        }
        .auth-card { 
            background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px);
            padding: 40px; border-radius: 25px; width: 100%; max-width: 400px; color: white;
            border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            box-sizing: border-box;
        }
        .auth-card h2 { text-align: center; margin-bottom: 25px; }
        .form-group input { 
            width: 100%; padding: 15px; margin-bottom: 15px; box-sizing: border-box;
            background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px; color: white; outline: none;
            font-family: 'Poppins', sans-serif; font-size: 15px;
        }
        .form-group input::placeholder { color: rgba(255, 255, 255, 0.7); }
        .btn-login { 
            width: 100%; padding: 15px; background: #2e7d32; border: none; border-radius: 12px;
            color: white; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 15px;
            font-family: 'Poppins', sans-serif;
        }
        .btn-login:hover { background: #1b5e20; }
        .error { background: #c62828; color: white; padding: 10px; border-radius: 10px; margin-bottom: 15px; text-align: center; font-size: 14px; }
        .link { text-align: center; margin-top: 20px; font-size: 14px; }
        .link a { color: #ffd54f; text-decoration: none; font-weight: 600; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2>Login</h2>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><input type="text" name="username" placeholder="Username" required></div>
            <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
            <button type="submit" name="login" class="btn-login">Masuk</button>
        </form>
        <div class="link">Belum punya akun? <a href="register.php">Daftar sekarang</a></div>
        
    </div>
</body>
</html>