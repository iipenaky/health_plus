<?php
// Start the session
session_start();

// Check if the user is logged in and has the role of 'admin'
// If not, redirect them to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit(); // Stop the script
}

// Include the database connection file
include '../db/db.php';

// Get the question text from the POST request
$question_text = $_POST['question_text'];

// Combine the options (A, B, C, D) into a JSON object for storage
$options = json_encode([
    'A' => $_POST['option_a'], // Option A
    'B' => $_POST['option_b'], // Option B
    'C' => $_POST['option_c'], // Option C
    'D' => $_POST['option_d']  // Option D
]);

// Get the correct option (e.g., A, B, C, D) from the POST request
$correct_option = $_POST['correct_option'];

// Get the category of the question from the POST request
$category = $_POST['category'];

// Prepare an SQL statement to insert the new question into the database
$stmt = $conn->prepare("
    INSERT INTO Quiz_Questions (question_text, options, correct_option, category) 
    VALUES (?, ?, ?, ?)
");

// Bind the variables to the prepared SQL statement
$stmt->bind_param("ssss", $question_text, $options, $correct_option, $category);

// Execute the SQL statement and check if it was successful
if ($stmt->execute()) {
    // Respond with a success message if the question was added successfully
    echo json_encode(['success' => true]);
} else {
    // Respond with an error message if the insertion failed
    echo json_encode(['error' => 'Failed to add question']);
}

// Close the prepared statement
$stmt->close();
?>
