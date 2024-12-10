<?php
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

// Check if the form data is received via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_id = $_SESSION['user_id'];
    $activity_date = date("Y-m-d");

    // Assign values to variables, set default to 0 or empty if not set
    $steps = isset($_POST['steps']) ? $_POST['steps'] : 0;
    $water = isset($_POST['water']) ? $_POST['water'] : 0.00;
    $sleep = isset($_POST['sleep']) ? $_POST['sleep'] : 0.00;
    $mood = isset($_POST['mood']) ? $_POST['mood'] : '';
    $exercise = isset($_POST['exercise']) ? $_POST['exercise'] : 0.00;

    // Validate the data
    if ($steps < 0 || $water < 0 || $sleep < 0 || empty($mood) || $exercise < 0) {
        echo json_encode(['error' => 'Please provide valid inputs for all fields.']);
        exit;
    }

    // Prepare and bind the SQL query to insert data into the Health_Activities table
    $stmt = $conn->prepare("INSERT INTO Health_Activities (user_id, activity_date, steps, water_intake, sleep_hours, exercise_time, mood) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare the SQL query.']);
        exit;
    }

    // Bind the parameters to the SQL query
    if (!$stmt->bind_param("isidids", $user_id, $activity_date, $steps, $water, $sleep, $exercise, $mood)) {
        echo json_encode(['error' => 'Failed to bind parameters.']);
        exit;
    }

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Health activity tracked successfully!']);
    } else {
        echo json_encode(['error' => 'SQL error: ' . $stmt->error]);
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>
