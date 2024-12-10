<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Return a JSON response indicating the user is not authorized
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

// Include database connection
include '../db/db.php';


// Assuming user ID is 1 for this example
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT calorie_goal FROM HealthUsers WHERE user_id = $user_id");
echo json_encode(['calorie_goal' => $result->fetch_assoc()['calorie_goal']]);
?>
