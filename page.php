<?php
session_start();
require_once 'core/config.php';

// Talep edilen sayfa slug'ını al
$page_slug = $_GET['page'] ?? 'hakkimizda';
$page_slug = htmlspecialchars(trim($page_slug));

// Veritabanından sayfayı al
$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? AND is_published = 1");
$stmt->bind_param("s", $page_slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();

if (!$page) {
    http_response_code(404);
    $page_title = '404 - Sayfa Bulunamadı';
    $page_content = '<h2>Sayfa Bulunamadı</h2><p>Aradığınız sayfa bulunamadı.</p>';
} else {
    $page_title = $page['title_tr'];
    $page_content = nl2br(htmlspecialchars($page['content_tr']));
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - MasterStudio Hotel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark.css" id="theme-style">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">🏨 MasterStudio</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Anasayfa</a></li>
                    <li><a href="rooms.php">Odalarımız</a></li>
                    <li><a href="about.php">Hakkımızda</a></li>
                    <li><a href="contact.php">İletişim</a></li>
                    <li><a href="booking.php" class="btn btn-primary">Şimdi Rezervasyon Yap</a></li>
                </ul>
            </nav>
            <div class="theme-switcher">
                <button id="theme-toggle">🌙</button>
            </div>
            <div class="lang-switcher">
                <select id="lang-toggle">
                    <option value="tr" selected>Türkçe</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>
    </header>

    <main>
        <!-- Page Title -->
        <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px 0; text-align: center;">
            <div class="container">
                <h1 style="font-size: 42px;"><?php echo htmlspecialchars($page_title); ?></h1>
            </div>
        </section>

        <!-- Page Content -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: var(--text-color);">
                    <?php echo $page_content; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> MasterStudio Hotel. Tüm Hakları Saklıdır.</p>
            <div class="social-links">
                <a href="https://facebook.com" target="_blank">Facebook</a>
                <a href="https://twitter.com" target="_blank">Twitter</a>
                <a href="https://instagram.com" target="_blank">Instagram</a>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/lang.js"></script>
</body>
</html>
