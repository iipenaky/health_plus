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

$question_text = $_POST['question_text'];
$options = json_encode([
    'A' => $_POST['option_a'],
    'B' => $_POST['option_b'],
    'C' => $_POST['option_c'],
    'D' => $_POST['option_d']
]);
$correct_option = $_POST['correct_option'];
$category = $_POST['category'];

$stmt = $conn->prepare("INSERT INTO Quiz_Questions (question_text, options, correct_option, category) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $question_text, $options, $correct_option, $category);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to add question']);
}
$stmt->close();
?>
