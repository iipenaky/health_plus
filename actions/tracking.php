<?php
// Start the session
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../frontend/index.html'); // Redirect to index page
    exit();
}


// Check if the form data is received via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_id = $_SESSION['user_id'];  // Assuming the logged-in user's ID is stored in session
    $activity_date = date("Y-m-d");   // Get the current date

    // Assign values to variables, set default to 0 or empty if not set
    $steps = !empty($_POST['steps']) ? $_POST['steps'] : 0;
    $water = !empty($_POST['water']) ? $_POST['water'] : 0.00;
    $sleep = !empty($_POST['sleep']) ? $_POST['sleep'] : 0.00;
    $mood = !empty($_POST['mood']) ? $_POST['mood'] : '';
    $exercise = !empty($_POST['exercise']) ? $_POST['exercise'] : 0.00;

    // Validate the data (make sure values are not empty)
    if ($steps == 0 || $water == 0.00 || $sleep == 0.00 || empty($mood) || $exercise == 0.00) {
        echo json_encode(['error' => 'Please fill in all fields.']);
        exit;
    }

    // Prepare and bind the SQL query to insert data into the Health_Activities table
    $stmt = $conn->prepare("INSERT INTO Health_Activities (user_id, activity_date, steps, water_intake, sleep_hours, exercise_time, mood) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Bind the parameters to the SQL query
    $stmt->bind_param("isidids", $user_id, $activity_date, $steps, $water, $sleep, $exercise, $mood);

    // Execute the query
    if ($stmt->execute()) {
        // Return success message if activity is tracked successfully
        echo json_encode(['success' => 'Health activity tracked successfully!']);
    } else {
        // Return error message if query fails
        echo json_encode(['error' => 'An error occurred. Please try again.']);
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>
