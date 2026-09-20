<?php
session_start();
require_once 'db_connect.php';

$current_user_id = $_SESSION['user_id'];

$all_genres_query = $conn->query("SELECT genreName FROM genres ORDER BY genreName ASC");
$all_genres = [];
while($row = $all_genres_query->fetch_assoc()) {
    $all_genres[] = $row['genreName'];
}

// Handle update
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $parts = explode(' ', trim($name), 2);
    $fName = $parts[0];
    $lName = isset($parts[1]) ? $parts[1] : '';
    $email = $_POST['email'];

    $updateUser = $conn->prepare("UPDATE users SET FName = ?, LName = ?, Email = ? WHERE userID = ?");
    $updateUser->bind_param("sssi", $fName, $lName, $email, $current_user_id);
    $updateUser->execute();

    $_SESSION['fname'] = $fName;
    $_SESSION['lname'] = $lName;
    $_SESSION['email'] = $email;

    // delete old prefer data
    $deletePrefer = $conn->prepare("DELETE FROM prefers WHERE userID = ?");
    $deletePrefer->bind_param("i", $current_user_id);
    $deletePrefer->execute();

    // insert new prefer data (can be empty)
    $newGenres = [
        $_POST['genre1'] ?? '', 
        $_POST['genre2'] ?? '', 
        $_POST['genre3'] ?? ''
    ];

    // Remove empty selections and duplicates
    $filteredGenres = array_filter(array_unique($newGenres));

    foreach($filteredGenres as $gName) {
            if (!empty($gName)) {
                $getGid = $conn->prepare("SELECT genreID FROM genres WHERE genreName = ?");
                $getGid->bind_param("s", $gName);
                $getGid->execute();
                $result = $getGid->get_result();

                if ($row = $result->fetch_assoc()) {
                    $gid = $row['genreID'];
                    $insert = $conn->prepare("INSERT INTO prefers (userID, genreID) VALUES (?, ?)");
                    $insert->bind_param("ii", $current_user_id, $gid);
                    $insert->execute();
                }
            }
        }
    $_SESSION['success'] = "Account and Preferences updated!";
    header("Location: user_account.php");
    exit();
}

// Fetch and display personal data
$statement = $conn->prepare("SELECT FName, LName, Email FROM users WHERE userID = ?");
$statement->bind_param("i", $current_user_id);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();

$firstName = $user['FName'];
$lastName = $user['LName'];
$email = $user['Email'];

// Fetch and display prefered genre
$genreStatement = $conn->prepare("SELECT g.genreName FROM genres g JOIN prefers p ON g.genreID = p.genreID WHERE p.userID = ?");
$genreStatement->bind_param("i", $current_user_id);
$genreStatement->execute();
$preferGenres = $genreStatement->get_result()->fetch_all(MYSQLI_ASSOC);

$genre1 = isset($preferGenres[0]) ? $preferGenres[0]['genreName'] : '';
$genre2 = isset($preferGenres[1]) ? $preferGenres[1]['genreName'] : '';
$genre3 = isset($preferGenres[2]) ? $preferGenres[2]['genreName'] : '';

if (isset($_POST['delete_acc'])) {
    // Delete related data

    // fix: no longer draw current user id from session, in case of manipulated session
    $stmt = $conn->prepare("DELETE FROM book_log WHERE performedBy = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM is_recommended WHERE userID = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    
    // Delete user
    $conn->query("DELETE FROM users WHERE userID = $current_user_id");

    // Destroy session (user no longer exists)
    session_destroy();

    // Redirect to main page
    header("Location: mainPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Account | The Lit Kit</title>

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
<nav>
    <a href="mainPage.php">Home</a>
    <a href="book_rec.php">My Books</a>
    <a href="user_account.php">Account</a>
</nav>

<!-- MAIN -->
<main>

<form action="update_account.php" method="POST">

<main class="card-background-acct">

<div class="div-border">

    <!-- ACCOUNT DETAILS -->
    <h1>Update Account</h1>

    <label for="name">Name</label>
    <input type="text" name="name" id="name"
           value="<?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>" required>

    <label for="email">Email</label>
    <input type="text" name="email" id="email"
           value="<?php echo htmlspecialchars($email); ?>" required>

    <!-- PREFERENCES -->
    <h1>Update Preferences</h1>
    <h3>Your top 3 genres</h3>

    <?php 
        for ($i = 1; $i <= 3; $i++) {
            $current_val = ${"genre" . $i};

            echo "<label for='genre$i'>Choice $i (Optional)</label>";
            echo "<div class='select-wrap'>";
            // REMOVED 'required' from here
            echo "<select id='genre$i' name='genre$i'>";

            // Changed 'disabled' to 'enabled' so user can switch back to "no choice"
            echo "<option value='' " . ($current_val ? "" : "selected") . ">None / Select a genre</option>";

            foreach ($all_genres as $option) {
                $selected = ($option == $current_val) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
            }

            echo "</select>";
            echo "</div>";
        }
    ?>

    <!-- UPDATE BUTTON -->
    <div class="div-button">
        <button type="submit" name="update">Update Account</button>
    </div>

</div>
</form>

</main>

<!-- DELETE BOX -->
<div class="div-border delete-box">

    <h1>Delete Account</h1>

    <p class="delete-text">
        This action is permanent and cannot be undone.
    </p>

    <form method="POST" onsubmit="return confirm('Are you sure? This is permanent.');">
        <div class="div-button">
            <button type="submit" name="delete_acc" class="delete-button">
                Delete Account
            </button>
        </div>
    </form>

</div>

<script>
const selects = [
    document.getElementById('genre1'),
    document.getElementById('genre2'),
    document.getElementById('genre3')
];

function updateDropdowns() {
    const selectedValues = selects.map(s => s.value).filter(v => v !== "");

    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            option.disabled = false;

            if (
                option.value !== "" &&
                option.value !== select.value &&
                selectedValues.includes(option.value)
            ) {
                option.disabled = true;
            }
        });
    });
}

selects.forEach(select => {
    select.addEventListener('change', updateDropdowns);
});

updateDropdowns();
</script>

</body>
</html>