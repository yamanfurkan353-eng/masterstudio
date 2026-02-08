<?php
session_start();
require_once 'core/config.php';

// Dil ve tema tercihlerini al
$lang = $_SESSION['lang'] ?? 'tr';
$theme = $_SESSION['theme'] ?? 'light';

$room_types = $conn->query("SELECT * FROM room_types WHERE is_active = TRUE LIMIT 3");
$hotel = $conn->query("SELECT * FROM hotel_info LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MasterStudio Hotel - Lüks ve konforlu konaklama deneyimi">
    <title>MasterStudio Hotel - Lüks Konaklama Deneyimi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/<?php echo $theme; ?>.css" id="theme-style">
    <style>
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-top: 30px; }
        .feature-card { background: var(--light-bg); padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-5px); }
        .feature-card .icon { font-size: 40px; margin-bottom: 15px; }
        .room-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; } 
        .room-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        body.dark-mode .room-card { background: #2a2a2a; }
        .room-card:hover { transform: translateY(-5px); }
        .room-image { height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; }
        .room-content { padding: 20px; }
        .price { color: #667eea; font-size: 20px; font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body class="<?php echo $theme; ?>-mode">
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
        <!-- Hero Section -->
        <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; text-align: center;">
            <div class="container">
                <h1 style="font-size: 48px; margin-bottom: 20px; font-weight: 700;">MasterStudio Hotel'e Hoş Geldiniz</h1>
                <p style="font-size: 20px; margin-bottom: 30px; opacity: 0.95;">Lüks ve Konforur Birleşimi, Unutulmaz Deneyim</p>
                <a href="booking.php" class="btn btn-primary" style="font-size: 16px; padding: 15px 40px;">Hemen Rezervasyon Yap</a>
            </div>
        </section>

        <!-- Rooms Showcase -->
        <section style="padding: 60px 0;">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: 40px; font-size: 32px;">Seçkin Odalarımız</h2>
                <div class="room-grid">
                    <?php while ($room = $room_types->fetch_assoc()): ?>
                        <div class="room-card">
                            <div class="room-image">Oda Görseli</div>
                            <div class="room-content">
                                <h3><?php echo htmlspecialchars($room['name_tr']); ?></h3>
                                <p style="color: #666; font-size: 14px;"><?php echo htmlspecialchars(substr($room['description_tr'], 0, 80)); ?>...</p>
                                <div class="price">₺<?php echo number_format($room['price_per_night'], 2); ?> / Gece</div>
                                <p style="color: #999; font-size: 13px;">👥 Max. <?php echo $room['max_guests']; ?> Misafir</p>
                                <a href="booking.php" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 10px;">Rezervasyon Yap</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="rooms.php" class="btn btn-secondary" style="font-size: 16px;">Tüm Odaları Gör →</a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section style="background: var(--light-bg); padding: 60px 0;">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: 40px; font-size: 32px;">Neden Bizi Seçmelisiniz?</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="icon">⭐</div>
                        <h3>5 Yıldız Hizmet</h3>
                        <p>Uluslararası standartlarda kalite ve müşteri memnuniyeti</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">🛏️</div>
                        <h3>Lüks Odalar</h3>
                        <p>Modern tasarım ve son teknoloji uygulamalar</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">👥</div>
                        <h3>Eğitimli Personel</h3>
                        <p>24/7 müşteri memnuniyeti için hazır tim</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">🍽️</div>
                        <h3>Gourmet Yemekler</h3>
                        <p>Dünya mutfağından seçkin yemekler</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">🏊</div>
                        <h3>Havuz & Wellness</h3>
                        <p>Komplet spa ve fitness merkezi</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon">🚗</div>
                        <h3>Ücretsiz Otopark</h3>
                        <p>Güvenli ve geniş otopark alanı</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Info Section -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
                    <div>
                        <h2 style="font-size: 28px; margin-bottom: 20px;">Bir Ev Gibi Hissedin</h2>
                        <p style="margin-bottom: 15px; color: var(--text-color); line-height: 1.8;">
                            <?php echo htmlspecialchars($hotel['description_tr'] ?? 'MasterStudio Hotel, konaklamanızı unutulmaz bir deneyime dönüştürmek için tasarlanmıştır.'); ?>
                        </p>
                        <ul style="list-style: none; color: var(--text-color);">
                            <li style="margin: 10px 0;">✓ Ücretsiz WiFi ve Park</li>
                            <li style="margin: 10px 0;">✓ 24/7 Resepsiyon Hizmeti</li>
                            <li style="margin: 10px 0;">✓ Oda Servisi (07:00 - 23:00)</li>
                            <li style="margin: 10px 0;">✓ Ücretsiz Kahvaltı</li>
                        </ul>
                        <a href="about.php" class="btn btn-primary" style="margin-top: 20px;">Daha Fazla Bilgi →</a>
                    </div>
                    <div style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                        Otel Görseli
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
