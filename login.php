<?php
session_start();
include 'config/koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];

    // Username & Password resmi panitia/admin
    $user_admin = 'admin';
    $pass_admin = 'sejati123'; 

    if ($username_input === $user_admin && $password_input === $pass_admin) {
        // Hanya set session jika password benar-benar cocok!
        $_SESSION['admin_username'] = $user_admin;
        header("Location: paket.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panitia - Arena Sejati</title>
    <style>
        body {
            background: #120c08;
            color: white;
            font-family: 'Outfit', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: rgba(41, 27, 17, 0.9);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #f59e0b;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; }
        .form-group input {
            width: 100%; padding: 12px;
            border-radius: 6px; border: 1px solid #555;
            background: #222; color: white; box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus {
            outline: none;
            border-color: #d97706;
        }
        .btn-login {
            width: 100%; padding: 14px;
            background: #d97706; color: white;
            border: none; border-radius: 6px;
            font-size: 16px; font-weight: bold; cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover { background: #b45309; }
        .error { color: #ef4444; text-align: center; margin-bottom: 15px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Kasir / Panitia</h2>
    <?php if($error != '') echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Masukkan username">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn-login">Masuk ke Sistem</button>
    </form>
</div>

</body>
</html>