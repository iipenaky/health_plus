<?php
session_start();
global $conn;

ob_start();

include '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    ob_end_flush();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['steps'], $_POST['water'], $_POST['sleep'], $_POST['mood'], $_POST['exercise'])) {
        echo json_encode(['error' => 'All fields are required.']);
        ob_end_flush();
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $activity_date = date("Y-m-d");

    $steps = intval($_POST['steps']);
    $water = floatval($_POST['water']);
    $sleep = floatval($_POST['sleep']);
    $mood = htmlspecialchars(trim($_POST['mood'])); 
    $exercise = floatval($_POST['exercise']);

    if ($steps <= 0 || $water <= 0 || $sleep <= 0 || empty($mood) || $exercise <= 0) {
        echo json_encode(['error' => 'Please provide valid inputs for all fields.']);
        ob_end_flush();
        exit();
    }

    $stmt = $conn->prepare(
        "INSERT INTO Health_Activities (user_id, activity_date, steps, water_intake, sleep_hours, exercise_time, mood)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare the SQL query.']);
        ob_end_flush();
        exit();
    }
    if (!$stmt->bind_param("isidids", $user_id, $activity_date, $steps, $water, $sleep, $exercise, $mood)) {
        echo json_encode(['error' => 'Failed to bind parameters.']);
        ob_end_flush();
        exit();
    }
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'SQL error: ' . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

ob_end_flush();
?>
