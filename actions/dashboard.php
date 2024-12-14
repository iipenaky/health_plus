<?php 
// Enable error reporting for debugging 
error_reporting(E_ALL); 
ini_set('display_errors', 1); 

// Start the session
session_start();



// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Return a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
// Include the database connection file
include '../db/db.php';
// Check if the connection was successful 
if ($conn->connect_error) { 
    http_response_code(500); // Internal Server Error 
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]); 
    exit(); 
} 

// Get the logged-in user's ID and today's date 
$user_id = $_SESSION['user_id']; 
$today = date("Y-m-d"); // Get today's date using PHP's date function

// First, check if an entry for today exists 
$checkExistsQuery = " 
    SELECT activity_id 
    FROM Health_Activities 
    WHERE user_id = ? AND activity_date = ? 
    LIMIT 1 
"; 

$checkStmt = $conn->prepare($checkExistsQuery); 
$checkStmt->bind_param("is", $user_id, $today); 
$checkStmt->execute(); 
$checkResult = $checkStmt->get_result(); 

// If no entry exists for today, create a new one with default values 
if ($checkResult->num_rows == 0) { 
    $insertQuery = " 
        INSERT INTO Health_Activities 
        (user_id, activity_date, steps, water_intake, sleep_hours, mood) 
        VALUES (?, ?, 0, 0.0, 0.0, '') 
    "; 
    
    $insertStmt = $conn->prepare($insertQuery); 
    $insertStmt->bind_param("is", $user_id, $today); 
    $insertStmt->execute(); 
    $insertStmt->close(); 
} 
$checkStmt->close(); 

// Prepare the query to fetch aggregated data 
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

// Fetch the aggregated data 
$aggregatedData = $mainResult->fetch_assoc(); 
if (!$aggregatedData) { 
    $aggregatedData = [ 
        'total_steps' => 0, 
        'total_water_intake' => 0.0, 
        'total_sleep_hours' => 0.0 
    ]; 
} 

// Close the main statement 
$mainStmt->close(); 

// Prepare the query to fetch the most recent mood 
$moodQuery = " 
    SELECT mood 
    FROM Health_Activities 
    WHERE user_id = ? AND activity_date = ? 
    ORDER BY activity_id DESC LIMIT 1 
"; 

$moodStmt = $conn->prepare($moodQuery); 
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

// Fetch a random health tip
$tipQuery = "SELECT tip_text FROM Health_Tips ORDER BY RAND() LIMIT 1";
$tipResult = $conn->query($tipQuery);

$healthTip = "Stay healthy and active!"; // Default tip
if ($tipResult && $tipResult->num_rows > 0) {
    $tipData = $tipResult->fetch_assoc();
    $healthTip = $tipData['tip_text'];
}

// Return the aggregated data, the most recent mood, and the health tip as JSON 
echo json_encode([ 
    'total_steps' => (int)$aggregatedData['total_steps'], 
    'total_water_intake' => (float)$aggregatedData['total_water_intake'], 
    'total_sleep_hours' => (float)$aggregatedData['total_sleep_hours'], 
    'last_mood' => $lastMood, 
    'health_tip' => $healthTip
]); 

// Close the database connection 
$conn->close(); 
?>
