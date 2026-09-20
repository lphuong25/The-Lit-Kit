<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'admin') {
    // If not admin, send them back to the user login/main page
    // Path: UP from admin, DOWN into user
    header("Location: ../user/signIn.php?error=unauthorized");
    exit();
}
?>
