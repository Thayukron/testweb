<?php
s// backend/logout.php
session_start();
session_unset();
session_destroy();
echo json_encode(['success' => true, 'message' => 'ออกจากระบบเรียบร้อย']);
