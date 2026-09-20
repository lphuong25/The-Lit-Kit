<?php
  require_once 'db_connect.php';
  session_start();

  $firstTime = true;

  if (isset($_SESSION['user_id'])) {

    $check = $conn->prepare("SELECT * FROM prefers WHERE userID = ?");
    $check->bind_param("i", $_SESSION['user_id']);
    $check->execute();
    $result = $check->get_result();

    // if user already has preferences → NOT first time
    if ($result->num_rows > 0) {
        $firstTime = false;
  }
}
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
                echo "<a href='logout.php' class='sign-in'>Logout</a>";
            }
        ?>
    </div>
</header>

  <!-- nav links -->
  <nav>
    <a href="mainPage.php">Home</a>
    <a href="book_rec.php">My Books</a>
    <a href="user_account.php">Account</a>
  </nav>

  <!-- main section of the page -->
  <main class="hero">
    <div class="hero-center">
      <h1 class="hero-title">Everything you <br>need to get back to<br>Literature</h1>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="signIn.php" class="btn-start">Get Started</a>

    <?php elseif ($firstTime): ?>
      <a href="preferences.php" class="btn-start">Get Started</a>
    <?php else: ?>
      <a href="book_rec.php" class="btn-start">Go to Recommendations</a>
    <?php endif; ?>
    </div>
  </main>

</body>
</html>