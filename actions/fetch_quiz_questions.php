<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';

$result = $conn->query("SELECT * FROM Quiz_Questions");
$questions = [];
while ($row = $result->fetch_assoc()) {
    $row['options'] = json_decode($row['options'], true); 
    $questions[] = $row;
}
echo json_encode(['questions' => $questions]);
?>
