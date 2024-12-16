<?php
header('Content-Type: application/json');
session_start();
include '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'GET') {
    if (isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        $query = "SELECT user_id, name, calorie_goal, created_at, role FROM HealthUsers WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user['created_at'] = date('Y-m-d H:i:s', strtotime($user['created_at']));
            echo json_encode(['user' => $user]);
        } else {
            echo json_encode(['error' => 'User not found']);
        }
        $stmt->close();
    } else {
        $query = "SELECT user_id, name, calorie_goal, created_at, role FROM HealthUsers where role = 'user'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
            $users[] = $row;
        }
        echo json_encode(['users' => $users]);
        $stmt->close();
    }
} elseif ($request_method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['user_id'], $data['name'], $data['calorie_goal'])) {
        $user_id = $data['user_id'];
        $name = $data['name'];
        $calorie_goal = $data['calorie_goal'];

        if ($user_id === $_SESSION['user_id']) {
            echo json_encode(['error' => 'Cannot update your own account']);
            exit;
        }

        $query = "UPDATE HealthUsers SET name = ?, calorie_goal = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $name, $calorie_goal, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to update user.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Invalid data']);
    }
} elseif ($request_method === 'DELETE') {
    $data = $_POST;
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;

    if (!$user_id) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }
    if ($user_id === $_SESSION['user_id']) {
        echo json_encode(['error' => 'Cannot delete your own account']);
        exit;
    }

    $query = "DELETE FROM HealthUsers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete user.']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
