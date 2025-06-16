<?php
header('Content-Type: application/json');

// รับ raw JSON
$data = file_get_contents('php://input');

// แปลง JSON string เป็น array (true = associative array)
$data = json_decode($data, true);

// ตรวจสอบว่าได้ข้อมูลมาหรือไม่
// if (!isset($data['username']) || !isset($data['password'])) {
//     echo json_encode(['error' => 'ข้อมูลไม่ครบถ้วน']);
//     exit;
// }

$username = $data['username'];
$password = $data['password'];

// ส่งค่ากลับ
echo json_encode([
    'username' => $username,
    'password' => $password
]);
