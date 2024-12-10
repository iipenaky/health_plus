<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, send a redirect response to the login page
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}
include '../db/db.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");  // Allow DELETE method
header("Access-Control-Allow-Headers: Content-Type");
error_reporting(E_ALL);
ini_set('display_errors', 1);


$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle saving a new journal entry
    $entry_date = date('Y-m-d'); // Today's date
    $entry_text = isset($_POST['journalEntry']) ? $_POST['journalEntry'] : '';

    if (empty($entry_text)) {
        echo json_encode(['success' => false, 'message' => 'Journal entry cannot be empty.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Journal_Entries (user_id, entry_date, entry_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $entry_date, $entry_text);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Journal entry saved successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save journal entry.']);
    }

    $stmt->close();
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle fetching journal entries
    $stmt = $conn->prepare("SELECT * FROM Journal_Entries WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $entries = [];
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
        echo json_encode(['success' => true, 'entries' => $entries]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No history found']);
    }

    $stmt->close();
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Handle deleting a journal entry
    parse_str(file_get_contents("php://input"), $data); // Get data from DELETE request
    $entry_id = isset($data['id']) ? $data['id'] : '';

    if (empty($entry_id)) {
        echo json_encode(['success' => false, 'message' => 'No entry ID provided']);
        exit;
    }

    // Prepare and execute the DELETE query
    $stmt = $conn->prepare("DELETE FROM Journal_Entries WHERE user_id = ? AND journal_id = ?");
    $stmt->bind_param("ii", $user_id, $entry_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Journal entry deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete journal entry.']);
    }

    $stmt->close();
    exit;

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>
