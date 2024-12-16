<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$calorie_goal = $_POST['calorie_goal'];
$conn->query("UPDATE HealthUsers SET calorie_goal = $calorie_goal WHERE user_id = $user_id");
echo json_encode(['success' => true]);
?>
