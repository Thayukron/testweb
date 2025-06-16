<?php
// backend/delete-from-cart.php
require __DIR__ . '/../db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$cart_id = $_POST['cart_id'] ?? null;

if (!$user_id || !$cart_id) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    exit();
}

$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'ลบสินค้าออกจากตะกร้าเรียบร้อย']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบสินค้าได้']);
}
?>
