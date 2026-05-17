<?php
// Initialize the database connection

// Default connection onnection credentials for XAMPP
$host = "localhost";
$username = "root";
$password = "";

// Our database name
$dbname = "ceylora";

// Create the Connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check the Connection. If it failed, we stop the website and show the error
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
