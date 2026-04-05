<?php
session_start();
include 'config.php';

if (isset($_SESSION['admin'])) {
    $admin = $_SESSION['admin'];


    $stmt = $conn->prepare("UPDATE admin_logs SET logout_time = NOW() WHERE admin_name = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$admin]);
}


session_destroy();
header("Location: login.php");
exit();
?>
