<?php
session_start();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
if ($isAjax) {
    header('Content-Type: application/json');
}

require_once '../../config/database.php';
require_once '../../component/auth.php';

if (!isLoggedIn()) {
    $response = [
        'success' => false,
        'message' => 'Anda harus login untuk membuat pesanan.'
    ];

    if ($isAjax) {
        http_response_code(401);
        echo json_encode($response);
    } else {
        $_SESSION['error'] = $response['message'];
        header('Location: ../../login.php');
    }
    exit();
}

$required_fields = ['customer_name', 'customer_phone', 'customer_address', 'payment_method', 'cart_items', 'subtotal', 'shipping', 'total'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $response = [
            'success' => false,
            'message' => 'Data tidak lengkap: ' . $field . ' harus diisi.'
        ];

        if ($isAjax) {
            http_response_code(400);
            echo json_encode($response);
        } else {
            $_SESSION['error'] = $response['message'];
            header('Location: ../../checkout.php');
        }
        exit();
    }
}

$user_id = getUserId();
$customer_name = $_POST['customer_name'];
$customer_phone = $_POST['customer_phone'];
$customer_address = $_POST['customer_address'];
$payment_method = $_POST['payment_method'];
$notes = $_POST['notes'] ?? '';
$cart_items = json_decode($_POST['cart_items'], true);
$subtotal = $_POST['subtotal'];
$shipping = $_POST['shipping'];
$total = $_POST['total'];

$order_code = 'ORD-' . date('YmdHis') . '-' . substr(strtoupper(uniqid()), -6);

try {
    if (mysqli_connect_error()) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    mysqli_begin_transaction($conn);

    $order_query = "INSERT INTO orders (
        user_id,
        order_code,
        customer_name,
        customer_phone,
        customer_address,
        payment_method,
        subtotal,
        shipping_cost,
        total,
        notes,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = mysqli_prepare($conn, $order_query);

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    $bind_result = mysqli_stmt_bind_param(
        $stmt,
        "isssssddds",
        $user_id,
        $order_code,
        $customer_name,
        $customer_phone,
        $customer_address,
        $payment_method,
        $subtotal,
        $shipping,
        $total,
        $notes
    );

    if ($bind_result === false) {
        throw new Exception("Bind failed: " . mysqli_stmt_error($stmt));
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

    $order_id = mysqli_insert_id($conn);

    $item_query = "INSERT INTO order_items (
        order_id,
        product_id,
        product_name,
        product_price,
        quantity,
        subtotal
    ) VALUES (?, ?, ?, ?, ?, ?)";

    $item_stmt = mysqli_prepare($conn, $item_query);

    if ($item_stmt === false) {
        throw new Exception("Prepare items failed: " . mysqli_error($conn));
    }

    foreach ($cart_items as $item) {
        $item_subtotal = $item['price'] * $item['quantity'];
        $bind_result = mysqli_stmt_bind_param(
            $item_stmt,
            "iisidi",
            $order_id,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity'],
            $item_subtotal
        );

        if ($bind_result === false) {
            throw new Exception("Bind items failed: " . mysqli_stmt_error($item_stmt));
        }

        if (!mysqli_stmt_execute($item_stmt)) {
            throw new Exception("Execute items failed: " . mysqli_stmt_error($item_stmt));
        }
    }

    mysqli_commit($conn);

    $_SESSION['should_clear_cart'] = true;
    $_SESSION['order_success'] = $order_code;

    $redirectUrl = '../../payment_success.php?order=' . $order_code;
    $response = [
        'success' => true,
        'redirect' => $redirectUrl
    ];

    if ($isAjax) {
        echo json_encode($response);
    } else {
        header('Location: ' . $redirectUrl);
    }
    exit();

} catch (Exception $e) {
    if (isset($conn) && function_exists('mysqli_rollback')) {
        mysqli_rollback($conn);
    }

    $errorResponse = [
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'error_details' => isset($conn) ? mysqli_error($conn) : null
    ];

    if ($isAjax) {
        http_response_code(500);
        echo json_encode($errorResponse);
    } else {
        $_SESSION['error'] = $errorResponse['message'];
        header('Location: ../../checkout.php');
    }
    exit();
}
