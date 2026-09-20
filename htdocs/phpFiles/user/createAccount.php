<?php
    session_start();

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $fname = $_POST['fname'];
      $lname = $_POST['lname'];
      $email = $_POST['email'];
      $hashPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $role = 'user';

      require_once 'db_connect.php'; 

      // Checking if email already used
      $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
      $check->bind_param("s", $email);
      $check->execute();
      $result = $check->get_result();
    
      if($result->num_rows > 0){
        $error = "Email already in use";
      }
    
      else{
        //Inserts info into table
        $stmt = $conn->prepare("INSERT INTO users(FName, LName, Email, password, role) VALUES(?,?,?,?,?)");
        $stmt->bind_param("sssss", $fname, $lname, $email, $hashPassword, $role);
        if($stmt->execute()) {
          $stmt->close();
          $conn->close();
          //Going to sign in page after account creation
          header("Location: signIn.php");
          exit();
        }
        else {
          $error = "Error: " . $stmt->error;
        }
      }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The Lit Kit — Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
  
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --dark: #1a1a1a;
      --border: #e0e0e0;
      --input-border: #c8c8c8;
      --bg: #ffffff;
    }

    html, body {
      height: 100%;
    }

    body {
      font-family: 'EB Garamond', Georgia, serif;
      background: var(--bg);
      color: var(--dark);
      display: flex;
      flex-direction: column;
    }

    /* top header layout */
    .top-bar {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px 60px;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
    }

    .logo {
      text-decoration: none;
    }

    .logo-text {
      font-family: 'Playfair Display', Georgia, serif;
      font-style: italic;
      font-size: 2.0rem;
      color: var(--dark);
      letter-spacing: 0.01em;
    }

    /* split layout for image and form */
    .split {
      display: flex;
      flex: 1;
    }

    .split-left {
      flex: 1;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
    }

  

    .split-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px;
    }

    .form-wrap {
      width: 100%;
      max-width: 360px;
    }

    .form-title {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 2.4rem;
      font-weight: 400;
      color: var(--dark);
      margin-bottom: 38px;
      text-align: center;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 20px;
    }

    .field label {
      font-size: 1rem;
      color: var(--dark);
    }

    /* input fields styling */
    .field input {
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1rem;
      padding: 12px 14px;
      border: 1px solid var(--input-border);
      border-radius: 3px;
      outline: none;
      color: var(--dark);
      background: #fff;
      transition: border-color 0.2s;
    }

    .field input:focus {
      border-color: var(--dark);
    }

    /* create account button */
    .btn-create {
      display: block;
      width: 100%;
      margin-top: 30px;
      background: var(--dark);
      color: #fff;
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.1rem;
      letter-spacing: 0.04em;
      padding: 15px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }

    .btn-create:hover {
      background: #333;
      transform: translateY(-1px);
    }

    .btn-create:active { transform: translateY(0); }

    .already-account {
      margin-top: 20px;
      text-align: center;
      font-size: 1rem;
      color: #888;
    }
    .already-account a {
      color: var(--dark);
      text-decoration: underline;
      text-underline-offset: 3px;
      transition: opacity 0.2s;
    }
    .already-account a:hover { opacity: 0.6; }
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

    <!-- left side for the image -->
    <div class="split-left">
      <!--<span class="img-placeholder"><img src="../../images/reading-news-1600.jpg" style="width:100%;"></span> -->
      <span class="img-placeholder"><img src="../../images/art-literature-1600.jpg" style="width:80%; margin-left:100px;"></span>
    </div>

    <!-- right side has the form fields -->
    <div class="split-right">
      <div class="form-wrap">
        <h1 class="form-title">Create Account</h1>

        <!-- error message for invalid account creation details -->
        <?php if (!empty($error)): ?>
          <p style="color:red;text-align:center;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method  = "POST">

        <div class="field">
          <label for="fname">First Name</label>
          <input type="text" id="fname" name="fname" required/>
        </div>

        <div class="field">
          <label for="lname">Last Name</label>
          <input type="text" id="lname" name="lname" required/>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required/>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required/>
        </div>

        <button class="btn-create">Create Account</button>
        <p class="already-account">Already have an account? <a href="signIn.php">Sign In</a></p>
      </div>
    </div>

  </div>

</body>
</html>