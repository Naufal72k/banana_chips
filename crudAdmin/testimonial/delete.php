<?php

session_start();
require_once '../../config/database.php';
require_once '../../component/auth.php';



if (!isLoggedIn()) { // Or check for admin role
    $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
    header('Location: ../../component/login.php'); // Redirect to admin login
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testimonial_id = $_POST['id'] ?? null;

    if (empty($testimonial_id)) {
        $_SESSION['error'] = 'ID testimoni tidak valid.';
        header('Location: ../../indexAdmin.php?tab=testimonial'); // Redirect to admin testimonial management page
        exit();
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM testimonials WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $testimonial_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Testimoni berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus testimoni: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header('Location: ../../indexAdmin.php?tab=testimonial'); // Redirect to admin testimonial management page
    exit();
} else {
    header('Location: ../../indexAdmin.php?tab=testimonial');
    exit();
}
