<?php
// backend/get-orders.php
require __DIR__ . '/../db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!$user_id || !$role) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

if ($role === 'admin') {
    $sql = "SELECT o.id, o.order_date, o.status, o.total, u.fullname FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, order_date, status, total FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(['success' => true, 'orders' => $orders]);
?>
