<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

// Include database connection
include '../db/db.php';

$result = $conn->query("SELECT * FROM Quiz_Questions");
$questions = [];
while ($row = $result->fetch_assoc()) {
    $row['options'] = json_decode($row['options'], true); // Decode JSON options
    $questions[] = $row;
}
echo json_encode(['questions' => $questions]);
?>
