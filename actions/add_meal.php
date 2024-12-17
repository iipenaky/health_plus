<?php
// Enable error reporting to help debug issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if the user is logged in and has the role of 'user'
// If not, redirect them to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit(); // Stop the script
}

// Include the database connection file
include '../db/db.php';

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Get input values from the POST request
$meal_name = $_POST['meal_name']; // Name of the meal
$calories = (int)$_POST['calories']; // Number of calories, cast to an integer
$meal_type = $_POST['meal_type']; // Type of the meal (e.g., breakfast, lunch)

// Prepare an SQL statement to insert a new meal record into the database
$stmt = $conn->prepare("
    INSERT INTO Meals (user_id, meal_name, calories, meal_type, meal_date)
    VALUES (?, ?, ?, ?, NOW()) -- 'NOW()' sets the current date and time
");

// Check if the statement was prepared successfully
if ($stmt) {
    // Bind the variables to the prepared SQL statement
    $stmt->bind_param("isis", $user_id, $meal_name, $calories, $meal_type);
    
    // Execute the SQL statement and check if it was successful
    if ($stmt->execute()) {
        // Respond with a success message
        echo json_encode(["success" => "Meal added successfully!"]);
    } else {
        // Respond with an error message if execution failed
        echo json_encode(["error" => "Error adding meal: " . $stmt->error]);
    }
    
    // Close the prepared statement
    $stmt->close();
} else {
    // Respond with an error message if statement preparation failed
    echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
}

// Close the database connection
$conn->close();
?>
