<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'fetch_topics':
        fetchTopics($conn);
        break;
    case 'add_topic':
        addTopic($conn);
        break;
    case 'fetch_comments':
        fetchComments($conn);
        break;
    case 'add_comment':
        addComment($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function fetchTopics($conn)
{
    $result = $conn->query("SELECT * FROM Health_Topics ORDER BY created_at DESC");
    $topics = [];
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
    echo json_encode(['topics' => $topics]);
}

function addTopic($conn)
{
    $user_id = $_SESSION['user_id'];
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if (empty($title) || empty($content)) {
        echo json_encode(['error' => 'Title and content are required']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO Health_Topics (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Topic added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add topic']);
    }
    $stmt->close();
}

function fetchComments($conn)
{
    $topic_id = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : 0;

    if ($topic_id === 0) {
        echo json_encode(['error' => 'Invalid topic ID']);
        return;
    }

    $result = $conn->query("SELECT * FROM Comments WHERE topic_id = $topic_id ORDER BY created_at ASC");
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    echo json_encode(['comments' => $comments]);
}

function addComment($conn)
{
    $user_id = $_SESSION['user_id'];
    $topic_id = isset($_POST['topic_id']) ? (int)$_POST['topic_id'] : 0;
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if ($topic_id === 0 || empty($content)) {
        echo json_encode(['error' => 'Topic ID and content are required']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO Comments (topic_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $topic_id, $user_id, $content);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Comment added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add comment']);
    }
    $stmt->close();
}
?>
