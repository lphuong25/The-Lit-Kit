<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signIn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$rec_sql = "SELECT b.mmsID, b.Title, a.authorName,
                   COALESCE(circulation_counts.popularity, 0) AS popularity
      FROM books b
      JOIN author a ON b.authorID = a.authorID
      LEFT JOIN (
        SELECT mmsID, COUNT(*) AS popularity
        FROM circulation
        GROUP BY mmsID
            ) AS circulation_counts ON b.mmsID = circulation_counts.mmsID
      WHERE EXISTS (
        SELECT 1
        FROM book_genre bg
        JOIN prefers p ON bg.genreID = p.genreID
        WHERE bg.mmsID = b.mmsID
          AND p.userID = ?
      )
      ORDER BY popularity DESC, b.Title ASC
      LIMIT 3";

$stmt = $conn->prepare($rec_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recommendations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>The Lit Kit — My Books</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="../../css/main.css">
<style>

.content {
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 50px;
}

.section-title {
  font-size: 1.4rem;
  margin-bottom: 18px;
}

.book-row {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
}

.book-row a {
  color: inherit;
  text-decoration: none;
}

.book-card {
  width: 150px;
  min-height: 230px;
  background: #f5f3f0;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.book-cover-placeholder {
  height: 125px;
  display: flex;
  align-items: flex-end;
  padding: 14px;
  background-color: #d9d9d9;
  color: #23352d;
}

.cover-1 {
  background-color: #b7c5bd;
  background-image: repeating-linear-gradient(
    -12deg,
    transparent 0,
    transparent 15px,
    rgba(255, 255, 255, 0.3) 16px,
    rgba(255, 255, 255, 0.3) 19px,
    transparent 20px,
    transparent 34px
  );
}

.cover-2 {
  background-color: #b8c4cc;
  background-image: repeating-linear-gradient(
    28deg,
    transparent 0,
    transparent 18px,
    rgba(255, 255, 255, 0.32) 19px,
    rgba(255, 255, 255, 0.32) 22px,
    transparent 23px,
    transparent 38px
  );
  color: #26343d;
}

.cover-3 {
  background-color: #c9bdb5;
  background-image: repeating-linear-gradient(
    90deg,
    transparent 0,
    transparent 22px,
    rgba(255, 255, 255, 0.28) 23px,
    rgba(255, 255, 255, 0.28) 26px,
    transparent 27px,
    transparent 44px
  );
  color: #493c36;
}

.book-cover-placeholder span {
  font-family: "Playfair Display", Georgia, serif;
  font-size: 0.85rem;
  font-style: italic;
}

.book-details {
  padding: 12px 14px;
}

.book-details strong {
  display: block;
  margin-bottom: 5px;
}

.book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.empty-state {
  max-width: 560px;
  padding: 28px;
  border: 1px solid #e0e0e0;
  background: #f5f3f0;
}

.empty-state p {
  margin-bottom: 16px;
}

.empty-state a {
  color: var(--crimson);
  text-decoration: none;
}

.empty-state a:hover {
  text-decoration: underline;
}

@media (max-width: 600px) {
  .content {
    padding: 32px 24px;
  }

  .book-row {
    gap: 16px;
  }
}
</style>
</head>

<body>

  <!-- logo and name at the top -->
 <header class="top-bar">
    <div style="width:200px">
        <?php
          echo "<span class='welcome'>" . $_SESSION['fname'] ." " . $_SESSION['lname'] . "</span>";
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

<nav>
  <a href="mainPage.php">Home</a>
  <a href="book_rec.php">My Books</a>
  <a href="user_account.php">Account</a>
</nav>

<main class="content">

<div class="section">
  <p class="section-title">Popular in Your Favorite Genres</p>
  <div class="book-row">
    <?php if ($recommendations->num_rows === 0): ?>
      <div class="empty-state">
        <p>Choose a few favorite genres to see book recommendations here.</p>
        <a href="preferences.php">Update your preferences</a>
      </div>
    <?php else: ?>
      <?php $cover_index = 0; ?>
      <?php while($row = $recommendations->fetch_assoc()): ?>
      <?php $cover_index++; ?>
      <a href="book_info.php?id=<?php echo $row['mmsID']; ?>">
        <div class="book-card">
          <div class="book-cover-placeholder cover-<?php echo $cover_index; ?>" aria-hidden="true">
            <span>The Lit Kit</span>
          </div>
          <div class="book-details">
            <strong><?php echo htmlspecialchars($row['Title']); ?></strong><br>
            <span style="font-size: 0.85rem; font-style: italic;">
              by <?php echo htmlspecialchars($row['authorName']); ?>
            </span>
          </div>
        </div>
      </a>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>

</main>

</body>
</html>