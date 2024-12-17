<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If not logged in or not a 'user', send a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection
include '../db/db.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Query to fetch all journal entries for the current user, formatting the date
$query = "SELECT user_id, journal_id, DATE_FORMAT(entry_date, '%Y-%m-%d') AS formatted_date, entry_text 
          FROM Journal_Entries 
          WHERE user_id = ?";

// Prepare the query to avoid SQL injection
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);  // Bind the user ID to the query
$stmt->execute();  // Execute the query
$result = $stmt->get_result();  // Get the result set

// Initialize an empty array to hold the journal entries
$entries = [];

// Loop through each row in the result set and build the entries array
while ($row = $result->fetch_assoc()) {
    $entries[] = [
        'id' => $row['user_id'],
        'journ_id' => $row['journal_id'],
        'date' => $row['formatted_date'],
        'message' => $row['entry_text']
    ];
}

// Check if there are entries and return them as JSON, or indicate no entries were found
if (!empty($entries)) {
    echo json_encode(['success' => true, 'entries' => $entries]);
} else {
    echo json_encode(['success' => false, 'message' => 'No entries found.']);
}
?>
