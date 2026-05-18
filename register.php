<?php
require_once "includes/connection.php";
session_start();
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GlobeTrek Adventures</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>

<body class="page-body">
    <?php include "includes/header.php"; ?>
    <div class="page-container">
        <div class="form-card">
            <h2>Create an Account</h2>
            <p class="form-subtitle">Join GlobeTrek and start planning your dream trip</p>
            <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            <?php if ($success) echo "<p class='success'>$success</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="register-name">Full Name</label>
                    <input type="text" id="register-name" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" placeholder="Create a password" required>
                </div>
                <button type="submit" id="register-submit" class="btn btn-register btn-block">Register</button>
            </form>
            <p style="margin-top: 24px; text-align: center; font-size: 14px; color: #777;">
                Already have an account? <a href="login.php" class="text-link">Login</a>
            </p>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>