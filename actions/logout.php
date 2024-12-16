<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['redirect' => '../frontend/login.html']);
    exit();
}

include '../db/db.php';

session_unset();
session_destroy();
echo json_encode(['success' => true]);
exit();
?>
