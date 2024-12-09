<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$meal_date = $_POST['meal_date'];
$meal_name = $_POST['meal_name'];
$calories = (int)$_POST['calories'];
$meal_type = $_POST['meal_type'];

$stmt = $conn->prepare("
    INSERT INTO Meals (user_id, meal_date, meal_name, calories, meal_type)
    VALUES (?, ?, ?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param("issis", $user_id, $meal_date, $meal_name, $calories, $meal_type);
    if ($stmt->execute()) {
        echo json_encode(["success" => "Meal added successfully!"]);
    } else {
        echo json_encode(["error" => "Error adding meal: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
}

$conn->close();
?>
