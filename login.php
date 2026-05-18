<?php
require_once "includes/connection.php";
session_start();

// Send logged-in users straight to their dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Check if the user just tried to log in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Clean up the email so it does not break the database
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Look for a user with this exact email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    // Make sure exactly one user was found
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if the typed password matches the one in the database
        if ($password === $row['password']) {
            
            // Save their details into the active session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];
            
            // Send them to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Show an error if the password is wrong
            $error = "Invalid password.";
        }
    } else {
        // Show an error if the email is not in the database
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GlobeTrek Adventures</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>

<body class="page-body">
    <?php include "includes/header.php"; ?>
    <div class="page-container">
        <div class="form-card">
            <h2>Welcome back</h2>
            <p class="form-subtitle">Sign in to manage your bookings and trips</p>
            <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" id="login-submit" class="btn btn-register btn-block">Login</button>
            </form>
            <p class="form-footer-text">
                Don't have an account? <a href="register.php" class="text-link">Register</a>
            </p>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>