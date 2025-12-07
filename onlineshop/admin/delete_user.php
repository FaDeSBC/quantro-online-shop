<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id']);

if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account!";
    header("Location: admin_users.php");
    exit();
}


$check = $conn->query("SELECT id FROM users WHERE id=$id");
if ($check->num_rows > 0) {
    $conn->query("DELETE FROM users WHERE id=$id");
    $_SESSION['success'] = "User deleted successfully!";
} else {
    $_SESSION['error'] = "User not found!";
}

header("Location: admin_users.php");
exit();
?>