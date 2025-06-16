<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

require __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

// 👇 รับข้อมูล JSON แทน $_POST

$fullname = $input['fullname'] ?? '';
$email    = $input['email'] ?? '';
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($fullname) || empty($username) || empty($password) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
    exit();
}

$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้นี้ถูกใช้แล้ว']);
    exit();
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (fullname,username, password, email, role) VALUES (?, ?, ?, ?, 'customer')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $hashed, $fullname, $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'สมัครสมาชิกสำเร็จ']);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการสมัครสมาชิก',
        'error' => $stmt->error
    ]);
}
?>
