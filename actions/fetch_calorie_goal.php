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

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT calorie_goal FROM HealthUsers WHERE user_id = $user_id");
echo json_encode(['calorie_goal' => $result->fetch_assoc()['calorie_goal']]);
?>
