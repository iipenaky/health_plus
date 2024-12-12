<?php
// Start the session to access session data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, send a JSON response to redirect
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

include '../db/db.php';

// Remove all session variables and destroy the session to log out the user
session_unset();
session_destroy();

// Return a JSON response indicating success
echo json_encode(['success' => true]);
exit();
?>
