<?php
// Start the session
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../frontend/index.html'); // Redirect to index page
    exit();
}

$result = $conn->query("SELECT * FROM Quiz_Questions");
$questions = [];
while ($row = $result->fetch_assoc()) {
    $row['options'] = json_decode($row['options'], true); // Decode JSON options
    $questions[] = $row;
}
echo json_encode(['questions' => $questions]);
?>
