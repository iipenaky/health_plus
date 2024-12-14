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

$question_id = $_POST['question_id'];
$conn->query("DELETE FROM Quiz_Questions WHERE question_id = $question_id");
echo json_encode(['success' => true]);
?>
