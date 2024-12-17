<?php
// Include the database configuration file to connect to the database
include '../db/db.php';

// Start session to store user data
session_start();
global $conn;

// Initialize response array
$response = [
    'error' => null,   // Holds any error messages
    'redirect' => null  // Holds the redirection URL
];

// Enable error reporting to display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and trim form data to remove unnecessary whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if any required fields are empty
    if (empty($email) || empty($password)) {
        $response['error'] = 'Please fill in all required fields.';  // Error if fields are empty
    } else {
        // Prepare a statement to check if the email exists in the database
        $stmt = $conn->prepare('SELECT `user_id`, `email`, `password_hash`, `role` FROM `HealthUsers` WHERE `email` = ?');
        $stmt->bind_param('s', $email); // Bind the email parameter to the query
        $stmt->execute(); // Execute the query
        $results = $stmt->get_result(); // Get the result of the query

        // Check if the email exists in the database
        if ($results->num_rows > 0) {
            $user = $results->fetch_assoc(); // Fetch the user data

            // Verify the password using password_verify
            if (password_verify($password, $user['password_hash'])) {
                // If password is correct, store user data in session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // Store user role

                // Set the redirect URL after successful login
                $response['redirect'] = '../frontend/dashboard.html';  // Redirect to the dashboard page
            } else {
                // If password verification fails
                $response['error'] = 'Incorrect password.';
            }
        } else {
            // If email is not found in the database
            $response['error'] = 'Email not found.';
        }

        // Close the statement after execution
        $stmt->close();
    }
}

// Close the database connection at the end
$conn->close();

// Return the response as JSON
echo json_encode($response);
exit();  // End the script execution
?>
