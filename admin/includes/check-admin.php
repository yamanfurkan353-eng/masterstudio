<?php
// Admin oturumunu kontrol et
if (!isset($_SESSION['admin_id'])) {
    // Doğru yolu hesapla - hem modules/ hem admin/ kökünden çalışır
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    if (strpos($script_dir, '/modules') !== false) {
        header('Location: ../auth/login.php');
    } else {
        header('Location: auth/login.php');
    }
    exit;
}

// Oturum zaman aşımı kontrolü (1 saat)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Admin bilgilerini al
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];
$admin_role = $_SESSION['admin_role'];
