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
