<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT calorie_goal FROM HealthUsers WHERE user_id = $user_id");
echo json_encode(['calorie_goal' => $result->fetch_assoc()['calorie_goal']]);
?>
