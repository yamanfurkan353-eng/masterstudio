<?php

// Ortam değişkenlerinden yükle
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $env = parse_ini_file($env_file);
    if ($env) {
        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }
    }
}

// Veritabanı Ayarları
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'hotel_user');
define('DB_PASS', getenv('DB_PASS') ?: 'hotel_password');
define('DB_NAME', getenv('DB_NAME') ?: 'masterstudio_hotel');

// Ortam modu (development/production)
define('APP_ENV', getenv('PHP_ENV') ?: 'development');

// Oturum güvenlik ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Veritabanı bağlantısı
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Database Error: " . $conn->connect_error);
    if (APP_ENV === 'development') {
        die("Veritabanı bağlantısı başarısız: " . htmlspecialchars($conn->connect_error));
    } else {
        die("Hata oluştu. Lütfen daha sonra tekrar deneyin.");
    }
}

$conn->set_charset("utf8mb4");

// Tema ve Dil Ayarları
define('DEFAULT_THEME', 'light');
define('DEFAULT_LANG', 'tr');
