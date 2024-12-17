<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access user session data
session_start();

// Check if the user is logged in and has the role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If the user is not logged in or not authorized, return a JSON response to redirect the user
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection file
include '../db/db.php';

// Check if 'meal_id' is provided in the POST request and is numeric
if (isset($_POST['meal_id']) && is_numeric($_POST['meal_id'])) {
    $meal_id = $_POST['meal_id']; // The ID of the meal to be deleted
    $user_id = $_SESSION['user_id']; // Get the current user's ID

    // Prepare the DELETE query, ensuring that the user can only delete their own meals
    $stmt = $conn->prepare("DELETE FROM Meals WHERE meal_id = ? AND user_id = ?");

    if ($stmt) {
        // Bind parameters: meal_id and user_id
        $stmt->bind_param("ii", $meal_id, $user_id);

        // Execute the query
        if ($stmt->execute()) {
            // Check if any row was affected (meal deleted successfully)
            if ($stmt->affected_rows > 0) {
                echo json_encode(["success" => "Meal deleted successfully!"]);
            } else {
                // No meal was deleted, meaning either it doesn't exist or the user doesn't have permission
                echo json_encode(["error" => "Meal not found or you don't have permission to delete it."]);
            }
        } else {
            // Error during execution of the DELETE query
            echo json_encode(["error" => "Error deleting meal: " . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error preparing the DELETE query
        echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
    }
} else {
    // If the meal_id is not valid
    echo json_encode(["error" => "Invalid meal ID."]);
}

// Close the database connection
$conn->close();
?>
