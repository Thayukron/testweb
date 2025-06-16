<?php
require __DIR__ . '/../db.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไอดีลูกค้า']);
    exit();
}

// ตรวจสอบว่ามีลูกค้าคนนี้อยู่ไหม
$check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user'");
$check->bind_param("i", $id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบลูกค้าที่ต้องการลบ']);
    exit();
}

// ลบลูกค้า
$delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $id);
if ($delete->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบ']);
}
?>
