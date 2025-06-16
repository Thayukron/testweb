<?php
// backend/user_info.php
session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['username'], // คุณต้องเซ็ตค่านี้ตอน login
        'role' => $_SESSION['role'] ?? 'user'
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
