<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';

$user_id = $_SESSION['user_id'];

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET') {
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
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['error' => 'No answers submitted']);
        exit();
    }

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
