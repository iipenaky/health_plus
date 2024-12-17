<?php
// Include the database connection file
include '../db/db.php';
global $conn; // Declare the global database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if the request method is POST (indicating form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted email and password, trimming whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if the email or password fields are empty
    if (empty($email) || empty($password)) {
        die('Please fill in all required fields.'); // Stop execution and show an error
    }

    // Prepare an SQL statement to find the user by email
    $stmt = $conn->prepare('SELECT `user_id`, `email`, `password_hash`, `role` FROM `HealthUsers` WHERE `email` = ?');
    $stmt->bind_param('s', $email); // Bind the email parameter to the statement
    $stmt->execute(); // Execute the query
    $results = $stmt->get_result(); // Get the results of the query

    // Check if any user was found with the provided email
    if ($results->num_rows > 0) {
        $user = $results->fetch_assoc(); // Fetch the user's data as an associative array

        // Verify the provided password against the stored password hash
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Check if the user has an admin role
            if ($_SESSION['role'] == 'admin') {
                // Redirect to the admin dashboard
                header('Location: ../frontend/admin.html');
            } else {
                // If the user is not an admin, display an alert and redirect to the home page
                echo '<script>alert("Access denied. You are not an admin.");</script>';
                echo '<script>window.location.href = "../frontend/index.html";</script>';
            }
        } else {
            // If the password is incorrect, display an alert and redirect to the login page
            echo '<script>alert("Incorrect password. Please try again.");</script>';
            echo '<script>window.location.href = "../frontend/admin_login.html";</script>';
        }
    } else {
        // Redirect to the general login page if the email doesn't exist in the database
        header('Location: ../frontend/login.html');
    }

    // Close the prepared statement after execution
    $stmt->close();
}

// Close the database connection at the end of the script
$conn->close();
?>
