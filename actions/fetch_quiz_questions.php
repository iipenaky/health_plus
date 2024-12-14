<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not logged in or not an admin, send a rerect response to the login page
    echo json_encode(['redirect' => '../frontend/index.html']);
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
