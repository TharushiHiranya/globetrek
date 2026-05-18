<?php
require_once "includes/connection.php";
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])) {
    $package_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO bookings (user_id, package_id) VALUES ($user_id, $package_id)";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Booking successful!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error booking package.'); window.location.href='packages.php';</script>";
    }
}
?>
