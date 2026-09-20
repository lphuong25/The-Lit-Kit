<?php 
require_once '../user/db_connect.php';
require_once 'admin_check.php';  
//include 'adminNavBar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $mmsID      = trim($_POST['mmsID']);
    $title      = trim($_POST['title']);
    $authorID   = (int)$_POST['authorID'];
    $callNumber = trim($_POST['callNumber']);
    $selectedGenres     = $_POST['genres'] ?? [];

    $conn->begin_transaction();

    try {
        //Insert book
        $stmt = $conn->prepare(
            "INSERT INTO books(mmsID, Title, authorID, callNumber) 
            VALUES (?,?,?,?)"
        );
        $stmt->bind_param("ssis", $mmsID, $title, $authorID, $callNumber);
        $stmt->execute();

        //Insert genre into book_genre
        if(!empty($selectedGenres))
        {
            $stmt = $conn->prepare(
                "INSERT INTO book_genre(mmsID, genreID) VALUES(?, ?)"
            );
            foreach($selectedGenres as $g)
            {
                $gid = (int)$g;
                $stmt->bind_param("si", $mmsID, $gid);
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


        

// Get authors
$authors = $conn->query("SELECT authorID, authorName FROM author");

// Get genres
$genres = $conn->query("SELECT genreID, genreName FROM genres");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--links for fonts and style sheet-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Junge&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <!---->
    <title>Add Book</title>
</head>
<body class="centered-body">
    <?php include '../common/nav.php'; ?> 

    <form method="POST" class="div-border">
        <h1>Add Book</h1>
        <h3>Book Information</h3>
        <label for="mmsID">MMS ID</label>
        <input type="text" name="mmsID" id="mmsID" required>

        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>

     <label>Author</label>
        <select name="authorID">
            <?php while($a = $authors->fetch_assoc()): ?>
                <option value="<?= $a['authorID'] ?>">
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
                    value="<?= $g['genreID'] ?>">
                    <span><?= htmlspecialchars($g['genreName']) ?></span>
                </label>
            <?php endwhile; ?>
        </div>
    </div>
    <label>Call Number</label>
    <input type="text" name="callNumber" id="callNumber" required>

        <div class="div-button">
            <button>Add Book</button>
        </div>
    </form>
    <script>
        function toggleDropdown() 
        {
            document.getElementById("genreDropdown").classList.toggle("show");
        }

        window.onclick = function(e) 
        {
            if (!e.target.closest('.dropdown')) {
                document.getElementById("genreDropdown").classList.remove("show");
            }
        };
    </script>
    
</body>
<footer></footer>
</html>