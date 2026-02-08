<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'masterstudio_hotel');

// Veritabanı bağlantısı
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Tema ve Dil Ayarları
define('DEFAULT_THEME', 'light'); // Varsayılan tema: light veya dark
define('DEFAULT_LANG', 'tr');    // Varsayılan dil: tr veya en

?>