<?php

require_once 'auth.php';
if (isset($_GET['url'])) {
    $_SESSION['redirect_after_login'] = $_GET['url'];
    echo 'OK';
} else {
    echo 'No URL provided';
}
?>
