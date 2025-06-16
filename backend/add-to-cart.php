<?php
// backend/add-to-cart.php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$user_id || !$product_id) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    exit();
}

// ตรวจสอบว่ามีสินค้าในตะกร้าอยู่แล้วหรือยัง
$sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // อัปเดตจำนวนสินค้า
    $row = $result->fetch_assoc();
    $new_qty = $row['quantity'] + $quantity;
    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_qty, $row['id']);
    $update->execute();
} else {
    // เพิ่มสินค้าใหม่
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
}

echo json_encode(['success' => true, 'message' => 'เพิ่มสินค้าลงตะกร้าแล้ว']);
?>
