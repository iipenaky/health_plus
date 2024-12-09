<?php
header('Content-Type: application/json');
// Start the session
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../frontend/index.html'); // Redirect to index page
    exit();
}

// Assuming user ID is 1 for this example
$user_id = $_SESSION['user_id'];


// Get the entry ID from the GET request
$entry_id = isset($_GET['id']) ? $_GET['id'] : null;

// Validate the entry ID
if (!$entry_id) {
    echo json_encode(['success' => false, 'message' => 'No entry ID provided']);
    exit;
}

if (!filter_var($entry_id, FILTER_VALIDATE_INT)) {
    echo json_encode(['success' => false, 'message' => 'Invalid entry ID']);
    exit;
}

// Prepare the delete query
$query = "DELETE FROM journal_entries WHERE user_id = ? AND journal_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $entry_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Entry deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete entry']);
}
?>
