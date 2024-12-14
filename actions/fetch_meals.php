<?php
// Start the session
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
$today = date("Y-m-d");

$result = $conn->query("SELECT meal_name, calories FROM Meals WHERE user_id = $user_id AND meal_date = '$today'");
$meals = $result->fetch_all(MYSQLI_ASSOC);

$goal_result = $conn->query("SELECT calorie_goal FROM HealthUsers WHERE user_id = $user_id");
$calorie_goal = $goal_result->fetch_assoc()['calorie_goal'];

echo json_encode(['meals' => $meals, 'calorie_goal' => $calorie_goal]);
?>
