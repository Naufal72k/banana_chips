<?php

require_once '../../config/database.php';
require_once '../../component/auth.php';

header('Content-Type: application/json');

$order_id = $_GET['order_id'] ?? 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit();
}

mysqli_begin_transaction($conn);

try {
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $order_result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($order_result);
    mysqli_stmt_close($stmt);

    if (!$order) {
        throw new Exception("Order not found.");
    }

    $stmt_items = mysqli_prepare($conn, "SELECT product_name, product_price, quantity FROM order_items WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt_items, "i", $order_id);
    mysqli_stmt_execute($stmt_items);
    $items_result = mysqli_stmt_get_result($stmt_items);
    $items = [];
    while ($row = mysqli_fetch_assoc($items_result)) {
        $items[] = $row;
    }
    mysqli_stmt_close($stmt_items);

    mysqli_commit($conn);

    echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
