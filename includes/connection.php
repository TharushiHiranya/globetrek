<?php
// Database details
$host     = "localhost";
$username = "root";
$password = "";
$dbname   = "globetrek";

// Connect to mysql first to see if the database exists
$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("MySQL server connection failed: " . mysqli_connect_error());
}

// Check if database is there
$result = mysqli_query($conn, "SHOW DATABASES LIKE '$dbname'");
if (mysqli_num_rows($result) == 0) {

    // Make the database if it is missing
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$dbname`");
    mysqli_select_db($conn, $dbname);

    // Make the tables
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100) NOT NULL,
        email      VARCHAR(100) NOT NULL UNIQUE,
        password   VARCHAR(255) NOT NULL,
        role       ENUM('customer','staff','admin') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS packages (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        title       VARCHAR(255) NOT NULL,
        destination VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        price       DECIMAL(10,2) NOT NULL,
        image_url   VARCHAR(255),
        is_featured TINYINT(1) DEFAULT 0,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS bookings (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        user_id      INT NOT NULL,
        package_id   INT NOT NULL,
        status       ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
        FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
    )");

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS queries (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        user_id    INT NULL,
        name       VARCHAR(100) NOT NULL,
        email      VARCHAR(100) NOT NULL,
        message    TEXT NOT NULL,
        status     ENUM('open','resolved') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Add users
    mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES
        ('Alice Admin',    'admin@globetrek.com',    'admin123',    'admin'),
        ('Sam Staff',      'staff@globetrek.com',    'staff123',    'staff'),
        ('Chris Customer', 'customer@globetrek.com', 'customer123', 'customer')
    ");

    // Add packages
    mysqli_query($conn, "INSERT INTO packages (title, destination, description, price, image_url, is_featured) VALUES
        ('Explore Machu Picchu', 'Peru',
         'A 5-day adventure through the ancient Inca citadel of Machu Picchu, trekking breathtaking mountain trails with expert local guides.',
         1200.00, 'images/destinations/machu.jpg', 1),
        ('Santorini Escape', 'Greece',
         'Unwind in the iconic whitewashed villages of Santorini over 3 days of luxury sea-view stays and Mediterranean cuisine.',
         850.00,  'images/destinations/santorini.jpg', 1),
        ('Costa Rica Wildlife', 'Costa Rica',
         'A 7-day eco-adventure through lush rainforests, volcanic landscapes, and the richest biodiversity on the planet.',
         950.00,  'images/destinations/costa_rica.jpg', 1),
        ('Tokyo City Explorer', 'Japan',
         'Spend 6 days discovering neon-lit streets, ancient temples, world-class cuisine, and the timeless culture of Tokyo.',
         1500.00, 'images/destinations/tokyo.jpg', 0),
        ('Safari in Serengeti', 'Tanzania',
         'Witness the spectacular Great Migration on a 4-day safari across the vast Serengeti plains at golden hour.',
         2200.00, 'images/destinations/serengeti.jpg', 0)
    ");

    // Add bookings
    mysqli_query($conn, "INSERT INTO bookings (user_id, package_id, status) VALUES
        (3, 1, 'confirmed'),
        (3, 3, 'pending')
    ");

    // Add contact messages
    mysqli_query($conn, "INSERT INTO queries (user_id, name, email, message) VALUES
        (3,    'Chris Customer', 'customer@globetrek.com', 'Do you offer group discounts for the Machu Picchu trip?'),
        (NULL, 'Jane Visitor',   'jane@example.com',       'I am interested in a custom honeymoon package. Please contact me.')
    ");
} else {
    // Use the existing database
    mysqli_select_db($conn, $dbname);
    
    // Ensure is_featured column exists for backward compatibility
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM packages LIKE 'is_featured'");
    if (mysqli_num_rows($col_check) == 0) {
        mysqli_query($conn, "ALTER TABLE packages ADD COLUMN is_featured TINYINT(1) DEFAULT 0");
    }
}
