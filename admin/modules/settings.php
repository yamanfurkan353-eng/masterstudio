<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';
$current_page = 'settings';
$sidebar_base = '../';

// Ayarları al
$q = $conn->query("SELECT * FROM settings");
$settings = array();
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Ayarları güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $site_name = trim($_POST['site_name'] ?? '');
    $site_footer_text = trim($_POST['site_footer_text'] ?? '');
    $social_facebook = trim($_POST['social_facebook'] ?? '');
    $social_twitter = trim($_POST['social_twitter'] ?? '');
    $social_instagram = trim($_POST['social_instagram'] ?? '');

    // URL doğrulama
    $url_fields = ['social_facebook' => $social_facebook, 'social_twitter' => $social_twitter, 'social_instagram' => $social_instagram];
    $valid = true;
    foreach ($url_fields as $field => $url) {
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            $error = 'Geçersiz URL: ' . htmlspecialchars($field);
            $valid = false;
            break;
        }
    }

    if ($valid) {
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
            $stmt->close();
        }

        $message = 'Ayarlar başarıyla güncellendi!';
        $settings = $settings_update;
    }
}

set_security_headers();
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
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Genel Ayarlar</h1>
            </header>

            <section class="form-section">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <?php echo csrf_field(); ?>

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
