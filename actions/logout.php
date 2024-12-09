<?php
// Start the session to have access to session data
session_start();

// Remove all session variables to log out the user
session_unset(); // Clears all session data

// Destroy the session completely
session_destroy(); // Ends the session and clears session data on the server

// Redirect the user back to the login page
header('Location: ../frontend/index.html');
exit(); // Stop further script execution after redirect
?>
