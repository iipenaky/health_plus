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

if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page
    header("Location: ../frontend/index.html");
    exit();
}

// Assuming user ID is 1 for this example
$user_id = $_SESSION['user_id'];
$calorie_goal = $_POST['calorie_goal'];
$conn->query("UPDATE Users SET calorie_goal = $calorie_goal WHERE user_id = $user_id");
echo json_encode(['success' => true]);
?>
