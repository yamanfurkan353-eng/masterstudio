<?php
session_start();
require_once 'core/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = htmlspecialchars(trim($_POST['guest_name'] ?? ''));
    $guest_email = htmlspecialchars(trim($_POST['guest_email'] ?? ''));
    $guest_phone = htmlspecialchars(trim($_POST['guest_phone'] ?? ''));
    $room_type = htmlspecialchars(trim($_POST['room_type'] ?? ''));
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $num_guests = intval($_POST['num_guests'] ?? 0);

    if (!empty($guest_name) && !empty($guest_email) && !empty($room_type) && !empty($check_in) && !empty($check_out) && $num_guests > 0) {
        
        $stmt = $conn->prepare("INSERT INTO reservations (guest_name, guest_email, guest_phone, room_type, check_in_date, check_out_date, num_guests, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("sssssssi", $guest_name, $guest_email, $guest_phone, $room_type, $check_in, $check_out, $num_guests);

        if ($stmt->execute()) {
            $message = 'Rezervasyonunuz başarıyla alınmıştır! Onay e-postası size gönderilecektir.';
        } else {
            $error = 'Rezervasyon sırasında hata oluştu. Lütfen tekrar deneyin.';
        }
    } else {
        $error = 'Lütfen tüm alanları doldurunuz.';
    }
}

$room_types = $conn->query("SELECT DISTINCT name_tr FROM room_types WHERE is_active = TRUE");
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'tr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyon Yap - MasterStudio Hotel</title>
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
                    <li><a href="contact.php">İletişim</a></li>
                    <li><a href="booking.php" class="btn btn-primary active">Şimdi Rezervasyon Yap</a></li>
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
                <h1 style="font-size: 42px;">Rezervasyon Yap</h1>
                <p>Şimdi en uygun odanızı ayırtın</p>
            </div>
        </section>

        <!-- Booking Form -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="max-width: 600px; margin: 0 auto; background: var(--light-bg); padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">

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
                            <label for="guest_name">Adınız *</label>
                            <input type="text" id="guest_name" name="guest_name" required>
                        </div>

                        <div class="form-group">
                            <label for="guest_email">E-posta Adresiniz *</label>
                            <input type="email" id="guest_email" name="guest_email" required>
                        </div>

                        <div class="form-group">
                            <label for="guest_phone">Telefon Numaranız</label>
                            <input type="tel" id="guest_phone" name="guest_phone">
                        </div>

                        <div class="form-group">
                            <label for="room_type">Oda Tipi *</label>
                            <select id="room_type" name="room_type" required>
                                <option value="">Seçiniz...</option>
                                <?php while ($rt = $room_types->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($rt['name_tr']); ?>">
                                        <?php echo htmlspecialchars($rt['name_tr']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="check_in">Giriş Tarihi *</label>
                            <input type="date" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="check_out">Çıkış Tarihi *</label>
                            <input type="date" id="check_out" name="check_out" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="num_guests">Misafir Sayısı *</label>
                            <input type="number" id="num_guests" name="num_guests" min="1" max="10" required value="1">
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 15px; font-size: 16px;">Rezervasyon Tamamla</button>
                    </form>

                    <p style="margin-top: 20px; color: #666; font-size: 14px;">
                        * İşaretli alanlar zorunludur. Rezervasyon onayı e-posta adresinize gönderilecektir.
                    </p>
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
