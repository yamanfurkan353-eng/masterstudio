<?php
/**
 * MasterStudio Hotel - Tek Tıkla Web Kurulum Sihirbazı
 * Bu dosyayı sunucunuza yükleyin ve tarayıcıdan açın.
 * Kurulum tamamlandıktan sonra bu dosyayı SİLİN!
 */

// Güvenlik: Zaten kurulu mu kontrol et
if (file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/core/config.php')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    if (!empty($env['DB_HOST'])) {
        $conn = @new mysqli($env['DB_HOST'], $env['DB_USER'] ?? '', $env['DB_PASS'] ?? '', $env['DB_NAME'] ?? '');
        if (!$conn->connect_error) {
            $result = $conn->query("SELECT COUNT(*) as c FROM users");
            if ($result && $result->fetch_assoc()['c'] > 0) {
                die('<div style="max-width:600px;margin:50px auto;padding:30px;background:#f8d7da;color:#721c24;border-radius:10px;font-family:sans-serif;text-align:center;">
                    <h2>Sistem Zaten Kurulu!</h2>
                    <p>Güvenliğiniz için bu dosyayı sunucudan siliniz.</p>
                    <a href="index.php" style="color:#667eea;">Ana Sayfaya Git</a>
                </div>');
            }
        }
    }
}

$step = intval($_GET['step'] ?? 1);
$error = '';
$success = '';

// Adım 2: Gereksinimleri kontrol et
function check_requirements() {
    $checks = [];
    $checks['php_version'] = ['label' => 'PHP >= 7.4', 'ok' => version_compare(PHP_VERSION, '7.4.0', '>='), 'value' => PHP_VERSION];
    $checks['mysqli'] = ['label' => 'MySQLi Eklentisi', 'ok' => extension_loaded('mysqli'), 'value' => extension_loaded('mysqli') ? 'Yüklü' : 'Eksik'];
    $checks['pdo'] = ['label' => 'PDO MySQL', 'ok' => extension_loaded('pdo_mysql'), 'value' => extension_loaded('pdo_mysql') ? 'Yüklü' : 'Eksik'];
    $checks['json'] = ['label' => 'JSON Eklentisi', 'ok' => extension_loaded('json'), 'value' => extension_loaded('json') ? 'Yüklü' : 'Eksik'];
    $checks['session'] = ['label' => 'Session Desteği', 'ok' => extension_loaded('session'), 'value' => extension_loaded('session') ? 'Yüklü' : 'Eksik'];
    $checks['mbstring'] = ['label' => 'Mbstring', 'ok' => extension_loaded('mbstring'), 'value' => extension_loaded('mbstring') ? 'Yüklü' : 'Opsiyonel'];
    $checks['writable_root'] = ['label' => 'Kök Dizin Yazılabilir', 'ok' => is_writable(__DIR__), 'value' => is_writable(__DIR__) ? 'Evet' : 'Hayır'];
    $checks['writable_core'] = ['label' => 'core/ Dizini Yazılabilir', 'ok' => is_writable(__DIR__ . '/core'), 'value' => is_writable(__DIR__ . '/core') ? 'Evet' : 'Hayır'];
    $checks['mail'] = ['label' => 'Mail Fonksiyonu', 'ok' => function_exists('mail'), 'value' => function_exists('mail') ? 'Mevcut' : 'Opsiyonel'];
    $checks['mod_rewrite'] = ['label' => 'Apache mod_rewrite', 'ok' => (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) || true, 'value' => 'Kontrol edin'];
    return $checks;
}

