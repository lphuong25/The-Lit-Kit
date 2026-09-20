<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signIn.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// USER INFO
$sql = "SELECT FName, LName, Email FROM users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();

// GENRES
$genre_sql = "SELECT g.genreName
              FROM genres g
              JOIN prefers p ON g.genreID = p.genreID
              WHERE p.userID = ?";

$stmt_g = $conn->prepare($genre_sql);
$stmt_g->bind_param("i", $current_user_id);
$stmt_g->execute();
$genre_result = $stmt_g->get_result();

$user_genres = [];
while ($row = $genre_result->fetch_assoc()) {
    $user_genres[] = $row['genreName'];
}

$g1 = $user_genres[0] ?? "None Selected";
$g2 = $user_genres[1] ?? "None Selected";
$g3 = $user_genres[2] ?? "None Selected";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account | The Lit Kit</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=EB+Garamond&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../css/main.css">
<link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<!-- HEADER -->
<header class="top-bar">
    <div style="width:200px;"></div>

    <span class="logo-text">The Lit Kit</span>

    <div style="width:200px; text-align:right;">
        <a href="logout.php" class="sign-in">Logout</a>
    </div>
</header>

<!-- NAV -->
 <!--
< ?php include '../common/nav.php'; ?> -->
<nav>
    <a href="mainPage.php">Home</a>
    <a href="book_rec.php">My Books</a>
    <a href="user_account.php">Account</a>
</nav>



<!-- MAIN CONTENT -->
<main class="card-background-acct">

    <div class="div-border">

        <h1>Account Details</h1>

        <h3>Name</h3>
        <input type="text"
               value="<?php echo htmlspecialchars($userInfo['FName'] . ' ' . $userInfo['LName']); ?>"
               readonly>

        <h3>Email</h3>
        <input type="text"
               value="<?php echo htmlspecialchars($userInfo['Email']); ?>"
               readonly>

        <h1>Preferences</h1>

        <h3>First Choice</h3>
        <input type="text" value="<?php echo htmlspecialchars($g1); ?>" readonly>

        <h3>Second Choice</h3>
        <input type="text" value="<?php echo htmlspecialchars($g2); ?>" readonly>

        <h3>Third Choice</h3>
        <input type="text" value="<?php echo htmlspecialchars($g3); ?>" readonly>

        <div class="div-button">
            <a href="update_account.php?id=<?php echo $current_user_id; ?>">
                <button type="button">Edit Account</button>
            </a>
        </div>

    </div>

</main>

</body>
</html>