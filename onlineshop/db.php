<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'onlineshopdb';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_errno) {
    die("Connection failed: " . $conn->connect_error);
}
?>
