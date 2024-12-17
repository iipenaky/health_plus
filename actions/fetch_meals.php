<?php
// Start the session to access user session data
session_start();

// Check if the user is logged in and has the role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If the user is not logged in or not authorized, return a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Get the user ID from the session and today's date
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d"); // Get today's date

// Fetch all meals for the user on the current day
$result = $conn->query("SELECT meal_name, meal_id, calories FROM Meals WHERE user_id = $user_id AND meal_date = '$today'");

// Fetch the meals as an associative array
$meals = $result->fetch_all(MYSQLI_ASSOC);

// Fetch the user's calorie goal from the HealthUsers table
$goal_result = $conn->query("SELECT calorie_goal FROM HealthUsers WHERE user_id = $user_id");
$calorie_goal = $goal_result->fetch_assoc()['calorie_goal'];

// Return the meals and the calorie goal as a JSON response
echo json_encode(['meals' => $meals, 'calorie_goal' => $calorie_goal]);
?>
