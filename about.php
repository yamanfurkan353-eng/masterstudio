<?php
session_start();
require_once 'core/config.php';

$hotel = $conn->query("SELECT * FROM hotel_info LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'tr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkımızda - MasterStudio Hotel</title>
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
                    <li><a href="about.php" class="active">Hakkımızda</a></li>
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
                <h1 style="font-size: 42px;">Hakkımızda</h1>
                <p>Lüks ve kalite ile biliniyoruz</p>
            </div>
        </section>

        <!-- About Content -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
                    <div>
                        <div style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                            Otel Görseli
                        </div>
                    </div>
                    <div>
                        <h2 style="font-size: 32px; margin-bottom: 20px;">MasterStudio Hotel</h2>
                        <p style="font-size: 16px; line-height: 1.8; margin-bottom: 15px; color: var(--text-color);">
                            <?php echo nl2br(htmlspecialchars($hotel['description_tr'] ?? 'Lüks ve konforlu konaklama deneyimi')); ?>
                        </p>
                        
                        <h3 style="margin-top: 30px; margin-bottom: 15px;">Bilgilerimiz:</h3>
                        <ul style="list-style: none; color: var(--text-color);">
                            <li style="margin: 10px 0;">📍 <strong>Adres:</strong> <?php echo htmlspecialchars($hotel['address_tr'] ?? 'İstanbul, Türkiye'); ?></li>
                            <li style="margin: 10px 0;">📞 <strong>Telefon:</strong> <?php echo htmlspecialchars($hotel['phone'] ?? '+90 212 XXXXXXX'); ?></li>
                            <li style="margin: 10px 0;">📧 <strong>E-posta:</strong> <?php echo htmlspecialchars($hotel['email'] ?? 'info@masterstudio.com'); ?></li>
                            <li style="margin: 10px 0;">⭐ <strong>Yıldız Derecelendirmesi:</strong> <?php echo str_repeat('★', $hotel['star_rating'] ?? 5); ?></li>
                            <li style="margin: 10px 0;">🕐 <strong>Giriş Saati:</strong> <?php echo $hotel['check_in_time'] ?? '14:00'; ?></li>
                            <li style="margin: 10px 0;">🕐 <strong>Çıkış Saati:</strong> <?php echo $hotel['check_out_time'] ?? '11:00'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section style="background: var(--light-bg); padding: 60px 0;">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: 40px; font-size: 32px;">Neden Bizi Seçmelisiniz?</h2>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                    <div style="text-align: center; padding: 20px;">
                        <h3 style="color: #667eea; margin-bottom: 10px; font-size: 20px;">🏆 Kalite Hizmeti</h3>
                        <p style="color: var(--text-color);">Uluslararası standartlarda hizmet kalitesi ile öne çıkıyoruz.</p>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <h3 style="color: #667eea; margin-bottom: 10px; font-size: 20px;">👥 Eğitimli Personel</h3>
                        <p style="color: var(--text-color);">Müşteri memnuniyeti için eğitimli ve deneyimli personel.</p>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <h3 style="color: #667eea; margin-bottom: 10px; font-size: 20px;">💎 Lüks Oda</h3>
                        <p style="color: var(--text-color);">Modern iç tasarım ve son teknoloji donanım ile uygun odalar.</p>
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
