<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';

$activeUsersQuery = "SELECT COUNT(*) AS active_users FROM HealthUsers";
$activeUsersResult = $conn->query($activeUsersQuery);
$activeUsers = $activeUsersResult->fetch_assoc()['active_users'];

$commonGoalQuery = "SELECT 'steps' AS metric, SUM(steps) AS total FROM Health_Activities
                    UNION
                    SELECT 'exercise_time' AS metric, SUM(exercise_time) AS total FROM Health_Activities
                    ORDER BY total DESC LIMIT 1";
$commonGoalResult = $conn->query($commonGoalQuery);

$commonGoal = null; 
if ($commonGoalResult->num_rows > 0) {
    $commonGoalData = $commonGoalResult->fetch_assoc();
    $commonGoal = "Most common goal is tracking: " . $commonGoalData['metric'] . " with total " . $commonGoalData['total'];
}

$totalLogsQuery = "SELECT COUNT(*) AS total_logs FROM Health_Activities";
$totalLogsResult = $conn->query($totalLogsQuery);
$totalLogs = $totalLogsResult->fetch_assoc()['total_logs'];

$topicsQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS topics_count 
                FROM Health_Topics
                GROUP BY month 
                ORDER BY month DESC";
$topicsResult = $conn->query($topicsQuery);
$topicsData = [];
while ($row = $topicsResult->fetch_assoc()) {
    $topicsData[$row['month']] = $row['topics_count'];
}

$statistics = [];
foreach ($topicsData as $month => $count) {
    $statistics[$month] = $count;
}

echo json_encode([
    'activeUsers' => $activeUsers,
    'commonGoal' => $commonGoal,
    'totalLogs' => $totalLogs,
    'statistics' => $statistics
]);

$conn->close();
?>
