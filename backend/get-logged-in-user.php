<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

echo json_encode([
    'success' => true,
    'username' => $_SESSION['username'],
    'role' => $_SESSION['role']
]);
?>
