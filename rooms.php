<?php
session_start();
require_once 'core/config.php';
require_once 'core/functions.php';

set_security_headers();

$room_types = $conn->query("SELECT * FROM room_types WHERE is_active = TRUE");
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'tr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odalarımız - MasterStudio Hotel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/<?php echo ($_SESSION['theme'] ?? 'light'); ?>.css" id="theme-style">
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
                    <li><a href="rooms.php" class="active">Odalarımız</a></li>
                    <li><a href="about.php">Hakkımızda</a></li>
                    <li><a href="contact.php">İletişim</a></li>
                    <li><a href="reservation-check.php">Rezervasyon Sorgula</a></li>
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
                <h1 style="font-size: 42px;">Odalarımız</h1>
                <p>Konfor ve lüksün birleşimi</p>
            </div>
        </section>

        <!-- Room Types -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
                    <?php if ($room_types && $room_types->num_rows > 0): ?>
                        <?php while ($room = $room_types->fetch_assoc()): ?>
                            <div style="background: var(--light-bg); border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                <div style="height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                                    Oda Görseli
                                </div>
                                <div style="padding: 25px;">
                                    <h3 style="margin-bottom: 10px; color: var(--text-color);"><?php echo htmlspecialchars($room['name_tr']); ?></h3>
                                    <p style="color: #666; margin-bottom: 15px;"><?php echo htmlspecialchars($room['description_tr'] ?? ''); ?></p>

                                    <div style="margin: 15px 0; padding: 15px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                                        <p style="color: var(--text-color); margin: 8px 0;">
                                            <strong>Kapasite:</strong> <?php echo (int)$room['max_guests']; ?> Misafir
                                        </p>
                                        <p style="color: var(--text-color); margin: 8px 0;">
                                            <strong>Fiyat:</strong> <?php echo number_format($room['price_per_night'], 2); ?> TL/Gece
                                        </p>
                                    </div>

                                    <?php if (!empty($room['amenities_tr'])): ?>
                                        <p style="color: var(--text-color); margin-bottom: 10px;"><strong>Kolaylıklar:</strong></p>
                                        <ul style="list-style: none; color: var(--text-color);">
                                            <?php foreach (explode(',', $room['amenities_tr']) as $amenity): ?>
                                                <li style="margin: 5px 0;"><?php echo htmlspecialchars(trim($amenity)); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    <a href="booking.php?room_type=<?php echo urlencode($room['name_tr']); ?>" class="btn btn-primary" style="margin-top: 20px; width: 100%; text-align: center;">Rezervasyon Yap</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; grid-column: 1 / -1;">Henüz oda tipi eklenmemiş.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> MasterStudio Hotel. Tüm Hakları Saklıdır.</p>
            <div class="social-links">
                <a href="#" target="_blank">Facebook</a>
                <a href="#" target="_blank">Twitter</a>
                <a href="#" target="_blank">Instagram</a>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/lang.js"></script>
</body>
</html>
