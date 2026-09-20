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
                echo "<a href='../../phpFiles/user/logout.php' class='sign-in'>Logout</a>";
            }
        ?>
    </div>
</header>

  <nav>
    <a href="adminMainPage.php">Home</a>
    <a href="manage_books.php">Books Inventory</a>
    <a href="admin_account.php">Account</a>
  </nav>