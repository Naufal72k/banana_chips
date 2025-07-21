<?php
session_start();
require_once '../../config/database.php';
require_once '../../component/auth.php'; // Pastikan ini mengarah ke file auth.php Anda

// Pastikan pengguna adalah admin atau memiliki hak akses yang sesuai
// Anda mungkin perlu menambahkan logika otentikasi admin di sini
// if (!isAdmin()) { ... }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $new_status = $_POST['status'] ?? null;

    if (empty($order_id) || empty($new_status)) {
        $_SESSION['error'] = 'ID pesanan atau status baru tidak valid.';
        header('Location: ../../indexAdmin.php?tab=orders');
        exit();
    }

    // Validasi status yang diizinkan
    $allowed_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error'] = 'Status yang diminta tidak valid.';
        header('Location: ../../indexAdmin.php?tab=orders');
        exit();
    }

    // Ambil status pesanan saat ini untuk validasi tambahan
    $current_status = '';
    $stmt_check = mysqli_prepare($conn, "SELECT status FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $order_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    if ($row = mysqli_fetch_assoc($result_check)) {
        $current_status = $row['status'];
    }
    mysqli_stmt_close($stmt_check);

    // Logika khusus untuk status 'cancelled': hanya bisa dari 'pending'
    if ($new_status === 'cancelled' && $current_status !== 'pending') {
        $_SESSION['error'] = 'Pesanan hanya bisa dibatalkan jika statusnya masih pending.';
        header('Location: ../../indexAdmin.php?tab=orders');
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Status pesanan berhasil diperbarui.';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui status pesanan: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header('Location: ../../indexAdmin.php?tab=orders');
    exit();
} else {
    header('Location: ../../indexAdmin.php?tab=orders');
    exit();
}
