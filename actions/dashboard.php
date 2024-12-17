<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access user session data
session_start();

// Check if the user is logged in and has the role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If the user is not logged in or not authorized, return a JSON response to redirect the user
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Check if the database connection was successful
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get the logged-in user's ID from the session and today's date
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d"); // Get today's date using PHP's date function

// Query to check if an entry for today's activity already exists for the user
$checkExistsQuery = "
    SELECT activity_id
    FROM Health_Activities
    WHERE user_id = ? AND activity_date = ?
    LIMIT 1
";

$checkStmt = $conn->prepare($checkExistsQuery); // Prepare the SQL statement
$checkStmt->bind_param("is", $user_id, $today); // Bind user_id and today's date to the query
$checkStmt->execute(); // Execute the query
$checkResult = $checkStmt->get_result(); // Get the result

// If no entry exists for today, create a new one with default values (steps, water, sleep, mood)
if ($checkResult->num_rows == 0) {
    $insertQuery = "
        INSERT INTO Health_Activities
        (user_id, activity_date, steps, water_intake, sleep_hours, mood)
        VALUES (?, ?, 0, 0.0, 0.0, '')
    ";

    $insertStmt = $conn->prepare($insertQuery); // Prepare the insert query
    $insertStmt->bind_param("is", $user_id, $today); // Bind the user_id and today's date to the query
    $insertStmt->execute(); // Execute the query to insert data
    $insertStmt->close(); // Close the statement
}

$checkStmt->close(); // Close the check statement

// Query to get aggregated data (total steps, total water intake, total sleep hours) for the user today
$mainQuery = "
    SELECT
        COALESCE(SUM(steps), 0) AS total_steps,
        COALESCE(SUM(water_intake), 0.0) AS total_water_intake,
        COALESCE(SUM(sleep_hours), 0.0) AS total_sleep_hours
    FROM
        Health_Activities
    WHERE
        user_id = ? AND activity_date = ?
";

// Prepare the statement for the aggregated query
$mainStmt = $conn->prepare($mainQuery);
if (!$mainStmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query preparation failed: ' . $conn->error]);
    exit();
}

// Bind parameters and execute the main query
$mainStmt->bind_param("is", $user_id, $today);
$mainStmt->execute();
$mainResult = $mainStmt->get_result();

// Fetch the aggregated data (total steps, total water intake, total sleep hours)
$aggregatedData = $mainResult->fetch_assoc();
if (!$aggregatedData) {
    // If no data is found, return default values for the aggregated data
    $aggregatedData = [
        'total_steps' => 0,
        'total_water_intake' => 0.0,
        'total_sleep_hours' => 0.0
    ];
}

// Close the main statement
$mainStmt->close();

// Query to get the most recent mood from today's activities
$moodQuery = "
    SELECT mood
    FROM Health_Activities
    WHERE user_id = ? AND activity_date = ?
    ORDER BY activity_id DESC LIMIT 1
";

$moodStmt = $conn->prepare($moodQuery); // Prepare the SQL statement
if (!$moodStmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query preparation failed: ' . $conn->error]);
    exit();
}

// Bind parameters and execute the mood query
$moodStmt->bind_param("is", $user_id, $today);
$moodStmt->execute();
$moodResult = $moodStmt->get_result();

// Fetch the most recent mood or default to an empty string
$moodData = $moodResult->fetch_assoc();
$lastMood = $moodData ? $moodData['mood'] : "";

// Close the mood statement
$moodStmt->close();

// Query to fetch a random health tip from the database
$tipQuery = "SELECT tip_text FROM Health_Tips ORDER BY RAND() LIMIT 1";
$tipResult = $conn->query($tipQuery);

$healthTip = "Stay healthy and active!"; // Default health tip
if ($tipResult && $tipResult->num_rows > 0) {
    // If a health tip is found, retrieve it
    $tipData = $tipResult->fetch_assoc();
    $healthTip = $tipData['tip_text'];
}

// Return the aggregated data, the most recent mood, and the health tip as JSON
echo json_encode([
    'total_steps' => (int)$aggregatedData['total_steps'], // Total steps (converted to integer)
    'total_water_intake' => (float)$aggregatedData['total_water_intake'], // Total water intake (converted to float)
    'total_sleep_hours' => (float)$aggregatedData['total_sleep_hours'], // Total sleep hours (converted to float)
    'last_mood' => $lastMood, // Most recent mood
    'health_tip' => $healthTip // Random health tip
]);

// Close the database connection
$conn->close();
?>
