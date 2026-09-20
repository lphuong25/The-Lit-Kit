<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = isset($_SESSION['Role']) && $_SESSION['Role'] === 'admin';
?>

<header>
    <div class="top-bar">
        <div class="top-bar-placeholder"></div>
        <a class="logo-text" href="/user/mainPage.php">The Lit Kit</a>
        <div class="profile-container">
            <button class="profile-button" onclick="toggleProfileDropdown()">
                <?php echo htmlspecialchars($_SESSION['fname'] ?? 'Account'); ?>
                <i class="fa-solid fa-angle-down"></i>
            </button>
            <div class="profile-dropdown" id="profile-dropdown">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    <nav>
        <?php if ($is_admin): ?>
            <a class="nav-a" href="../admin/adminMainPage.php">Dashboard</a>
            <a class="nav-a" href="../admin/manage_books.php">Manage Books</a>
            <a class="nav-a" href="../admin/admin_account.php">Account</a>
        <?php else: ?>
            <a class="nav-a" href="/user/mainPage.php">Home</a>
            <a class="nav-a" href="../user/preferences.php">My Books</a>
            <a class="nav-a" href="../user/user_account.php">Account</a>
        <?php endif; ?>
    </nav>
</header>

<script>
function toggleProfileDropdown() {
    document.getElementById('profile-dropdown').classList.toggle('open');
}
document.addEventListener('click', function(event) {
    const container = document.querySelector('.profile-container');
    if (!container.contains(event.target)) {
        document.getElementById('profile-dropdown').classList.remove('open');
    }
});
</script>