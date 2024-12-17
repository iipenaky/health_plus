<?php
// Enable error reporting for debugging purposes
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not logged in or not an admin, return a JSON response to redirect to login page
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection
include '../db/db.php';

// Handle GET request - Fetch all health tips
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch health tips in descending order of their creation date
    $query = "SELECT * FROM Health_Tips ORDER BY created_at DESC";
    $result = $conn->query($query);

    // Initialize an array to store the health tips
    $tips = [];
    if ($result && $result->num_rows > 0) {
        // Fetch all tips and store them in the $tips array
        while ($row = $result->fetch_assoc()) {
            $tips[] = $row;
        }
    }

    // Set the header to return JSON data and send the health tips as JSON
    header('Content-Type: application/json');
    echo json_encode($tips);
    exit(); // Exit after sending the response
}

// Handle POST request - Add a new health tip
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if 'action' is 'add' and 'tip_text' is provided
    if (isset($data['action']) && $data['action'] === 'add' && !empty($data['tip_text'])) {
        // Sanitize the tip text before inserting it into the database
        $tip_text = $conn->real_escape_string($data['tip_text']);
        
        // Prepare and execute the SQL query to insert the new health tip
        $query = "INSERT INTO Health_Tips (tip_text) VALUES ('$tip_text')";
        $result = $conn->query($query);

        // Return a success message as JSON
        echo json_encode(['success' => $result]);
        exit(); // Exit after sending the response
    }
}

// Handle DELETE request - Delete a health tip
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Read the incoming JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if 'tip_id' is provided for deletion
    if (isset($data['tip_id'])) {
        // Sanitize and prepare the tip ID for deletion
        $tip_id = (int)$data['tip_id'];
        
        // Prepare and execute the SQL query to delete the health tip
        $query = "DELETE FROM Health_Tips WHERE tip_id = $tip_id";
        $result = $conn->query($query);

        // Return a success message as JSON
        echo json_encode(['success' => $result]);
        exit(); // Exit after sending the response
    }
}

// If none of the above request methods match, return a 400 Bad Request response
header('HTTP/1.0 400 Bad Request');
echo json_encode(['error' => 'Invalid request']);
?>
