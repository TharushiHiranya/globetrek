<?php
require_once "includes/connection.php";
session_start();

// Send logged-in users straight to their dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// Check if the user just submitted the registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Clean up the text inputs so they do not break the database
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if someone is already using this email address
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists.";
    } else {
        // Prepare the SQL command to create a new user account
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        
        // Try to save the user and show a success or error message
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful. You can now login.";
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
            <h2>Create an account</h2>
            <p class="form-subtitle">Join GlobeTrek and start planning your dream trip</p>
            <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            <?php if ($success) echo "<p class='success'>$success</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="register-name">Full name</label>
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
            <p class="form-footer-text">
                Already have an account? <a href="login.php" class="text-link">Login</a>
            </p>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>