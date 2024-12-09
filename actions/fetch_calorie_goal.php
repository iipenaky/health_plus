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


// Assuming user ID is 1 for this example
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT calorie_goal FROM Users WHERE user_id = $user_id");
echo json_encode(['calorie_goal' => $result->fetch_assoc()['calorie_goal']]);
?>
