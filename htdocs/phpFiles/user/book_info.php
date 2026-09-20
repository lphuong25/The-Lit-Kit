<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signIn.php");
    exit();
}

// Get ID from URL
$book_id = isset($_GET['id']) ? $_GET['id'] : '99101';

// SQL
$sql = "SELECT b.Title, b.callNumber, b.mmsID, a.authorName
        FROM books b
        JOIN author a ON b.authorID = a.authorID
        WHERE b.mmsID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

// Get genre
$genre_sql = "SELECT g.genreName
            FROM genres g
            JOIN book_genre bg ON g.genreID = bg.genreID
            WHERE bg.mmsID = ?";

$stmt_g = $conn->prepare($genre_sql);
$stmt_g->bind_param("s", $book_id);
$stmt_g->execute();
$genre_result = $stmt_g->get_result();
$genres = [];
while($book_genre = $genre_result->fetch_assoc()) {
    $genres[] = $book_genre['genreName'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The Lit Kit — Book Detail</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
<!--links for fonts and style sheet-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Junge&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/user-book-info.css">
    <!---->
</head>
<body>

<header class="top-bar">
  <div style="width:200px;"></div>

  <span class="logo-text">The Lit Kit</span>

  <div style="width:200px; text-align:right;">
    <a href="logout.php" class="logout-link">Logout</a>
  </div>
</header>

  <!-- nav links -->
  <nav>
    <a href="mainPage.php">Home</a>
    <a href="book_rec.php">My Books</a>
    <a href="user_account.php">Account</a>
  </nav>

  <!-- book detail section -->
  <main class="content">
    <div class="book-layout">

      <!-- book cover on the left -->
      <div class="book-left">
        <div class="book-cover"></div>
          <p class="book-info">
              <strong>MMSID:</strong> <?php echo htmlspecialchars($book['mmsID'] ?? 'N/A'); ?> <br>
              <strong>Call Number:</strong> <?php echo htmlspecialchars($book['callNumber'] ?? 'N/A'); ?><br>
              <strong>Genres:</strong> <?php echo !empty($genres) ? htmlspecialchars(implode(", ", $genres)) : 'None'; ?>
          </p>
      </div>

      <!-- title and description on the right -->
      <div class="book-right">
        <h1 class="book-title"><?php echo htmlspecialchars($book['Title'] ?? 'Title Not Found'); ?></h1>

        <h3 class="book-author" style = "font-style: italic; margin-bottom: 20px;">
            by <?php echo htmlspecialchars($book['authorName'] ?? 'Unknown Author'); ?>
        </h3>
      </div>

    </div>

    <!-- button to save the book - Skip for now -->
    <!--
    <div class="btn-wrap">
      <button class="btn-add">Add to MyBooks</button>
    </div>
  </main>
-->

  <script>
    // Added JavaScript to make dropdown interactive
    function toggleMenu() {
      document.getElementById('userMenu').classList.toggle('open');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      const menu = document.getElementById('userMenu');
      if (!menu.contains(e.target)) menu.classList.remove('open');
    });
  </script>

</body>
</html>