<?php
// backend/add_product.php
require __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['productName'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $description = $_POST['description'] ?? '';
    
    // จัดการไฟล์ภาพ (ถ้ามี)
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = "uploads/" . $filename;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $category, $price, $stock, $imagePath, $description);

    if ($stmt->execute()) {
        echo "บันทึกสินค้าสำเร็จ!";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}
?>
