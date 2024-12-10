<?php
header('Content-Type: application/json');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Return a JSON response indicating the user is not authorized
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}
include '../db/db.php';
// Assuming user ID is 1 for this example
$user_id = $_SESSION['user_id'];

// Check database connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Determine action based on request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET') {
    // Fetch 6 random quiz questions
    $query = "SELECT question_id, question_text, options FROM Quiz_Questions ORDER BY RAND() LIMIT 6";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = [
                'id' => $row['question_id'],
                'text' => $row['question_text'],
                'options' => json_decode($row['options'], true),
            ];
        }
        echo json_encode(['questions' => $questions]);
    } else {
        echo json_encode(['error' => 'No quiz questions found']);
    }
} elseif ($requestMethod === 'POST') {
    // Handle quiz submission
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['error' => 'No answers submitted']);
        exit();
    }

    // Fetch correct answers
    $query = "SELECT question_id, correct_option FROM Quiz_Questions";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $total = $result->num_rows;
        $correct = 0;

        while ($row = $result->fetch_assoc()) {
            if (isset($data["q" . $row['question_id']]) && $data["q" . $row['question_id']] === $row['correct_option']) {
                $correct++;
            }
        }

        echo json_encode(['correct' => $correct, 'total' => $total]);
    } else {
        echo json_encode(['error' => 'Error fetching quiz answers']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
