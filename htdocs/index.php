<?php
    session_start();

    if (isset($_SESSION['user_id'])) {
    header("Location: phpFiles/user/mainPage.php");
    }   
    else {
        header("Location: phpFiles/user/mainPage.php");
    }

    exit();
?>