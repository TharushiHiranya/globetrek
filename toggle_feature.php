<?php
require_once "includes/connection.php";
session_start();

// Make sure only the admin can change featured packages
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Send a 403 Forbidden status and stop the script
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if this is a valid POST request with the required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package_id']) && isset($_POST['is_featured'])) {
    
    // Convert the incoming data into safe integers
    $package_id = (int)$_POST['package_id'];
    $is_featured = (int)$_POST['is_featured'];
    
    // Double check that the featured value is exactly 0 or 1
    $is_featured = ($is_featured === 1) ? 1 : 0;
    
    // Prepare a secure query to update the database
    $stmt = mysqli_prepare($conn, "UPDATE packages SET is_featured = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $is_featured, $package_id);
    
    // Try to execute the update and send back the result
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        // Send a 500 error if the database update failed
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    // Clean up the prepared statement
    mysqli_stmt_close($stmt);
} else {
    // Send a 400 error if the request was missing data
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
