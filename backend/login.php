<?php
header('Content-Type: application/json');
// backend/login.php
session_start();
require __DIR__ . '/../db.php';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';


if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit();
}

$sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fullname'] = $user['fullname'];
        echo json_encode(['success' => true, 'role' => $user['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'รหัสผ่านไม่ถูกต้อง']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบชื่อผู้ใช้หรืออีเมลนี้']);
}
