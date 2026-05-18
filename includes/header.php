<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="site-header">
    <a href="index.php" class="site-logo-link">
        <img src="images/branding/logo.png" alt="GlobeTrek Adventures Logo" class="site-logo" />
    </a>

    <nav class="site-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="packages.php">Packages</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <div class="header-auth">
        <?php if(isset($_SESSION['user_id'])) { ?>
            <a href="dashboard.php" class="btn btn-login">Dashboard</a>
            <a href="logout.php" class="btn btn-register">Logout</a>
        <?php } else { ?>
            <a href="login.php" class="btn btn-login">Login</a>
            <a href="register.php" class="btn btn-register">Register</a>
        <?php } ?>
    </div>
</header>