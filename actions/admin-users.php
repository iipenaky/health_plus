<?php
// Set the content type to JSON for API responses
header('Content-Type: application/json');

// Start the session to access user session data
session_start();

// Include the database connection file
include '../db/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users or unauthenticated requests
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Determine the request method (GET, PUT, DELETE)
$request_method = $_SERVER['REQUEST_METHOD'];

// Handle GET requests
if ($request_method === 'GET') {
    if (isset($_GET['user_id'])) {
        // Fetch details for a specific user by user_id
        $user_id = intval($_GET['user_id']); // Convert user_id to an integer for security
        $query = "SELECT user_id, name, calorie_goal, created_at, role FROM HealthUsers WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id); // Bind the user_id parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists and return the data
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Format the created_at field for readability
            $user['created_at'] = date('Y-m-d H:i:s', strtotime($user['created_at']));
            echo json_encode(['user' => $user]);
        } else {
            // Return an error if the user is not found
            echo json_encode(['error' => 'User not found']);
        }
        $stmt->close();
    } else {
        // Fetch all users with the role 'user' for the admin dashboard
        $query = "SELECT user_id, name, calorie_goal, created_at, role FROM HealthUsers WHERE role = 'user'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        // Collect all user data into an array
        $users = [];
        while ($row = $result->fetch_assoc()) {
            // Format the created_at field for each user
            $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
            $users[] = $row;
        }
        echo json_encode(['users' => $users]); // Return the list of users
        $stmt->close();
    }
} elseif ($request_method === 'PUT') {
    // Handle PUT requests to update a user's information
    $data = json_decode(file_get_contents("php://input"), true); // Parse the JSON request body

    // Check if the required fields are present
    if (isset($data['user_id'], $data['name'], $data['calorie_goal'])) {
        $user_id = intval($data['user_id']);
        $name = $data['name'];
        $calorie_goal = $data['calorie_goal'];

        // Prevent admins from updating their own account
        if ($user_id === $_SESSION['user_id']) {
            echo json_encode(['error' => 'Cannot update your own account']);
            exit;
        }

        // Prepare the SQL query to update the user's information
        $query = "UPDATE HealthUsers SET name = ?, calorie_goal = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $name, $calorie_goal, $user_id);

        // Execute the update query and check for success
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to update user.']);
        }
        $stmt->close();
    } else {
        // Return an error if the required fields are missing
        echo json_encode(['error' => 'Invalid data']);
    }
} elseif ($request_method === 'DELETE') {
    // Handle DELETE requests to remove a user
    $data = json_decode(file_get_contents("php://input"), true); // Parse the JSON request body

    if (isset($data['user_id'])) {
        $user_id = intval($data['user_id']); // Convert user_id to an integer for security

        // Prepare the SQL query to delete the user
        $query = "DELETE FROM HealthUsers WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);

        // Execute the delete query and check for success
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to delete user.']);
        }
        $stmt->close();
    } else {
        // Return an error if the user_id is not provided
        echo json_encode(['error' => 'Invalid user ID']);
    }
} else {
    // Handle invalid request methods
    echo json_encode(['error' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>
