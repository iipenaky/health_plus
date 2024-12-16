<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';
$meal_id = $_POST['meal_id'];
if (isset($_POST['meal_id']) && is_numeric($_POST['meal_id'])) {
    $meal_id = $_POST['meal_id']; 
    $user_id = $_SESSION['user_id']; 

    $stmt = $conn->prepare("DELETE FROM Meals WHERE meal_id = ? AND user_id = ?");

    if ($stmt) {
        $stmt->bind_param("ii", $meal_id, $user_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["success" => "Meal deleted successfully!"]);
            } else {
                echo json_encode(["error" => "Meal not found or you don't have permission to delete it."]);
            }
        } else {
            echo json_encode(["error" => "Error deleting meal: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Invalid meal ID."]);
}

$conn->close();
?>

