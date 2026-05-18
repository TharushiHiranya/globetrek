<?php
require_once "includes/connection.php";
session_start();

// Make sure only logged-in customers can book a trip
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    // Send them to the login page if they are not allowed here
    header("Location: login.php");
    exit();
}

// Check if a package ID was sent in the URL
if (isset($_GET['id'])) {
    
    // Turn the ID into an integer for safety
    $package_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Prepare the SQL command to save the new booking
    $sql = "INSERT INTO bookings (user_id, package_id) VALUES ($user_id, $package_id)";
    
    // Try to save the booking and show a popup message
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Booking successful.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error booking package.'); window.location.href='packages.php';</script>";
    }
}
?>
