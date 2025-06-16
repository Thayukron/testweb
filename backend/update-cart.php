<?php
// backend/update-cart.php
require __DIR__ . '/../db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$cart_id = $_POST['cart_id'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if (!$user_id || !$cart_id || $quantity === null) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    exit();
}

$sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $quantity, $cart_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'อัปเดตจำนวนสินค้าเรียบร้อย']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตตะกร้าได้']);
}
?>
