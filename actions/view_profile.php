<?php
// Start the session
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../frontend/index.html'); // Redirect to index page
    exit();
}


$user_id = $_SESSION['user_id'];

// Query to fetch user profile information
$stmt = $conn->prepare("SELECT name, email FROM HealthUsers  WHERE user_id = ?");
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
