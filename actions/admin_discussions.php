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
    case 'fetch_all_topics':
        fetchAllTopics($conn);
        break;

    case 'delete_topic':
        deleteTopic($conn);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Fetch all topics
function fetchAllTopics($conn)
{
    $sql = "SELECT t.topic_id, t.title, t.content, t.created_at, u.name 
            FROM health_topics t 
            JOIN Users u ON t.user_id = u.user_id 
            ORDER BY t.created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $topics = [];
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
        echo json_encode(['topics' => $topics]);
    } else {
        echo json_encode(['error' => 'No topics found']);
    }
}

// Delete a topic
function deleteTopic($conn)
{
    if (!isset($_POST['topic_id'])) {
        echo json_encode(['error' => 'Topic ID is required']);
        exit();
    }

    $topic_id = (int) $_POST['topic_id'];

    $sql = "DELETE FROM health_topics WHERE topic_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $topic_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Topic deleted successfully']);
        } else {
            echo json_encode(['error' => 'Error deleting topic: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    }
}

// Close the database connection
$conn->close();
?>
