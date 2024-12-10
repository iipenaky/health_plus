<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not logged in or not an admin, send a redirect response to the login page
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Fetch total active users
$activeUsersQuery = "SELECT COUNT(*) AS active_users FROM HealthUsers";
$activeUsersResult = $conn->query($activeUsersQuery);
$activeUsers = $activeUsersResult->fetch_assoc()['active_users'];

// Fetch the most common activity based on the highest sum of steps or exercise time
$commonGoalQuery = "SELECT 'steps' AS metric, SUM(steps) AS total FROM Health_Activities
                    UNION
                    SELECT 'exercise_time' AS metric, SUM(exercise_time) AS total FROM Health_Activities
                    ORDER BY total DESC LIMIT 1";
$commonGoalResult = $conn->query($commonGoalQuery);

// Check if we have a result for the common goal
$commonGoal = null; // Default to null if no result
if ($commonGoalResult->num_rows > 0) {
    $commonGoalData = $commonGoalResult->fetch_assoc();
    $commonGoal = "Most common goal is tracking: " . $commonGoalData['metric'] . " with total " . $commonGoalData['total'];
}

// Fetch total daily logs (from health activities)
$totalLogsQuery = "SELECT COUNT(*) AS total_logs FROM Health_Activities";
$totalLogsResult = $conn->query($totalLogsQuery);
$totalLogs = $totalLogsResult->fetch_assoc()['total_logs'];

// Fetch the rate of topics published (or quiz accuracy rate, depending on your choice)
$topicsQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS topics_count 
                FROM Health_Topics
                GROUP BY month 
                ORDER BY month DESC";
$topicsResult = $conn->query($topicsQuery);
$topicsData = [];
while ($row = $topicsResult->fetch_assoc()) {
    $topicsData[$row['month']] = $row['topics_count'];
}

// Fetch app statistics (now replaced with topics or quiz accuracy data)
$statistics = [];
foreach ($topicsData as $month => $count) {
    $statistics[$month] = $count;
}

// Return data as JSON
echo json_encode([
    'activeUsers' => $activeUsers,
    'commonGoal' => $commonGoal,
    'totalLogs' => $totalLogs,
    'statistics' => $statistics
]);

// Close the connection
$conn->close();
?>
