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

$question_id = $_POST['question_id'];
$conn->query("DELETE FROM Quiz_Questions WHERE question_id = $question_id");
echo json_encode(['success' => true]);
?>
