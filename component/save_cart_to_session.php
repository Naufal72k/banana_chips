    // MultipleFiles/save_cart_to_session.php
    <?php
    // MultipleFiles/save_cart_to_session.php
    session_start();
    require_once 'auth.php'; // Include auth.php for isLoggedIn() and getUserId()

    header('Content-Type: application/json');

    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $cart_data = $input['cart'] ?? [];
    $user_id = $input['user_id'] ?? null;

    // Ensure the user_id from the request matches the logged-in user_id
    if ($user_id !== getUserId()) {
        echo json_encode(['success' => false, 'message' => 'User ID mismatch.']);
        exit();
    }

    // Store the cart data in the user's specific session key
    $user_cart_key = 'cart_' . $user_id;
    $_SESSION[$user_cart_key] = $cart_data;

    echo json_encode(['success' => true, 'message' => 'Cart saved to session.']);
    ?>
    