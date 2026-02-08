<?php

// Ortam değişkenlerinden yükle
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $env = parse_ini_file($env_file);
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Veritabanı Ayarları (örnek değerler - bunları .env dosyasında ayarla)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'hotel_user');
define('DB_PASS', getenv('DB_PASS') ?: 'hotel_password');
define('DB_NAME', getenv('DB_NAME') ?: 'masterstudio_hotel');

// Ortam modu (development/production)
define('APP_ENV', getenv('PHP_ENV') ?: 'development');

// Veritabanı bağlantısı
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Database Error: " . $conn->connect_error);
    if (APP_ENV === 'development') {
        die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
    } else {
        die("Hata oluştu. Lütfen daha sonra tekrar deneyin.");
    }
}

// Tema ve Dil Ayarları
define('DEFAULT_THEME', 'light'); // Varsayılan tema: light veya dark
define('DEFAULT_LANG', 'tr');    // Varsayılan dil: tr veya en

?>