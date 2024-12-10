<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Return a JSON response indicating the user is not authorized
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

// Include database connection
include '../db/db.php';


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
