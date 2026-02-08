<!DOCTYPE html>
<html lang="<?php echo DEFAULT_LANG; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterStudio Hotel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/<?php echo DEFAULT_THEME; ?>.css" id="theme-style">
    <!-- Font Awesome veya diğer vendor CSS dosyaları buraya eklenecek -->
</head>
<body class="<?php echo DEFAULT_THEME; ?>-mode">
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">MasterStudio</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" data-lang-key="home">Anasayfa</a></li>
                    <li><a href="rooms.php" data-lang-key="rooms">Odalarımız</a></li>
                    <li><a href="about.php" data-lang-key="about">Hakkımızda</a></li>
                    <li><a href="contact.php" data-lang-key="contact">İletişim</a></li>
                    <li><a href="booking.php" class="btn btn-primary" data-lang-key="book_now">Şimdi Rezervasyon Yap</a></li>
                </ul>
            </nav>
            <div class="theme-switcher">
                <button id="theme-toggle">Tema Değiştir</button>
            </div>
            <div class="lang-switcher">
                <select id="lang-toggle">
                    <option value="tr">Türkçe</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>
    </header>
    <main>