<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Return a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';


$user_id = $_SESSION['user_id'];

// Get name and email
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

// Validate input (simple validation, customize as needed)
if (empty($name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Update the user information in the database
$stmt = $conn->prepare("UPDATE HealthUsers SET name = ?, email = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $name, $email,$user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Information updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update information']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

