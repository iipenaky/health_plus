<?php
// Start the session
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
$today = date("Y-m-d");

$result = $conn->query("SELECT meal_name, calories FROM Meals WHERE user_id = $user_id AND meal_date = '$today'");
$meals = $result->fetch_all(MYSQLI_ASSOC);

$goal_result = $conn->query("SELECT calorie_goal FROM Users WHERE user_id = $user_id");
$calorie_goal = $goal_result->fetch_assoc()['calorie_goal'];

echo json_encode(['meals' => $meals, 'calorie_goal' => $calorie_goal]);
?>
