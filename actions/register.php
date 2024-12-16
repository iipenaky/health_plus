<?php
include '../db/db.php';
global $conn;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    

    if (empty($fname) ||empty($email) || empty($password) || empty($confirm_password)) {
        die('Please fill in all required fields.');
    }

    if ($confirm_password != $password) {
        die('Passwords do not match.'); 
    }

    $stmt = $conn->prepare('SELECT email FROM HealthUsers WHERE email = ?');
    $stmt->bind_param('s', $email); 
    $stmt->execute(); 
    $results = $stmt->get_result(); 

    if ($results->num_rows > 0) {
        echo '<script>alert("User already registered.");</script>';
        echo '<script>window.location.href = "../frontend/signup.html";</script>';
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO HealthUsers (name, email, password_hash, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $fname, $email, $hashed_password);

        if ($stmt->execute()) {
            header('Location: ../frontend/login.html'); 
        } else {
            header('Location: ../frontend/signup.html'); 
        }
    }

    $stmt->close();
}

$conn->close();

?>