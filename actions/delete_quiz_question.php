<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';

$question_id = $_POST['question_id'];
$conn->query("DELETE FROM Quiz_Questions WHERE question_id = $question_id");
echo json_encode(['success' => true]);
?>
