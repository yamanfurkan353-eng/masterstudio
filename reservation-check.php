<?php
session_start();
require_once 'core/config.php';
require_once 'core/functions.php';

set_security_headers();

$reservations = [];
$searched = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $searched = true;

    if (empty($email) || !validate_email($email)) {
        $error = 'Geçerli bir e-posta adresi giriniz.';
    } else {
        $stmt = $conn->prepare("SELECT id, guest_name, room_type, check_in_date, check_out_date, num_guests, status, created_at FROM reservations WHERE guest_email = ? ORDER BY created_at DESC LIMIT 20");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row;
        }
        $stmt->close();
    }
}

$status_labels = ['pending' => 'Beklemede', 'confirmed' => 'Onaylı', 'cancelled' => 'İptal'];
$status_colors = ['pending' => '#856404', 'confirmed' => '#155724', 'cancelled' => '#721c24'];
$status_bgs = ['pending' => '#fff3cd', 'confirmed' => '#d4edda', 'cancelled' => '#f8d7da'];
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'tr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyon Sorgula - MasterStudio Hotel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/<?php echo ($_SESSION['theme'] ?? 'light'); ?>.css" id="theme-style">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo"><a href="index.php">MasterStudio</a></div>
            <nav>
                <ul>
                    <li><a href="index.php">Anasayfa</a></li>
                    <li><a href="rooms.php">Odalarımız</a></li>
                    <li><a href="about.php">Hakkımızda</a></li>
                    <li><a href="contact.php">İletişim</a></li>
                    <li><a href="reservation-check.php" class="active">Rezervasyon Sorgula</a></li>
                    <li><a href="booking.php" class="btn btn-primary">Şimdi Rezervasyon Yap</a></li>
                </ul>
            </nav>
            <div class="theme-switcher"><button id="theme-toggle">🌙</button></div>
            <div class="lang-switcher">
                <select id="lang-toggle"><option value="tr" selected>Türkçe</option><option value="en">English</option></select>
            </div>
        </div>
    </header>

    <main>
        <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px 0; text-align: center;">
            <div class="container">
                <h1 style="font-size: 42px;">Rezervasyon Sorgula</h1>
                <p>E-posta adresiniz ile rezervasyonlarınızı kontrol edin</p>
            </div>
        </section>

        <section style="padding: 60px 0;">
            <div class="container">
                <div style="max-width: 600px; margin: 0 auto;">

                    <form method="POST" style="background: var(--light-bg); padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <div class="form-group">
                            <label for="email" style="font-size: 16px;">E-posta Adresiniz</label>
                            <input type="email" id="email" name="email" required placeholder="Rezervasyon yaptığınız e-posta adresi" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" style="padding: 14px; font-size: 16px;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;">Rezervasyonlarımı Sorgula</button>
                    </form>

                    <?php if (!empty($error)): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($searched && empty($error)): ?>
                        <?php if (empty($reservations)): ?>
                            <div style="text-align: center; padding: 40px; background: var(--light-bg); border-radius: 10px;">
                                <p style="font-size: 18px; color: #666; margin-bottom: 15px;">Bu e-posta adresi ile kayıtlı rezervasyon bulunamadı.</p>
                                <a href="booking.php" class="btn btn-primary">Yeni Rezervasyon Yap</a>
                            </div>
                        <?php else: ?>
                            <h2 style="margin-bottom: 20px; font-size: 22px;"><?php echo count($reservations); ?> Rezervasyon Bulundu</h2>

                            <?php foreach ($reservations as $res): ?>
                                <?php
                                $in = new DateTime($res['check_in_date']);
                                $out = new DateTime($res['check_out_date']);
                                $nights = $in->diff($out)->days;
                                ?>
                                <div style="background: var(--light-bg); padding: 25px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid <?php echo $status_colors[$res['status']] ?? '#999'; ?>;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                        <h3 style="margin: 0; color: var(--text-color);"><?php echo htmlspecialchars($res['room_type']); ?></h3>
                                        <span style="padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; background: <?php echo $status_bgs[$res['status']] ?? '#eee'; ?>; color: <?php echo $status_colors[$res['status']] ?? '#333'; ?>;">
                                            <?php echo $status_labels[$res['status']] ?? $res['status']; ?>
                                        </span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; color: var(--text-color);">
                                        <div>
                                            <small style="color: #999;">Giriş</small><br>
                                            <strong><?php echo date('d.m.Y', strtotime($res['check_in_date'])); ?></strong>
                                        </div>
                                        <div>
                                            <small style="color: #999;">Çıkış</small><br>
                                            <strong><?php echo date('d.m.Y', strtotime($res['check_out_date'])); ?></strong>
                                        </div>
                                        <div>
                                            <small style="color: #999;">Süre</small><br>
                                            <strong><?php echo $nights; ?> gece</strong>
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px; font-size: 13px; color: #999;">
                                        Misafir: <?php echo htmlspecialchars($res['guest_name']); ?> | <?php echo (int)$res['num_guests']; ?> kişi | Oluşturulma: <?php echo date('d.m.Y', strtotime($res['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
