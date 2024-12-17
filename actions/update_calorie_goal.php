<?php
session_start();

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If not logged in or not a 'user', send a JSON response to redirect the user
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the index page
    header("Location: ../frontend/index.html");
    exit();
}

// Get the user ID and the calorie goal from the session and POST data
$user_id = $_SESSION['user_id'];
$calorie_goal = $_POST['calorie_goal'];

// Validate that the calorie goal is a valid number (optional but recommended)
if (is_numeric($calorie_goal)) {
    // Update the calorie goal in the HealthUsers table for the current user
    $conn->query("UPDATE HealthUsers SET calorie_goal = $calorie_goal WHERE user_id = $user_id");

    // Return a JSON response indicating success
    echo json_encode(['success' => true]);
} else {
    // If the calorie goal is not a valid number, return an error message
    echo json_encode(['error' => 'Invalid calorie goal.']);
}

// Close the database connection (optional but good practice)
$conn->close();
?>
