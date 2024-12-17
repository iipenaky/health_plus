<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
global $conn;

// Start output buffering to prevent any output before JSON response
ob_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and has the role "user"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Return a JSON response indicating redirection (without actually redirecting)
    echo json_encode(['redirect' => '../frontend/index.html']);
    ob_end_flush();
    exit();
}

// Check if the form data is received via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all required POST fields are present
    if (!isset($_POST['steps'], $_POST['water'], $_POST['sleep'], $_POST['mood'], $_POST['exercise'])) {
        echo json_encode(['error' => 'All fields are required.']);
        ob_end_flush();
        exit();
    }

    // Retrieve user ID and current date
    $user_id = $_SESSION['user_id'];
    $activity_date = date("Y-m-d");

    // Sanitize and validate input values
    $steps = intval($_POST['steps']);
    $water = floatval($_POST['water']);
    $sleep = floatval($_POST['sleep']);
    $mood = htmlspecialchars(trim($_POST['mood'])); // Sanitize mood input
    $exercise = floatval($_POST['exercise']);

    // Validation: Ensure no negative or invalid values
    if ($steps <= 0 || $water <= 0 || $sleep <= 0 || empty($mood) || $exercise <= 0) {
        echo json_encode(['error' => 'Please provide valid inputs for all fields.']);
        ob_end_flush();
        exit();
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare(
        "INSERT INTO Health_Activities (user_id, activity_date, steps, water_intake, sleep_hours, exercise_time, mood)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    // Handle SQL errors in preparation
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare the SQL query.']);
        ob_end_flush();
        exit();
    }

    // Bind parameters to the SQL query
    if (!$stmt->bind_param("isidids", $user_id, $activity_date, $steps, $water, $sleep, $exercise, $mood)) {
        echo json_encode(['error' => 'Failed to bind parameters.']);
        ob_end_flush();
        exit();
    }

    // Execute the statement and handle success or failure
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'SQL error: ' . $stmt->error]);
    }

    // Clean up resources
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

// End output buffering and flush the response to the browser
ob_end_flush();
?>
