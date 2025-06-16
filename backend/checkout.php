<?php
// backend/checkout.php
require __DIR__ . '/../db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

// ดึงรายการในตะกร้า
$sql = "SELECT c.product_id, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

if (count($items) === 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสินค้าในตะกร้า']);
    exit();
}

// บันทึกคำสั่งซื้อ
$order_sql = "INSERT INTO orders (user_id, order_date, total, status) VALUES (?, NOW(), ?, 'pending')";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("id", $user_id, $total);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;

// บันทึกรายการสินค้าใน order_items
$item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
$item_stmt = $conn->prepare($item_sql);
foreach ($items as $item) {
    $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $item_stmt->execute();
}

// ล้างตะกร้า
$delete_sql = "DELETE FROM cart WHERE user_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $user_id);
$delete_stmt->execute();

echo json_encode(['success' => true, 'message' => 'สั่งซื้อสำเร็จ', 'order_id' => $order_id]);
?>
