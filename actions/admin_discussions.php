<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Get the action from the query string (e.g., 'fetch_all_topics' or 'delete_topic')
// If no action is provided, set it to an empty string
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Use a switch statement to handle different actions based on the value of $action
switch ($action) {
    case 'fetch_all_topics':
        // Call the function to fetch all topics
        fetchAllTopics($conn);
        break;

    case 'delete_topic':
        // Call the function to delete a specific topic
        deleteTopic($conn);
        break;

    default:
        // Respond with an error if the action is invalid
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Function to fetch all topics from the database
function fetchAllTopics($conn)
{
    // SQL query to retrieve all topics along with their author names
    $sql = "SELECT t.topic_id, t.title, t.content, t.created_at, u.name 
            FROM Health_Topics t 
            JOIN HealthUsers u ON t.user_id = u.user_id 
            ORDER BY t.created_at DESC"; // Sort topics by newest first
    $result = $conn->query($sql);

    // Check if any topics are found
    if ($result->num_rows > 0) {
        $topics = [];
        // Loop through each row and add it to the topics array
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
        // Respond with the topics data in JSON format
        echo json_encode(['topics' => $topics]);
    } else {
        // Respond with an error if no topics are found
        echo json_encode(['error' => 'No topics found']);
    }
}

// Function to delete a specific topic
function deleteTopic($conn)
{
    // Check if the topic ID is provided in the POST request
    if (!isset($_POST['topic_id'])) {
        echo json_encode(['error' => 'Topic ID is required']);
        exit(); // Stop the script
    }

    // Get the topic ID from the POST request and cast it to an integer
    $topic_id = (int) $_POST['topic_id'];

    // Prepare an SQL statement to delete the topic with the given ID
    $sql = "DELETE FROM Health_Topics WHERE topic_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind the topic ID to the prepared SQL statement
        $stmt->bind_param('i', $topic_id);

        // Execute the SQL statement and check if it was successful
        if ($stmt->execute()) {
            // Respond with a success message if the topic was deleted
            echo json_encode(['success' => 'Topic deleted successfully']);
        } else {
            // Respond with an error message if the deletion failed
            echo json_encode(['error' => 'Error deleting topic: ' . $stmt->error]);
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
