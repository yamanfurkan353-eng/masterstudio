<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';
require_once 'includes/check-admin.php';

$message = '';
$error = '';
$current_page = 'profile';
$sidebar_base = '';

// Geçerli kullanıcı bilgilerini al - prepared statement
$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Profil güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verify_csrf_token();

    $action = $_POST['action'];

    if ($action === 'update_profile') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $error = 'E-posta alanı boş olamaz.';
        } elseif (!validate_email($email)) {
            $error = 'Geçerli bir e-posta adresi giriniz.';
        } else {
            // E-posta benzersizlik kontrolü
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $admin_id = $_SESSION['admin_id'];
            $stmt->bind_param("si", $email, $admin_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.';
            } else {
                $stmt->close();
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->bind_param("si", $email, $admin_id);

                if ($stmt->execute()) {
                    $message = 'Profil başarıyla güncellendi!';
                    $user['email'] = $email;
                } else {
                    $error = 'Güncelleme sırasında hata oluştu.';
                }
            }
            $stmt->close();
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Mevcut şifreyi kontrol et - prepared statement
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $admin_id = $_SESSION['admin_id'];
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!password_verify($current_password, $user_data['password'])) {
            $error = 'Mevcut şifre yanlış.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Yeni şifreler eşleşmiyor.';
        } elseif (strlen($new_password) < 8) {
            $error = 'Yeni şifre en az 8 karakter olmalıdır.';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $admin_id);

            if ($stmt->execute()) {
                $message = 'Şifre başarıyla değiştirildi!';
            } else {
                $error = 'Şifre değiştirilirken hata oluştu.';
            }
            $stmt->close();
        }
    }
}

set_security_headers();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Ayarları - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

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
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Profil Güncelleme -->
                <form method="POST" class="form" style="margin-bottom: 40px;">
                    <?php echo csrf_field(); ?>
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
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label for="current_password">Mevcut Şifre:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Yeni Şifre:</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                        <small style="color: #999;">En az 8 karakter olmalıdır.</small>
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
