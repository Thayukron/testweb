<?php
// backend/report_api.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../libs/mpdf/src/Mpdf.php'; // สำหรับ mPDF แบบ manual
session_start();

$role = $_SESSION['role'] ?? null;
if ($role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'เฉพาะผู้ดูแลระบบเท่านั้น']);
    exit();
}

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
$export = $_GET['export'] ?? null;
$whereClause = "";
if ($start && $end) {
    $startDate = $conn->real_escape_string($start);
    $endDate = $conn->real_escape_string($end);
    $whereClause = " AND order_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}

$report = [];

// ยอดขายรวมรายวัน
$sql_day = "SELECT DATE(order_date) as day, SUM(total) as total FROM orders WHERE status = 'completed' $whereClause GROUP BY DATE(order_date) ORDER BY day DESC LIMIT 7";
$result_day = $conn->query($sql_day);
$report['daily'] = [];
while ($row = $result_day->fetch_assoc()) {
    $report['daily'][] = $row;
}

// ยอดขายรวมรายเดือน
$sql_month = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(total) as total FROM orders WHERE status = 'completed' $whereClause GROUP BY month ORDER BY month DESC LIMIT 6";
$result_month = $conn->query($sql_month);
$report['monthly'] = [];
while ($row = $result_month->fetch_assoc()) {
    $report['monthly'][] = $row;
}

// สินค้าขายดี
$sql_top = "SELECT p.name, SUM(oi.quantity) as sold FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE o.status = 'completed' $whereClause GROUP BY p.id ORDER BY sold DESC LIMIT 5";
$result_top = $conn->query($sql_top);
$report['top_products'] = [];
while ($row = $result_top->fetch_assoc()) {
    $report['top_products'][] = $row;
}

// Export Excel
if ($export === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=report_export.xls");
    echo "<table border='1'>";
    echo "<tr><th colspan='2'>ยอดขายรายวัน</th></tr>";
    echo "<tr><th>วันที่</th><th>ยอดขาย</th></tr>";
    foreach ($report['daily'] as $row) {
        echo "<tr><td>{$row['day']}</td><td>{$row['total']}</td></tr>";
    }

    echo "<tr><th colspan='2'>ยอดขายรายเดือน</th></tr>";
    echo "<tr><th>เดือน</th><th>ยอดขาย</th></tr>";
    foreach ($report['monthly'] as $row) {
        echo "<tr><td>{$row['month']}</td><td>{$row['total']}</td></tr>";
    }

    echo "<tr><th colspan='2'>สินค้าขายดี</th></tr>";
    echo "<tr><th>ชื่อสินค้า</th><th>จำนวนที่ขาย</th></tr>";
    foreach ($report['top_products'] as $row) {
        echo "<tr><td>{$row['name']}</td><td>{$row['sold']}</td></tr>";
    }

    echo "</table>";
    exit();
}

// Export PDF
if ($export === 'pdf') {
    $mpdf = new \Mpdf\Mpdf();
    ob_start();
    echo "<h2>รายงานยอดขาย</h2>";
    echo "<h3>ยอดขายรายวัน</h3><table border='1' width='100%' style='border-collapse: collapse;'>";
    echo "<tr><th>วันที่</th><th>ยอดขาย</th></tr>";
    foreach ($report['daily'] as $row) {
        echo "<tr><td>{$row['day']}</td><td>{$row['total']}</td></tr>";
    }
    echo "</table><br>";

    echo "<h3>ยอดขายรายเดือน</h3><table border='1' width='100%' style='border-collapse: collapse;'>";
    echo "<tr><th>เดือน</th><th>ยอดขาย</th></tr>";
    foreach ($report['monthly'] as $row) {
        echo "<tr><td>{$row['month']}</td><td>{$row['total']}</td></tr>";
    }
    echo "</table><br>";

    echo "<h3>สินค้าขายดี</h3><table border='1' width='100%' style='border-collapse: collapse;'>";
    echo "<tr><th>ชื่อสินค้า</th><th>จำนวนที่ขาย</th></tr>";
    foreach ($report['top_products'] as $row) {
        echo "<tr><td>{$row['name']}</td><td>{$row['sold']}</td></tr>";
    }
    echo "</table>";

    $html = ob_get_clean();
    $mpdf->WriteHTML($html);
    $mpdf->Output('report.pdf', 'D');
    exit();
}

echo json_encode(['success' => true, 'report' => $report]);
?>
