<?php
header('Content-Type: application/json');
session_start();

// Include the database connection
include '../db/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Return a JSON response indicating the user is not authorized
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Check if the logged-in user is an admin
$admin_check_query = "SELECT role FROM  HealthUsers   WHERE user_id = ?";
$stmt = $conn->prepare($admin_check_query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0 || ($row = $result->fetch_assoc()) && $row['role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Access denied. Admin privileges required']);
    exit;
}

// Check if the request method is GET or DELETE
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'GET') {
    // Fetch all  HealthUsers   for the admin dashboard
    $query = "SELECT user_id, name, calorie_goal, created_at, role FROM  HealthUsers  ";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(['users' => $users]);
    } else {
        echo json_encode(['error' => 'No  HealthUsers   found']);
    }
} elseif ($request_method === 'DELETE') {
    // Delete a user by ID
    parse_str(file_get_contents("php://input"), $_DELETE);
    $user_id = isset($_DELETE['user_id']) ? intval($_DELETE['user_id']) : null;

    if (!$user_id) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }

    // Prevent deleting your own account
    if ($user_id === $_SESSION['user_id']) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Cannot delete your own account']);
        exit;
    }

    // Perform the deletion
    $query = "DELETE FROM HealthUsers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete user. Please try again.']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>