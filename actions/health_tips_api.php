<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

include '../db/db.php';
// Check the request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all health tips
    $query = "SELECT * FROM Health_Tips ORDER BY created_at DESC";
    $result = $conn->query($query);

    $tips = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tips[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($tips);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new health tip
    $data = json_decode(file_get_contents('php://input'), "Tip added");
    if (isset($data['action']) && $data['action'] === 'add' && !empty($data['tip_text'])) {
        $tip_text = $conn->real_escape_string($data['tip_text']);
        $query = "INSERT INTO Health_Tips (tip_text) VALUES ('$tip_text')";
        $result = $conn->query($query);

        echo json_encode(['success' => $result]);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete a health tip
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['tip_id'])) {
        $tip_id = (int)$data['tip_id'];
        $query = "DELETE FROM Health_Tips WHERE tip_id = $tip_id";
        $result = $conn->query($query);

        echo json_encode(['success' => $result]);
        exit();
    }
}

header('HTTP/1.0 400 Bad Request');
echo json_encode(['error' => 'Invalid request']);
?>
