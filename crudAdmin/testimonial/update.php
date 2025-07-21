<?php

session_start();
require_once '../../config/database.php';
require_once '../../component/auth.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
    header('Location: ../../component/login.php'); // Redirect to admin login
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testimonial_id = $_POST['id'] ?? null;
    $new_status = $_POST['status'] ?? null; // New status field

    if (empty($testimonial_id) || empty($new_status)) {
        $_SESSION['error'] = 'ID testimoni atau status tidak valid.';
        header('Location: ../../indexAdmin.php?tab=testimonial');
        exit();
    }

    // Validate allowed statuses
    $allowed_statuses = ['pending', 'published'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error'] = 'Status yang diminta tidak valid.';
        header('Location: ../../indexAdmin.php?tab=testimonial');
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE testimonials SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $testimonial_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Status testimoni berhasil diperbarui.';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui status testimoni: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header('Location: ../../indexAdmin.php?tab=testimonial'); // Redirect to admin testimonial management page
    exit();
} else {
    header('Location: ../../indexAdmin.php?tab=testimonial');
    exit();
}
