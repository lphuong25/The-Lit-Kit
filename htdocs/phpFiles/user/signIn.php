<?php
  session_start();

  $error = '';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $inputPassword = $_POST['password'];

    // Connecting Database
    require_once 'db_connect.php';

    //Grabbing email and storing result
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    //Making sure email actually exists in DB
    if ($stmt_result->num_rows > 0) {

      //Grabbing password and verifying it matches 
      $data = $stmt_result->fetch_assoc();

      if (password_verify($inputPassword, $data['Password'])) {
          $_SESSION['user_id'] = $data['userID'];
          $_SESSION['email'] = $data['Email'];
          $_SESSION['fname'] = $data['FName'];
          $_SESSION['lname'] = $data['LName'];
          $_SESSION['Role'] = $data['Role'];

      if ($_SESSION['Role'] === 'admin') {
              // Go UP one level, then into admin *currently make manage_book as a placeholder*
              header("Location: ../admin/adminMainPage.php");
          } else {
              header("Location: ../user/mainPage.php");
              //header(__DIR__.'/mainPage.php');
              //header("Location: /mainPage.php");
          }

          exit();
      } 
      //Wrong Password display error
      else {
            $error = "Invalid Email or Password";
      }

    } 
    //Email not found display error
    else {
      $error = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The Lit Kit — Sign In</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --dark: #1a1a1a;
      --border: #e0e0e0;
      --input-border: #c8c8c8;
      --bg: #ffffff;
    }

    html, body { height: 100%; }

    body {
      font-family: 'EB Garamond', Georgia, serif;
      background: var(--bg);
      color: var(--dark);
      display: flex;
      flex-direction: column;
    }

    /* top navigation bar */
    .top-bar {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px 60px;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
    }

    .logo { text-decoration: none; }

    .logo-text {
      font-family: 'Playfair Display', Georgia, serif;
      font-style: italic;
      font-size: 2.0rem;
      color: var(--dark);
      letter-spacing: 0.01em;
    }

    /* layout split for left image and right form */
    .split {
      display: flex;
      flex: 1;
    }

    .split-left {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: white;
      color: #aaa;
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.3rem;
      letter-spacing: 0.08em;
    }

    .split-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 70px;
    }

    .form-wrap {
      width: 100%;
      max-width: 400px;
    }

    .form-title {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 3rem;
      font-weight: 400;
      color: var(--dark);
      margin-bottom: 48px;
      text-align: center;
    }

    /* input field styling */
    .field {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 28px;
    }

    .field label {
      font-size: 1.2rem;
      color: var(--dark);
    }

    .field input {
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.15rem;
      padding: 14px 16px;
      border: 1px solid var(--input-border);
      border-radius: 3px;
      outline: none;
      color: var(--dark);
      background: #fff;
      transition: border-color 0.2s;
    }

    .field input:focus { border-color: var(--dark); }

    .btn-signin {
      display: block;
      width: 100%;
      margin-top: 36px;
      background: var(--dark);
      color: #fff;
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.2rem;
      letter-spacing: 0.04em;
      padding: 16px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }

    .btn-signin:hover { background: #333; transform: translateY(-1px); }
    .btn-signin:active { transform: translateY(0); }

    .forgot {
      display: block;
      text-align: center; /* previously right*/
      margin-top: 8px;
      font-size: 1rem;
      color: #888;
      text-decoration: none;
      transition: color 0.2s;
    }
    .forgot:hover { color: var(--dark); }

    .create-account {
      margin-top: 28px;
      text-align: center;
      font-size: 1.05rem;
      color: #888;
    }
    .create-account a {
      color: var(--dark);
      text-decoration: underline;
      text-underline-offset: 3px;
      transition: opacity 0.2s;
    }
    .create-account a:hover { opacity: 0.6; }
  </style>
</head>
<body>
  <!-- logo at the top -->
  <header class="top-bar">
    <a href="#" class="logo">
      <span class="logo-text">The Lit Kit</span>
    </a>
  </header>

  <!-- left and right sections -->
  <div class="split">

    <!-- left side for the image <img src="../../images/art-literature-1600.jpg" style="width:80%; margin-left:100px;">-->
    <div class="split-left">
      <img src="../../images/reading-news-1600.jpg" style="width:96%;">
    </div>
    

    <!-- right side has sign in form -->
    <div class="split-right">
      <div class="form-wrap">
        <h1 class="form-title">Sign In To Get Started!</h1>

        <!-- error message for invalid login -->
        <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method = "POST">

        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required/>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required/>
        </div>
        <a href="#" class="forgot">Forgot password?</a>

	<!-- submit button for signing in -->
        <button class="btn-signin">Sign in</button>
        <p class="create-account">Don't have an account? <a href="createAccount.php">Create Account</a></p>
      </div>
    </div>

  </div>

</body>
</html>