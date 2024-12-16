<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1); 

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';
if ($conn->connect_error) { 
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]); 
    exit(); 
} 

$user_id = $_SESSION['user_id']; 
$today = date("Y-m-d"); 

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

$mainStmt = $conn->prepare($mainQuery); 
if (!$mainStmt) { 
    http_response_code(500); 
    echo json_encode(['error' => 'Database query preparation failed: ' . $conn->error]); 
    exit(); 
} 
 
$mainStmt->bind_param("is", $user_id, $today); 
$mainStmt->execute(); 
$mainResult = $mainStmt->get_result(); 

$aggregatedData = $mainResult->fetch_assoc(); 
if (!$aggregatedData) { 
    $aggregatedData = [ 
        'total_steps' => 0, 
        'total_water_intake' => 0.0, 
        'total_sleep_hours' => 0.0 
    ]; 
} 
 
$mainStmt->close(); 

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

$moodStmt->bind_param("is", $user_id, $today); 
$moodStmt->execute(); 
$moodResult = $moodStmt->get_result();  

$moodData = $moodResult->fetch_assoc(); 
$lastMood = $moodData ? $moodData['mood'] : ""; 
 
$moodStmt->close(); 

$tipQuery = "SELECT tip_text FROM Health_Tips ORDER BY RAND() LIMIT 1";
$tipResult = $conn->query($tipQuery);

$healthTip = "Stay healthy and active!"; 
if ($tipResult && $tipResult->num_rows > 0) {
    $tipData = $tipResult->fetch_assoc();
    $healthTip = $tipData['tip_text'];
}

echo json_encode([ 
    'total_steps' => (int)$aggregatedData['total_steps'], 
    'total_water_intake' => (float)$aggregatedData['total_water_intake'], 
    'total_sleep_hours' => (float)$aggregatedData['total_sleep_hours'], 
    'last_mood' => $lastMood, 
    'health_tip' => $healthTip
]); 

$conn->close(); 
?>
