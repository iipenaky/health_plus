<?php
// Start the session to access user session data
session_start();

// Check if the user is logged in and has the role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If not logged in or not authorized, return a JSON response to redirect the user
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Get the current password, new password, and confirm password from the AJAX request
$currentPassword = $_POST['currentPassword'] ?? ''; // Current password entered by the user
$newPassword = $_POST['newPassword'] ?? '';         // New password entered by the user
$confirmPassword = $_POST['confirmPassword'] ?? ''; // Confirmation of the new password

// Validate the input fields
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    // If any field is empty, return an error response
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    // If the new password and confirmation do not match, return an error response
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

// Query to get the current password hash from the database for the logged-in user
$stmt = $conn->prepare("SELECT password_hash FROM HealthUsers WHERE user_id = ?");
$stmt->bind_param("i", $user_id); // Bind the user_id parameter
$stmt->execute();
$stmt->store_result(); // Store the result to check if a row is returned
$stmt->bind_result($password_hash); // Bind the password_hash column to a variable
$stmt->fetch(); // Fetch the result

// Check if the user exists in the database
if (!$password_hash) {
    // If no password hash is found, return an error response
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

// Verify the entered current password against the stored password hash
if (!password_verify($currentPassword, $password_hash)) {
    // If the current password is incorrect, return an error response
    echo json_encode(['status' => 'error', 'message' => 'Incorrect current password']);
    exit;
}

// Hash the new password using bcrypt
$newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

// Query to update the password in the database for the logged-in user
$stmt = $conn->prepare("UPDATE HealthUsers SET password_hash = ? WHERE user_id = ?");
$stmt->bind_param("si", $newPasswordHash, $user_id); // Bind the new password hash and user_id

// Execute the update query and check if it was successful
if ($stmt->execute()) {
    // If the password update is successful, return a success response
    echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
} else {
    // If the password update fails, return an error response
    echo json_encode(['status' => 'error', 'message' => 'Failed to change password']);
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>
