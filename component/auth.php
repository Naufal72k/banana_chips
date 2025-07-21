 
    <?php
    // MultipleFiles/auth.php

    session_start(); 

    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    function requireLogin() {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; 
            header('Location: component/login.php');
            exit();
        }
    }

    function loginUser($user_id, $username) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        // Store user-specific cart data in session
        // This will be loaded by keranjang.php's JS
        $_SESSION['user_cart_key'] = 'cart_' . $user_id; // Unique key for user's cart
        if (!isset($_SESSION[$_SESSION['user_cart_key']])) {
            $_SESSION[$_SESSION['user_cart_key']] = []; // Initialize empty cart for new user
        }
    }

    function logoutUser() {
        // Save current user's cart from localStorage to session before destroying
        // This requires JavaScript to send the cart data to a PHP endpoint before logout
        // For simplicity, we'll assume the cart is already saved or will be cleared.
        
        // If a user was logged in, clear their specific cart data from session
        if (isset($_SESSION['user_id'])) {
            $user_cart_key = 'cart_' . $_SESSION['user_id'];
            if (isset($_SESSION[$user_cart_key])) {
                unset($_SESSION[$user_cart_key]); // Clear specific user's cart
            }
        }

        $_SESSION = array(); 
        session_destroy(); 
        setcookie(session_name(), '', time() - 3600, '/');
    }
    ?>
    