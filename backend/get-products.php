<?php
// backend/get-products.php
require __DIR__ . '/../db.php';

$sql = "SELECT id, name, description, price, image FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode(['success' => true, 'products' => $products]);
?>
