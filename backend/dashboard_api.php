<?php
// backend/dashboard_api.php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

// ตรวจสอบว่าเป็นผู้ดูแลระบบหรือไม่
$role = $_SESSION['role'] ?? null;
if ($role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'เฉพาะผู้ดูแลระบบเท่านั้น']);
    exit();
}

// 1. ยอดขายวันนี้
$sql_sales = "SELECT SUM(total) as today_sales FROM orders WHERE DATE(order_date) = CURDATE() AND status = 'completed'";
$result_sales = $conn->query($sql_sales);
$today_sales = $result_sales->fetch_assoc()['today_sales'] ?? 0;

// 2. จำนวนคำสั่งซื้อวันนี้
$sql_orders = "SELECT COUNT(*) as today_orders FROM orders WHERE DATE(order_date) = CURDATE() AND status = 'completed'";
$result_orders = $conn->query($sql_orders);
$today_orders = $result_orders->fetch_assoc()['today_orders'] ?? 0;

// 3. สินค้าที่สต็อกต่ำ (น้อยกว่าหรือเท่ากับ 5)
$sql_low = "SELECT COUNT(*) as low_stock FROM products WHERE stock <= 5";
$result_low = $conn->query($sql_low);
$low_stock = $result_low->fetch_assoc()['low_stock'] ?? 0;

// ส่งกลับเป็น JSON
echo json_encode([
    'success' => true,
    'today_sales' => $today_sales,
    'today_orders' => $today_orders,
    'customer_count' => $customer_count,
    'low_stock' => $low_stock
  ]);
  