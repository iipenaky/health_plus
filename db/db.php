<?php
$servername = 'localhost'; 
$username = 'root'; 
$password = '';  
$dbname = 'health_plus';        

$conn = mysqli_connect($servername, $username, $password, $dbname) 
    or die('Unable to connect'); 

if ($conn->connect_error) {
    die('Connection failed'); 
} else {
}
?>
