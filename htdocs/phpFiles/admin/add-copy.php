<?php 
require_once '../user/db_connect.php';
require_once 'admin_check.php'; 
$mmsID = trim($_GET['id']);

if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $barcode = trim($_POST['barcode']);
        $mmsID = trim($_POST['mmsID']);

        try
        {
            $stmt = $conn->prepare(
                "INSERT INTO books_copy(Barcode, mmsID, status) VALUES (?, ?, 'available')"
            );
            $stmt->bind_param("ss", $barcode, $mmsID);
            $stmt->execute();

            header("Location: manage_books.php");
            exit();
        }
        catch(Exception $e)
        {
            die("Insert failed: " . $e->getMessage);
        }
    }
//get the book titles to display in form
$stmt = $conn->prepare("SELECT Title FROM books WHERE mmsID = ?");
$stmt->bind_param("s", $mmsID);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
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
<form method="POST" class="div-border">
    <h1>Add Copy</h1>
    <h3><?= htmlspecialchars($book['Title']) ?></h3>

<!-- pass mmsID as hidden field-->
    <input type="hidden" name="mmsID" value="<?= htmlspecialchars($mmsID) ?>">

    <label for="barcode">Barcode</label>
    <input type="text" name="barcode" id="barcode" required>

    <div class="div-button">
        <button type="submit">Add Copy</button>
    </div>

</form>

</body>
</html>
