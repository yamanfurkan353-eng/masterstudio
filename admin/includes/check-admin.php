<?php
// Admin oturumunu kontrol et
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

// Admin ID ve roll bilgilerini al
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];
$admin_role = $_SESSION['admin_role'];
?>
