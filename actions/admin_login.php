<?php
// Include the database configuration file to connect to the database
include '../db/db.php';
global $conn;
// Enable error reporting to display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to store user data
session_start();

// Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Collect and trim form data to remove unnecessary whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if any required fields are empty
    if (empty($email) || empty($password)) {
        die('Please fill in all required fields.'); // Stop execution if any field is empty
    }

    // Prepare a statement to check if the email exists in the database
    $stmt = $conn->prepare('SELECT `user_id`, `email`, `password_hash`, `role` FROM  `users` WHERE `email` = ?');
    $stmt->bind_param('s', $email); // Bind the email parameter to the query
    $stmt->execute(); // Execute the query
    $results = $stmt->get_result(); // Get the result of the query

    // Check if the email exists in the database
    if ($results->num_rows > 0) {
        $user = $results->fetch_assoc(); // Fetch the user data

        // Verify the password using password_verify
        if (password_verify($password, $user['password_hash'])) {
            // Store user data in session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; // Assuming you store user role as well

            // Check if the user has 'admin' role
            if ($_SESSION['role'] == 'admin') {
                // Redirect to the Admin Dashboard
                header('Location: ../frontend/admin.html');
            } else {
                // Redirect to a page for non-admin users (or home page)
                echo '<script>alert("Access denied. You are not an admin.");</script>';
                echo '<script>window.location.href = "../frontend/index.html";</script>';
            }
        } else {
            echo '<script>alert("Incorrect password. Please try again.");</script>';
            echo '<script>window.location.href = "../frontend/admin_login.html";</script>';
        }
    } else {
        header('Location: ../frontend/login.html'); // Redirect if email doesn't exist
    }

    // Close the statement after execution
    $stmt->close();
}

// Close the database connection at the end
$conn->close();
?>
