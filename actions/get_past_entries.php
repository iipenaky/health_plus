<?php
// Start the session
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../frontend/index.html'); // Redirect to index page
    exit();
}


header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

// Fetch all journal entries for the current user
$query = "SELECT user_id,journal_id, DATE_FORMAT(entry_date, '%Y-%m-%d') as formatted_date, entry_text 
          FROM journal_entries 
          WHERE user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$entries = [];
while ($row = $result->fetch_assoc()) {
    $entries[] = [
        'id' => $row['user_id'],
        'journ_id' => $row['journal_id'],
        'date' => $row['formatted_date'],
        'message' => $row['entry_text']
    ];
}

if (!empty($entries)) {
    echo json_encode(['success' => true, 'entries' => $entries]);
} else {
    echo json_encode(['success' => false, 'message' => 'No entries found.']);
}
?>
