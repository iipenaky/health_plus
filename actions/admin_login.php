<?php
include '../db/db.php';
global $conn;
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        die('Please fill in all required fields.'); 
    }

    $stmt = $conn->prepare('SELECT `user_id`, `email`, `password_hash`, `role` FROM  `HealthUsers` WHERE `email` = ?');
    $stmt->bind_param('s', $email); 
    $stmt->execute(); 
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        $user = $results->fetch_assoc(); 
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; 

            if ($_SESSION['role'] == 'admin') {
                header('Location: ../frontend/admin.html');
            } else {
                echo '<script>alert("Access denied. You are not an admin.");</script>';
                echo '<script>window.location.href = "../frontend/index.html";</script>';
            }
        } else {
            echo '<script>alert("Incorrect password. Please try again.");</script>';
            echo '<script>window.location.href = "../frontend/admin_login.html";</script>';
        }
    } else {
        header('Location: ../frontend/login.html');
    }

    $stmt->close();
}

$conn->close();
?>
