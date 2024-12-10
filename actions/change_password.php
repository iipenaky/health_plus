<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Return a JSON response indicating the user is not authorized
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}
include '../db/db.php';


$user_id = $_SESSION['user_id'];

// Get the new password and current password from the AJAX request
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Validate the input
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

// Get the current password hash from the database
$stmt = $conn->prepare("SELECT password_hash FROM  HealthUsers   WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($password_hash);
$stmt->fetch();

// Check if the user exists
if (!$password_hash) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

// Verify the current password
if (!password_verify($currentPassword, $password_hash)) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect current password']);
    exit;
}

// Hash the new password
$newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

// Update the password in the database
$stmt = $conn->prepare("UPDATE HealthUsers SET password_hash = ? WHERE user_id = ?");
$stmt->bind_param("si", $newPasswordHash, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to change password']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
