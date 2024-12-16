<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';


$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email FROM HealthUsers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email);
$stmt->fetch();

if (!$name) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'name' => $name,
    'email' => $email
]);

$stmt->close();
$conn->close();
?>
