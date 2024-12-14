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

// Query to fetch user profile information
$stmt = $conn->prepare("SELECT name, email FROM HealthUsers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email);
$stmt->fetch();

// Check if user exists
if (!$name) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

// Return user data in JSON format
echo json_encode([
    'status' => 'success',
    'name' => $name,
    'email' => $email
]);

// Close the statement and connection
$stmt->close();
$conn->close();
?>
