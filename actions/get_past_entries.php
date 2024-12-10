<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, send a redirect response to the login page
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}
include '../db/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

// Fetch all journal entries for the current user
$query = "SELECT user_id,journal_id, DATE_FORMAT(entry_date, '%Y-%m-%d') as formatted_date, entry_text 
          FROM Journal_Entries 
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
