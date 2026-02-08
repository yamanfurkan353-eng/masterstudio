<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';

// Geçerli kullanıcı bilgilerini al
$user = $conn->query("SELECT id, username, email FROM users WHERE id = {$_SESSION['admin_id']}")->fetch_assoc();

// Profil güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_profile') {
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));

        if (!empty($email)) {
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $id = $_SESSION['admin_id'];
            $stmt->bind_param("si", $email, $id);

            if ($stmt->execute()) {
                $message = 'Profil başarıyla güncellendi!';
                $user = $conn->query("SELECT id, username, email FROM users WHERE id = {$_SESSION['admin_id']}")->fetch_assoc();
            } else {
                $error = 'Güncelleme sırasında hata oluştu.';
            }
        } else {
            $error = 'E-posta alanı boş olamaz.';
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Mevcut şifreyi kontrol et
        $user_data = $conn->query("SELECT password FROM users WHERE id = {$_SESSION['admin_id']}")->fetch_assoc();

        if (!password_verify($current_password, $user_data['password'])) {
            $error = 'Mevcut şifre yanlış.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Yeni şifreler eşleşmiyor.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Yeni şifre en az 6 karakter olmalıdır.';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $id = $_SESSION['admin_id'];
            $stmt->bind_param("si", $hashed_password, $id);

            if ($stmt->execute()) {
                $message = 'Şifre başarıyla değiştirildi!';
            } else {
                $error = 'Şifre değiştirilirken hata oluştu.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Ayarları - Admin Panel</title>
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
                    <li><a href="settings.php">Ayarlar</a></li>
                    <li><a href="users.php">Kullanıcılar</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                <a href="../logout.php" class="btn btn-danger">Çıkış Yap</a>
            </div>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Profil Ayarları</h1>
                <div class="user-menu">
                    <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </header>

            <section class="form-section">
                <h2>Profil Bilgilerim</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Profil Güncelleme -->
                <form method="POST" class="form" style="margin-bottom: 40px;">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">
                        <label for="username">Kullanıcı Adı (Değiştirilemez):</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="email">E-posta Adresi:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Profili Güncelle</button>
                </form>

                <hr style="border: none; border-top: 2px solid var(--border-color); margin: 40px 0;">

                <h2>Şifre Değiştir</h2>

                <!-- Şifre Değiştirme -->
                <form method="POST" class="form">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label for="current_password">Mevcut Şifre:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Yeni Şifre:</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <small style="color: #999;">En az 6 karakter olmalıdır.</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Yeni Şifreyi Onayla:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Şifreyi Değiştir</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
