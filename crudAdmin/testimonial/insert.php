<?php
// MultipleFiles/crudAdmin/testimonial/insert.php
session_start();
require_once '../../config/database.php';
require_once '../../component/auth.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Anda harus login untuk memberikan testimoni.';
    header('Location: ../../component/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = getUserId();
    $order_id = $_POST['order_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? null;

    if (empty($order_id) || empty($rating) || empty($comment)) {
        $_SESSION['error'] = 'Semua kolom testimoni harus diisi.';
        header('Location: ../../component/history.php');
        exit();
    }

    $rating = intval($rating);
    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = 'Rating harus antara 1 dan 5.';
        header('Location: ../../component/history.php');
        exit();
    }

    // Check if the order belongs to the user and is completed
    $stmt = mysqli_prepare($conn, "SELECT status FROM orders WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$order || $order['status'] !== 'completed') {
        $_SESSION['error'] = 'Anda hanya bisa memberikan testimoni untuk pesanan yang sudah selesai.';
        header('Location: ../../component/history.php');
        exit();
    }


    $stmt_check = mysqli_prepare($conn, "SELECT id FROM testimonials WHERE user_id = ? AND order_id = ?");
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $order_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    if (mysqli_num_rows($result_check) > 0) {
        $_SESSION['error'] = 'Anda sudah memberikan testimoni untuk pesanan ini.';
        header('Location: ../../component/history.php');
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // Insert testimonial
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO testimonials (user_id, order_id, rating, comment) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "iiis", $user_id, $order_id, $rating, $comment);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success'] = 'Testimoni Anda berhasil ditambahkan!';
    } else {
        $_SESSION['error'] = 'Gagal menambahkan testimoni: ' . mysqli_stmt_error($stmt_insert);
    }
    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);

    header('Location: ../../history.php');
    exit();
} else {
    header('Location: ../../history.php');
    exit();
}
