<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../frontend/index.html'); // Redirect to index page
    exit();
}

// Check if the connection was successful
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'fetch_all_comments':
        fetchAllComments($conn);
        break;

    case 'delete_comment':
        deleteComment($conn);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Fetch all comments
function fetchAllComments($conn)
{
    $sql = "SELECT c.comment_id, c.content, c.created_at, u.name, t.title AS topic_title 
            FROM Comments c
            JOIN HealthUsers u ON c.user_id = u.user_id
            JOIN health_topics t ON c.topic_id = t.topic_id
            ORDER BY c.created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        echo json_encode(['comments' => $comments]);
    } else {
        echo json_encode(['error' => 'No comments found']);
    }
}

// Delete a comment
function deleteComment($conn)
{
    if (!isset($_POST['comment_id'])) {
        echo json_encode(['error' => 'Comment ID is required']);
        exit();
    }

    $comment_id = (int) $_POST['comment_id'];

    $sql = "DELETE FROM Comments WHERE comment_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $comment_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Comment deleted successfully']);
        } else {
            echo json_encode(['error' => 'Error deleting comment: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    }
}

// Close the database connection
$conn->close();
?>
