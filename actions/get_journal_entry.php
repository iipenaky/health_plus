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

$date = isset($_GET['date']) ? $_GET['date'] : null;

// Check if the date is provided
if ($date) {
    // Prepare SQL query to fetch the journal entry for the provided date
    $sql = "SELECT entry_text FROM Journal_Entries WHERE entry_date = ?";
    $stmt = $conn->prepare($sql);
    
    // Bind the date parameter to the prepared statement
    $stmt->bind_param("s", $date);

    // Execute the statement
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($entry_text);

    // Fetch the result
    if ($stmt->fetch()) {
        // Return the journal entry if found
        echo json_encode([
            'success' => true,
            'entry_text' => $entry_text
        ]);
    } else {
        // Return an error message if no entry found
        echo json_encode([
            'success' => false,
            'message' => 'No entry found for this date.'
        ]);
    }

    // Close the statement
    $stmt->close();
} else {
    // Return an error if no date is provided
    echo json_encode([
        'success' => false,
        'message' => 'Date is required.'
    ]);
}

// Close the database connection
$conn->close();
?>
