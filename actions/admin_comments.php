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

// Get the action from the query string (e.g., 'fetch_all_comments' or 'delete_comment')
// If no action is specified, set it to an empty string
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Use a switch statement to handle different actions based on the value of $action
switch ($action) {
    case 'fetch_all_comments':
        // Call the function to fetch all comments
        fetchAllComments($conn);
        break;

    case 'delete_comment':
        // Call the function to delete a specific comment
        deleteComment($conn);
        break;

    default:
        // Respond with an error if the action is invalid
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Function to fetch all comments from the database
function fetchAllComments($conn)
{
    // SQL query to retrieve comments, their authors, and the topics they belong to
    $sql = "SELECT c.comment_id, c.content, c.created_at, u.name, t.title AS topic_title 
            FROM Comments c
            JOIN HealthUsers u ON c.user_id = u.user_id
            JOIN Health_Topics t ON c.topic_id = t.topic_id
            ORDER BY c.created_at DESC"; // Sort comments by the newest first
    $result = $conn->query($sql);

    // Check if any comments are found
    if ($result->num_rows > 0) {
        $comments = [];
        // Loop through each row and add it to the comments array
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        // Respond with the comments data in JSON format
        echo json_encode(['comments' => $comments]);
    } else {
        // Respond with an error if no comments are found
        echo json_encode(['error' => 'No comments found']);
    }
}

// Function to delete a specific comment
function deleteComment($conn)
{
    // Check if the comment ID is provided in the POST request
    if (!isset($_POST['comment_id'])) {
        echo json_encode(['error' => 'Comment ID is required']);
        exit(); // Stop the script
    }

    // Get the comment ID from the POST request and cast it to an integer
    $comment_id = (int) $_POST['comment_id'];

    // Prepare an SQL statement to delete the comment with the given ID
    $sql = "DELETE FROM Comments WHERE comment_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind the comment ID to the prepared SQL statement
        $stmt->bind_param('i', $comment_id);

        // Execute the SQL statement and check if it was successful
        if ($stmt->execute()) {
            // Respond with a success message if the comment was deleted
            echo json_encode(['success' => 'Comment deleted successfully']);
        } else {
            // Respond with an error message if the deletion failed
            echo json_encode(['error' => 'Error deleting comment: ' . $stmt->error]);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Respond with an error message if statement preparation failed
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    }
}

// Close the database connection
$conn->close();
?>
