<?php
// Pick up the current active session
session_start();

// Wipe all stored user data completely
session_destroy();

// Send the user back to the homepage
header("Location: index.php");
exit();
?>