// Adım 3: Veritabanı kurulumu
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['db_host'] ?? 'localhost');
    $db_name = trim($_POST['db_name'] ?? 'masterstudio_hotel');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = $_POST['db_pass'] ?? '';
    $admin_user = trim($_POST['admin_user'] ?? 'admin');
    $admin_pass = $_POST['admin_pass'] ?? '';
    $admin_email = trim($_POST['admin_email'] ?? '');
    $site_url = trim($_POST['site_url'] ?? '');

    if (empty($db_user) || empty($admin_user) || empty($admin_pass) || empty($admin_email)) {
        $error = 'Lütfen tüm zorunlu alanları doldurun.';
        $step = 2;
    } elseif (strlen($admin_pass) < 8) {
        $error = 'Admin şifresi en az 8 karakter olmalıdır.';
        $step = 2;
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi girin.';
        $step = 2;
    } else {
        // Veritabanı bağlantısı dene
        $conn = @new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            $error = 'Veritabanı bağlantısı başarısız: ' . $conn->connect_error;
            $step = 2;
        } else {
            // Veritabanını oluştur
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->select_db($db_name);
            $conn->set_charset("utf8mb4");

            // SQL dosyasını çalıştır
            $sql_file = __DIR__ . '/sql/database.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                // Satırları ayır ve çalıştır
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($statements as $stmt) {
                    if (!empty($stmt) && stripos($stmt, 'CREATE DATABASE') === false && stripos($stmt, 'USE ') === false) {
                        $conn->query($stmt);
                    }
                }
            }

            // Admin kullanıcısını sil ve yenisini ekle
            $conn->query("DELETE FROM users WHERE username = 'admin'");
            $hashed = password_hash($admin_pass, PASSWORD_BCRYPT);
            $stmt_ins = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
            $stmt_ins->bind_param("sss", $admin_user, $hashed, $admin_email);
            $stmt_ins->execute();
            $stmt_ins->close();

            // .env dosyasını oluştur
            $env_content = "DB_HOST=$db_host\n";
            $env_content .= "DB_USER=$db_user\n";
            $env_content .= "DB_PASS=$db_pass\n";
            $env_content .= "DB_NAME=$db_name\n";
            $env_content .= "PHP_ENV=production\n";
            $env_content .= "SITE_URL=$site_url\n";
            file_put_contents(__DIR__ . '/.env', $env_content);
            chmod(__DIR__ . '/.env', 0600);

            $success = 'Kurulum başarıyla tamamlandı!';
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterStudio Hotel - Kurulum Sihirbazı</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .installer { max-width: 700px; width: 100%; margin: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .installer-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .installer-header h1 { font-size: 28px; margin-bottom: 5px; }
        .installer-header p { opacity: 0.9; }
        .installer-body { padding: 30px; }
        .steps { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; }
        .step-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
        .step-dot.active { background: #667eea; color: white; }
        .step-dot.done { background: #28a745; color: white; }
        .step-dot.pending { background: #e0e0e0; color: #999; }
        .step-line { width: 40px; height: 2px; background: #e0e0e0; align-self: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; }
        .form-group input:focus { outline: none; border-color: #667eea; }
        .form-group small { color: #999; font-size: 12px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .btn { display: inline-block; padding: 14px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.3s; }
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; width: 100%; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-secondary { background: #f0f0f0; color: #333; }
        .check-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .check-ok { color: #28a745; font-weight: bold; }
        .check-fail { color: #e74c3c; font-weight: bold; }
        .check-warn { color: #ffc107; font-weight: bold; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .alert-success { background: #d4edda; color: #155724; }
        .section-title { font-size: 18px; font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #667eea; }
        .success-box { text-align: center; padding: 40px; }
        .success-box h2 { color: #28a745; font-size: 28px; margin-bottom: 15px; }
        .success-links { display: flex; gap: 15px; justify-content: center; margin-top: 25px; flex-wrap: wrap; }
        .success-links a { padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .warning-box { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: left; }
    </style>
</head>
<body>
<div class="installer">
    <div class="installer-header">
        <h1>MasterStudio Hotel</h1>
        <p>Kurulum Sihirbazı v2.0</p>
    </div>
    <div class="installer-body">
        <div class="steps">
            <div class="step-dot <?php echo $step >= 1 ? ($step > 1 ? 'done' : 'active') : 'pending'; ?>">1</div>
            <div class="step-line"></div>
            <div class="step-dot <?php echo $step >= 2 ? ($step > 2 ? 'done' : 'active') : 'pending'; ?>">2</div>
            <div class="step-line"></div>
            <div class="step-dot <?php echo $step >= 3 ? 'active' : 'pending'; ?>">3</div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <!-- ADIM 1: Hoş Geldiniz -->
            <div style="text-align: center;">
                <h2 style="margin-bottom: 15px;">Hoş Geldiniz!</h2>
                <p style="color: #666; margin-bottom: 25px; line-height: 1.8;">
                    MasterStudio Hotel yönetim sistemini kurmak üzeresiniz.<br>
                    Kurulum 3 basit adımda tamamlanacaktır.
                </p>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: left; margin-bottom: 25px;">
                    <p><strong>Gerekli Bilgiler:</strong></p>
                    <ul style="margin-left: 20px; margin-top: 10px; color: #555;">
                        <li>MySQL veritabanı bilgileri (host, kullanıcı, şifre)</li>
                        <li>Admin hesabı bilgileri</li>
                        <li>PHP 7.4+ ve MySQLi eklentisi</li>
                    </ul>
                </div>
                <a href="?step=2" class="btn btn-primary">Kuruluma Başla</a>
            </div>

        <?php elseif ($step === 2): ?>
            <!-- ADIM 2: Gereksinimler + Form -->
            <?php $checks = check_requirements(); $all_ok = true; ?>
            <div class="section-title">Sistem Gereksinimleri</div>
            <?php foreach ($checks as $check): ?>
                <div class="check-item">
                    <span><?php echo $check['label']; ?></span>
                    <span class="<?php echo $check['ok'] ? 'check-ok' : (strpos($check['value'], 'Opsiyonel') !== false ? 'check-warn' : 'check-fail'); ?>">
                        <?php echo htmlspecialchars($check['value']); ?>
                        <?php if (!$check['ok'] && strpos($check['value'], 'Opsiyonel') === false) $all_ok = false; ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <?php if ($all_ok): ?>
                <form method="POST" action="?step=3" style="margin-top: 30px;">
                    <div class="section-title">Veritabanı Ayarları</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Veritabanı Sunucusu *</label>
                            <input type="text" name="db_host" value="localhost" required>
                        </div>
                        <div class="form-group">
                            <label>Veritabanı Adı *</label>
                            <input type="text" name="db_name" value="masterstudio_hotel" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>DB Kullanıcı Adı *</label>
                            <input type="text" name="db_user" required placeholder="root veya kullanıcı adınız">
                        </div>
                        <div class="form-group">
                            <label>DB Şifresi</label>
                            <input type="password" name="db_pass" placeholder="Veritabanı şifresi">
                        </div>
                    </div>

                    <div class="section-title" style="margin-top: 25px;">Admin Hesabı</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Admin Kullanıcı Adı *</label>
                            <input type="text" name="admin_user" value="admin" required>
                        </div>
                        <div class="form-group">
                            <label>Admin E-posta *</label>
                            <input type="email" name="admin_email" required placeholder="admin@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Admin Şifresi * (min. 8 karakter)</label>
                        <input type="password" name="admin_pass" required minlength="8" placeholder="Güçlü bir şifre girin">
                    </div>

                    <div class="section-title" style="margin-top: 25px;">Site Ayarları</div>
                    <div class="form-group">
                        <label>Site URL (opsiyonel)</label>
                        <input type="url" name="site_url" placeholder="https://oteliniz.com">
                        <small>Boş bırakırsanız otomatik algılanır.</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Kurulumu Tamamla</button>
                </form>
            <?php else: ?>
                <div class="alert alert-danger" style="margin-top: 20px;">
                    Bazı gereksinimler karşılanmıyor. Lütfen eksikleri giderin ve sayfayı yenileyin.
                </div>
            <?php endif; ?>

        <?php elseif ($step === 3 && !empty($success)): ?>
            <!-- ADIM 3: Tamamlandı -->
            <div class="success-box">
                <div style="font-size: 60px; margin-bottom: 15px;">&#10003;</div>
                <h2>Kurulum Tamamlandı!</h2>
                <p style="color: #666; margin-bottom: 10px;">MasterStudio Hotel sisteminiz kullanıma hazır.</p>

                <div class="success-links">
                    <a href="index.php" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">Ana Sayfa</a>
                    <a href="admin/auth/login.php" style="background: #28a745; color: white;">Admin Paneli</a>
                </div>

                <div class="warning-box">
                    <strong>UYARI:</strong> Güvenliğiniz için <code>install.php</code> dosyasını sunucudan hemen siliniz!
                    <br><code>rm install.php</code>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
