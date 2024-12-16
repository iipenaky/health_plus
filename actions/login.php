<?php
include '../db/db.php';
session_start();
global $conn;

$response = [
    'error' => null,
    'redirect' => null
];

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if (empty($email) || empty($password)) {
        $response['error'] = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare('SELECT `user_id`, `email`, `password_hash`, `role` FROM `HealthUsers` WHERE `email` = ?');
        $stmt->bind_param('s', $email); 
        $stmt->execute(); 
        $results = $stmt->get_result(); 

        if ($results->num_rows > 0) {
            $user = $results->fetch_assoc(); 
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; 

                $response['redirect'] = '../frontend/dashboard.html';
            } else {
                $response['error'] = 'Incorrect password.';
            }
        } else {
            $response['error'] = 'Email not found.';
        }
        $stmt->close();
    }
}

$conn->close();

echo json_encode($response);
exit();
?>
