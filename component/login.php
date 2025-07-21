<?php

require_once '../config/database.php';
require_once 'auth.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = "Username dan password harus diisi.";
    } else {
        // --- START ADMIN LOGIN CHECK ---
        if ($username === 'nopal' && $password === 'admin') {
            // Ini adalah login admin, set user_id khusus atau role di sesi
            loginUser(9999, 'nopal'); // Menggunakan ID fiktif untuk admin
            $_SESSION['is_admin'] = true; // Menandai sebagai admin
            header("Location: ../indexAdmin.php"); // Redirect ke halaman admin
            exit();
        }
        // --- END ADMIN LOGIN CHECK ---

        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                loginUser($user['id'], $user['username']);
                $_SESSION['is_admin'] = false; // Pastikan bukan admin jika login user biasa

                // Load cart for this user
                $user_cart_key = 'cart_' . $user['id'];
                if (isset($_SESSION[$user_cart_key])) {
                    // Store the cart data in a temporary session variable to be picked up by keranjang.php
                    $_SESSION['temp_cart_data'] = $_SESSION[$user_cart_key];
                } else {
                    // If no cart data for this user, ensure it's empty
                    $_SESSION['temp_cart_data'] = [];
                }

                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect_url = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: " . $redirect_url);
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                $error_message = "Username atau password salah.";
            }
        } else {
            $error_message = "Username atau password salah.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Banana Chips</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .primary-color {
            color: #FACC15;
        }

        .bg-primary-color {
            background-color: #FACC15;
        }

        .hover-bg-primary-color:hover {
            background-color: #E5B800;
        }

        /* Background styling for login/register */
        .auth-background {
            background-image: url('../img/home.jpg');
            /* Adjust path as needed */
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .auth-background::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            /* Dark overlay */
            z-index: 1;
        }

        .auth-container {
            position: relative;
            z-index: 2;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen auth-background">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md auth-container">
        <h2 class="text-3xl font-bold text-center primary-color mb-6">Login</h2>
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= $error_message ?></span>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                <input type="text" id="username" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                    required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                    required>
            </div>
            <button type="submit"
                class="w-full bg-primary-color text-white py-2 px-4 rounded-lg hover-bg-primary-color transition duration-300">Login</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-4">Belum punya akun? <a href="register.php"
                class="primary-color hover:underline">Daftar di sini</a></p>
        <p class="text-center text-gray-600 text-sm mt-2"><a href="../index.php"
                class="primary-color hover:underline">Kembali ke Beranda</a></p>
    </div>
</body>

</html>