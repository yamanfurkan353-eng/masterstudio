<?php
session_start();
require_once 'core/config.php';

$hotel = $conn->query("SELECT * FROM hotel_info LIMIT 1")->fetch_assoc();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message_text = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_text)) {
        // E-posta gönder
        $to = $hotel['email'] ?? 'info@example.com';
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $body = "Ad: " . $name . "\r\nE-posta: " . $email . "\r\n\r\nMesaj:\r\n" . $message_text;

        if (mail($to, $subject, $body, $headers)) {
            $message = 'Mesajınız başarıyla gönderildi. En kısa sürede size döneceğiz.';
        } else {
            $error = 'Mesaj gönderilirken hata oluştu. Lütfen tekrar deneyin.';
        }
    } else {
        $error = 'Lütfen tüm alanları doldurunuz.';
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'tr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - MasterStudio Hotel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark.css" id="theme-style">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">MasterStudio</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Anasayfa</a></li>
                    <li><a href="rooms.php">Odalarımız</a></li>
                    <li><a href="about.php">Hakkımızda</a></li>
                    <li><a href="contact.php" class="active">İletişim</a></li>
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
                <h1 style="font-size: 42px;">İletişim</h1>
                <p>Bize ulaşmaktan çekinmeyin</p>
            </div>
        </section>

        <!-- Contact Section -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    
                    <!-- Contact Info -->
                    <div>
                        <h2 style="margin-bottom: 30px; font-size: 28px;">İletişim Bilgilerimiz</h2>
                        
                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #667eea; margin-bottom: 10px;">📍 Adres</h3>
                            <p style="color: var(--text-color);"><?php echo htmlspecialchars($hotel['address_tr'] ?? 'İstanbul, Türkiye'); ?></p>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #667eea; margin-bottom: 10px;">📞 Telefon</h3>
                            <p style="color: var(--text-color);"><?php echo htmlspecialchars($hotel['phone'] ?? '+90 212 XXXXXXX'); ?></p>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #667eea; margin-bottom: 10px;">📧 E-posta</h3>
                            <p style="color: var(--text-color);">
                                <a href="mailto:<?php echo htmlspecialchars($hotel['email'] ?? 'info@example.com'); ?>" style="color: #667eea; text-decoration: none;">
                                    <?php echo htmlspecialchars($hotel['email'] ?? 'info@example.com'); ?>
                                </a>
                            </p>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #667eea; margin-bottom: 15px;">🕐 Çalışma Saatleri</h3>
                            <p style="color: var(--text-color);">
                                <strong>Pazartesi - Pazar</strong><br>
                                24 Saat Açık (Özel Rezervasyonlar İçin)
                            </p>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #667eea; margin-bottom: 15px;">📱 Sosyal Medya</h3>
                            <div style="display: flex; gap: 15px;">
                                <a href="https://facebook.com" target="_blank" style="color: #667eea; text-decoration: none; font-size: 18px;">Facebook</a>
                                <a href="https://twitter.com" target="_blank" style="color: #667eea; text-decoration: none; font-size: 18px;">Twitter</a>
                                <a href="https://instagram.com" target="_blank" style="color: #667eea; text-decoration: none; font-size: 18px;">Instagram</a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div>
                        <h2 style="margin-bottom: 30px; font-size: 28px;">Bize Mesaj Gönderin</h2>

                        <?php if (!empty($message)): ?>
                            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                ✓ <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                ✗ <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                            <div class="form-group">
                                <label for="name">Adınız *</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">E-posta Adresiniz *</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="subject">Konu *</label>
                                <input type="text" id="subject" name="subject" required>
                            </div>

                            <div class="form-group">
                                <label for="message">Mesaj *</label>
                                <textarea id="message" name="message" rows="6" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding: 12px; font-size: 16px;">Gönder</button>
                        </form>
                    </div>
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
