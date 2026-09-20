<?php
  require_once '../user/db_connect.php';
  require_once 'admin_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The Lit Kit</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../../css/main.css">
</head>
<body>

  <!-- logo and welcome at the top -->
 <header class="top-bar">
    <div style="width:200px">
        <?php
          if (isset($_SESSION['fname'])) {
            echo "<span class='welcome'>Welcome, " . $_SESSION['fname'] . "</span>";
          }
        ?>
    </div>

    <span class="logo-text">The Lit Kit</span>

    <div style="width:200px; text-align:right;">
        <?php
            if (isset($_SESSION['user_id'])) {
                echo "<a href='../../phpFiles/user/logout.php' class='sign-in'>Logout</a>";
            }
        ?>
    </div>
</header>

  <!-- nav links -->
  <nav>
    <a href="adminMainPage.php">Home</a>
    <a href="manage_books.php">Books Inventory</a>
    <a href="admin_account.php">Account</a>
  </nav>

  <!-- main section of the page -->
  <main class="hero">
    <div class="hero-center">
      <h1 class="hero-title">Everything you <br>need to get back to<br>Literature</h1>
        <a href="preferences.php" class="btn-start">Get Started</a>
    </div>
  </main>

</body>
</html>