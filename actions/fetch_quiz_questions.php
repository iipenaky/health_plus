<?php
// Start the session to access user session data
session_start();

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If the user is not logged in or not an admin, return a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Fetch all quiz questions from the Quiz_Questions table
$result = $conn->query("SELECT * FROM Quiz_Questions");

// Initialize an empty array to store the questions
$questions = [];

// Loop through the result set and decode the options from JSON to an array
while ($row = $result->fetch_assoc()) {
    $row['options'] = json_decode($row['options'], true); // Decode JSON options into an array
    $questions[] = $row; // Add each question to the questions array
}

// Return the questions as a JSON response
echo json_encode(['questions' => $questions]);
?>
