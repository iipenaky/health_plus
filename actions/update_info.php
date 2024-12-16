<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';

$user_id = $_SESSION['user_id'];

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

$stmt = $conn->prepare("UPDATE HealthUsers SET name = ?, email = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $name, $email,$user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Information updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update information']);
}

$stmt->close();
$conn->close();
?>

