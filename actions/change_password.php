<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';


$user_id = $_SESSION['user_id'];

$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

$stmt = $conn->prepare("SELECT password_hash FROM  HealthUsers   WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($password_hash);
$stmt->fetch();

if (!$password_hash) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

if (!password_verify($currentPassword, $password_hash)) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect current password']);
    exit;
}

$newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE HealthUsers SET password_hash = ? WHERE user_id = ?");
$stmt->bind_param("si", $newPasswordHash, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to change password']);
}

$stmt->close();
$conn->close();
?>
