<?php
session_start();
require_once '../user/db_connect.php';
require_once 'admin_check.php';

// Get data from session
$admin_id = $_SESSION['user_id'] ?? 'N/A';
$admin_fname = $_SESSION['FName'] ?? 'Admin';
$admin_lname = $_SESSION['LName'] ?? '';
$admin_email = $_SESSION['Email'] ?? 'Not set';
$admin_role = $_SESSION['Role'] ?? 'Administrator';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>The Lit Kit</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/main.css">
</head>

<body>

<!-- HEADER -->
<header class="top-bar">
    <div style="width:200px;"></div>

    <span class="logo-text">The Lit Kit</span>

    <div style="width:200px; text-align:right;">
        <a href="../user/logout.php" class="sign-in">Logout</a>
    </div>
</header>

<!-- NAV -->
<nav>
    <a href="adminMainPage.php">Home</a>
    <a href="manage_books.php">Books Inventory</a>
    <a href="admin_account.php">Account</a>
</nav>



<!-- MAIN CONTENT -->
<main>

    <div class="div-border">

        <h1>Account Details</h1>

        <h3>Name</h3>
        <input type="text"
            value="<?= htmlspecialchars(($_SESSION['fname']) . ' ' . ($_SESSION['lname'] )) ?>"
            readonly>

        <h3>Email</h3>
        <input type="text"
            value="<?= htmlspecialchars($_SESSION['email'])?>"
            readonly>

        <h3>Role</h3>
        <input type="text"
            value="<?= htmlspecialchars($_SESSION['Role'])?>"
            readonly>

        <h3>ID</h3>
        <input type="text"
            value="<?= htmlspecialchars($_SESSION['user_id'])?>"
            readonly> 


        <div class="div-button">
            <a href="update_admin.php">
                <button type="button">Edit Account</button>
            </a>
        </div>

    </div>

</main>

</body>
</html>