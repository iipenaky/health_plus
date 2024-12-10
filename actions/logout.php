<?php
// Start the session to have access to session data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, send a redirect response to the login page
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}
include '../db/db.php';
// Remove all session variables to log out the user
session_unset(); // Clears all session data

// Destroy the session completely
session_destroy(); // Ends the session and clears session data on the server

// Redirect the user back to the login page
header('Location: ../frontend/index.html');
exit(); // Stop further script execution after redirect
?>
