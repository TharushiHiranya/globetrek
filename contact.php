<?php
require_once "includes/connection.php";
session_start();
$msg = '';

// Check if the user just submitted the contact form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Clean up the text inputs so they do not break the database
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Check if the user is logged in, otherwise save them as a guest (NULL)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';

    // Prepare the SQL command to save the new message
    $sql = "INSERT INTO queries (user_id, name, email, message) VALUES ($user_id, '$name', '$email', '$message')";

    // Try to run the query and show a success or error message on the page
    if (mysqli_query($conn, $sql)) {
        $msg = "<p class='success'>Your query has been submitted successfully.</p>";
    } else {
        $msg = "<p class='error'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - GlobeTrek Adventures</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>

<body class="page-body">
    <?php include "includes/header.php"; ?>
    <div class="page-container">
        <div class="form-card">
            <h2>Contact us</h2>
            <p class="form-subtitle">Have a question? We want to hear from you.</p>
            <?php echo $msg; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="contact-name">Name</label>
                    <input type="text" id="contact-name" name="name" placeholder="Your full name" required value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="contact-email">Email</label>
                    <input type="email" id="contact-email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label for="contact-message">Message</label>
                    <textarea id="contact-message" name="message" rows="5" placeholder="Tell us how we can help" required></textarea>
                </div>
                <button type="submit" id="contact-submit" class="btn btn-register btn-block">Send message</button>
            </form>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>

</html>