<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';

// Ayarları al
$q = $conn->query("SELECT * FROM settings");
$settings = array();
while ($row = $q->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Ayarları güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'] ?? '';
    $site_footer_text = $_POST['site_footer_text'] ?? '';
    $social_facebook = $_POST['social_facebook'] ?? '';
    $social_twitter = $_POST['social_twitter'] ?? '';
    $social_instagram = $_POST['social_instagram'] ?? '';

    $settings_update = array(
        'site_name' => $site_name,
        'site_footer_text' => $site_footer_text,
        'social_facebook' => $social_facebook,
        'social_twitter' => $social_twitter,
        'social_instagram' => $social_instagram
    );

    foreach ($settings_update as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
    }

    $message = 'Ayarlar başarıyla güncellendi!';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>MasterStudio</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="reservations.php">Rezervasyonlar</a></li>
                    <li><a href="room-types.php">Oda Tipleri</a></li>
                    <li><a href="rooms.php">Odalar</a></li>
                    <li><a href="hotel-info.php">Otel Bilgileri</a></li>
                    <li><a href="pages.php">Sayfalar</a></li>
                    <li><a href="settings.php" class="active">Ayarlar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Genel Ayarlar</h1>
            </header>

            <section class="form-section">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <h2>Site Bilgileri</h2>

                    <div class="form-group">
                        <label for="site_name">Site Adı:</label>
                        <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'MasterStudio Hotel'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="site_footer_text">Footer Metni:</label>
                        <textarea id="site_footer_text" name="site_footer_text"><?php echo htmlspecialchars($settings['site_footer_text'] ?? ''); ?></textarea>
                    </div>

                    <h2>Sosyal Medya Linkleri</h2>

                    <div class="form-group">
                        <label for="social_facebook">Facebook:</label>
                        <input type="url" id="social_facebook" name="social_facebook" value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                    </div>

                    <div class="form-group">
                        <label for="social_twitter">Twitter:</label>
                        <input type="url" id="social_twitter" name="social_twitter" value="<?php echo htmlspecialchars($settings['social_twitter'] ?? ''); ?>" placeholder="https://twitter.com/...">
                    </div>

                    <div class="form-group">
                        <label for="social_instagram">Instagram:</label>
                        <input type="url" id="social_instagram" name="social_instagram" value="<?php echo htmlspecialchars($settings['social_instagram'] ?? ''); ?>" placeholder="https://instagram.com/...">
                    </div>

                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
