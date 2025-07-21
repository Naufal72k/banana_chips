<?php

require_once '../config/database.php';
require_once 'auth.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua kolom harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password minimal 6 karakter.";
    } else {
        // Check if username or email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "Username atau email sudah terdaftar.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Registrasi berhasil! Silakan login.";
                // Optional: Redirect to login page after successful registration
                // header("Location: login.php");
                // exit();
            } else {
                $error_message = "Terjadi kesalahan saat registrasi: " . mysqli_stmt_error($stmt);
            }
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
    <title>Register - Banana Chips</title>
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
        <h2 class="text-3xl font-bold text-center primary-color mb-6">Register</h2>
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= $error_message ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline"><?= $success_message ?></span>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                <input type="text" id="username" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                    required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                    required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                    required>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi
                    Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                    required>
            </div>
            <button type="submit"
                class="w-full bg-primary-color text-white py-2 px-4 rounded-lg hover-bg-primary-color transition duration-300">Daftar</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-4">Sudah punya akun? <a href="../component/login.php"
                class="primary-color hover:underline">Login di sini</a></p>
        <p class="text-center text-gray-600 text-sm mt-2"><a href="../index.php"
                class="primary-color hover:underline">Kembali ke Beranda</a></p>
    </div>
</body>

</html>