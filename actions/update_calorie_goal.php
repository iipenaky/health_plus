<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Return a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include database connection
include '../db/db.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page
    header("Location: ../frontend/index.html");
    exit();
}

// Assuming user ID is 1 for this example
$user_id = $_SESSION['user_id'];
$calorie_goal = $_POST['calorie_goal'];
$conn->query("UPDATE HealthUsers SET calorie_goal = $calorie_goal WHERE user_id = $user_id");
echo json_encode(['success' => true]);
?>
