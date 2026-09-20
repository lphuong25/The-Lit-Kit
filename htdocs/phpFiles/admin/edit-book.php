<?php 
require_once '../user/db_connect.php';
require_once 'admin_check.php';  
include 'adminNavBar.php';

//Actual Editing function
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mmsID      = (int)$_POST['mmsID'];
    $title      = trim($_POST['title']);
    $authorID   = (int)$_POST['authorID'];
    $callNumber = trim($_POST['callNumber']);
    $genres     = $_POST['genres'] ?? [];

    $conn->begin_transaction();

    try {
        // Update book
        $stmt = $conn->prepare(
            "UPDATE books 
             SET Title = ?, authorID = ?, callNumber = ?
             WHERE mmsID = ?"
        );
        $stmt->bind_param("sisi", $title, $authorID, $callNumber, $mmsID);
        $stmt->execute();

        // Remove old genres
        $stmt = $conn->prepare("DELETE FROM book_genre WHERE mmsID = ?");
        $stmt->bind_param("i", $mmsID);
        $stmt->execute();

        // Insert new genres
        if (!empty($genres)) {
            $stmt = $conn->prepare(
                "INSERT INTO book_genre (mmsID, genreID) VALUES (?, ?)"
            );

            foreach ($genres as $g) {
                $gid = (int)$g;
                $stmt->bind_param("ii", $mmsID, $gid);
                $stmt->execute();
            }
        }

        $conn->commit();

        // Redirect to avoid resubmission
        header("Location: manage_books.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Update failed: " . $e->getMessage());
    }
}

//Grabbing Data to Reflect on front end
// Get book ID
$mmsID = $_GET['id'];

// Get book details
$stmt = $conn->prepare("SELECT * FROM books WHERE mmsID = ?");
$stmt->bind_param("i", $mmsID);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

// Get authors
$authors = $conn->query("SELECT authorID, authorName FROM author");

// Get genres
$genres = $conn->query("SELECT genreID, genreName FROM genres");

// Get selected genres
$stmt = $conn->prepare("SELECT genreID FROM book_genre WHERE mmsID = ?");
$stmt->bind_param("i", $mmsID);
$stmt->execute();
$result = $stmt->get_result();

$bookGenres = [];
while($row = $result->fetch_assoc()){ 
    $bookGenres[] = $row['genreID'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Book | The Lit Kit</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=EB+Garamond&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../css/style.css">
<link rel="stylesheet" href="../../css/main.css">
</head>

<body>

<form method="POST" class="div-border">

<h1>Edit Book</h1>

<h3>Book Information</h3>

<label>Title</label>
<input type="text" name="title"
       value="<?= htmlspecialchars($book['Title']) ?>">

<label>Author</label>
<select name="authorID">
  <?php while($a = $authors->fetch_assoc()): ?>
    <option value="<?= $a['authorID'] ?>"
      <?= $a['authorID'] == $book['authorID'] ? 'selected' : '' ?>>
      <?= htmlspecialchars($a['authorName']) ?>
    </option>
  <?php endwhile; ?>
</select>

<label>Genres</label>

<div class="dropdown">
  <button type="button" class="dropbtn" onclick="toggleDropdown()">
    Select Genres (Click All That Apply)
  </button>

  <div id="genreDropdown" class="dropdown-content">
    <?php while($g = $genres->fetch_assoc()): ?>
      <label>
        <input type="checkbox"
          name="genres[]"
          value="<?= $g['genreID'] ?>"
          <?= in_array($g['genreID'], $bookGenres) ? 'checked' : '' ?>>
        <span><?= htmlspecialchars($g['genreName']) ?></span>
      </label>
    <?php endwhile; ?>
  </div>
</div>

<label>Call Number</label>
<input type="text" name="callNumber"
       value="<?= htmlspecialchars($book['callNumber']) ?>">

<input type="hidden" name="mmsID" value="<?= $book['mmsID'] ?>">

<div class="div-button">
  <button type="submit">Update Book</button>
</div>

</form>

<script>
function toggleDropdown() {
  document.getElementById("genreDropdown").classList.toggle("show");
}

window.onclick = function(e) {
  if (!e.target.closest('.dropdown')) {
    document.getElementById("genreDropdown").classList.remove("show");
  }
};
</script>

</body>
</html>