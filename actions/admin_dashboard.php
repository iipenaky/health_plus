<?php
// Start the session
session_start();

// Check if the user is logged in and has the role of 'admin'
// If not, redirect them to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit(); // Stop the script
}

// Include the database connection file
include '../db/db.php';

// Query to get the total number of active users in the HealthUsers table
$activeUsersQuery = "SELECT COUNT(*) AS active_users FROM HealthUsers";
$activeUsersResult = $conn->query($activeUsersQuery);
// Fetch the result and store the total number of active users
$activeUsers = $activeUsersResult->fetch_assoc()['active_users'];

// Query to determine the most common activity metric (steps or exercise_time) based on total values
$commonGoalQuery = "SELECT 'steps' AS metric, SUM(steps) AS total FROM Health_Activities
                    UNION
                    SELECT 'exercise_time' AS metric, SUM(exercise_time) AS total FROM Health_Activities
                    ORDER BY total DESC LIMIT 1"; // Sort by total and get the highest
$commonGoalResult = $conn->query($commonGoalQuery);

$commonGoal = null; // Initialize commonGoal as null
if ($commonGoalResult->num_rows > 0) {
    // Fetch the result and format it as a readable string
    $commonGoalData = $commonGoalResult->fetch_assoc();
    $commonGoal = $commonGoalData['metric'] . " with total " . $commonGoalData['total'];
}

// Query to count the total number of logs in the Health_Activities table
$totalLogsQuery = "SELECT COUNT(*) AS total_logs FROM Health_Activities";
$totalLogsResult = $conn->query($totalLogsQuery);
// Fetch the result and store the total number of logs
$totalLogs = $totalLogsResult->fetch_assoc()['total_logs'];

// Query to count the number of topics created each month in the Health_Topics table
$topicsQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS topics_count 
                FROM Health_Topics
                GROUP BY month 
                ORDER BY month DESC"; // Group by month and sort by newest first
$topicsResult = $conn->query($topicsQuery);

// Initialize an empty array to store monthly topic counts
$topicsData = [];
while ($row = $topicsResult->fetch_assoc()) {
    // Store the month and the count of topics in the topicsData array
    $topicsData[$row['month']] = $row['topics_count'];
}

// Prepare statistics array to format the topics data for output
$statistics = [];
foreach ($topicsData as $month => $count) {
    $statistics[$month] = $count; // Add month and count to statistics
}

// Send the collected data as a JSON response
echo json_encode([
    'activeUsers' => $activeUsers,     // Total active users
    'commonGoal' => $commonGoal,       // Most common activity goal
    'totalLogs' => $totalLogs,         // Total activity logs
    'statistics' => $statistics        // Monthly topic statistics
]);

// Close the database connection
$conn->close();
?>
