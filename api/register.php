<?php
session_start();
require_once 'includes/koneksi.php';

// Jika sudah login, lempar ke index
if (isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $email    = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validasi kecocokan password
    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        // Cek apakah username sudah terdaftar
        $cek_username = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_username) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password menggunakan BCRYPT (Sama dengan password_verify di login.php)
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Query insert data user baru dengan role default 'user'
            $query = "INSERT INTO users (nama, username, email, password, role) VALUES ('$nama', '$username', '$email', '$hash', 'user')";
            
            if (mysqli_query($koneksi, $query)) {
                $success = "Akun berhasil dibuat! Silakan <a href='login.php'>Login</a>";
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Wonogiri Wisata</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            margin: 0; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/img/jembatan-kaca.webp');
            background-size: cover; 
            background-position: center;
        }
        .auth-card { 
            background: rgba(255, 255, 255, 0.15); 
            backdrop-filter: blur(15px);
            padding: 40px; 
            border-radius: 25px; 
            width: 100%;
            max-width: 400px; 
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            box-sizing: border-box;
        }
        .form-group input { 
            width: 100%; 
            padding: 15px; 
            margin-bottom: 15px; 
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.1); 
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px; 
            color: white; 
            outline: none;
            font-family: 'Poppins', sans-serif;
        }
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-register { 
            width: 100%; 
            padding: 15px; 
            background: #1b5e20; 
            border: none; 
            border-radius: 12px;
            color: white; 
            font-weight: 600; 
            cursor: pointer; 
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }
        .btn-register:hover { 
            background: #e65100; 
        }
        .error { 
            background: #ffebee; 
            color: #c62828; 
            padding: 12px; 
            border-radius: 10px; 
            margin-bottom: 15px; 
            text-align: center; 
            font-size: 14px;
        }
        .success { 
            background: #e8f5e9; 
            color: #2e7d32; 
            padding: 12px; 
            border-radius: 10px; 
            margin-bottom: 15px; 
            text-align: center; 
            font-size: 14px;
        }
        .success a {
            color: #1b5e20;
            font-weight: bold;
            text-decoration: underline;
        }
        .link { 
            text-align: center; 
            margin-top: 18px; 
            font-size: 14px; 
        }
        .link a { 
            color: #FFD54F; 
            text-decoration: none; 
            font-weight: 600;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <h2 style="text-align: center; margin-top: 0; margin-bottom: 25px;">Daftar Akun</h2>

    <?php if ($error != ''): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success != ''): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
        </div>
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
        </div>
        <button type="submit" name="register" class="btn-register">Daftar Sekarang</button>
    </form>

    <div class="link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>

</body>
</html>