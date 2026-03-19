<?php
session_start();

$isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
if (!$isAdmin) {
    header('Location: login.php');
    exit();
}

require 'db.php';

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($userId <= 0) {
    header('Location: admin_dashboard.php');
    exit();
}

$adminId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId === $adminId) {
    header('Location: admin_dashboard.php');
    exit();
}

$stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
if ($stmt) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_dashboard.php');
exit();

