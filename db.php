<?php
// backend/db.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ecommerce";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    echo "Conntected success";
}
?>
