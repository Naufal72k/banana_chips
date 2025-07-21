    // MultipleFiles/logout.php
    <?php

    require_once 'auth.php';

    // Before logging out, we need to save the current cart from localStorage to session
    // This requires an AJAX call from the frontend before redirecting to logout.php
    // For now, we'll just clear the cart on logout.

    logoutUser();

    // Add a flag to indicate that the cart should be cleared on the client side
    // $_SESSION['clear_cart_on_load'] = true; // Hapus baris ini

    header("Location: ../index.php"); 
    exit();
    ?>
    