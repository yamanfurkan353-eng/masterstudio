<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

// Sadece admin kullanıcılar erişebilsin
if ($_SESSION['admin_role'] !== 'admin') {
    die('Bu sayfaya erişim yetkiniz yoktur.');
}

$message = '';
$error = '';
$current_page = 'users';
$sidebar_base = '../';

// Kullanıcı ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    verify_csrf_token();

    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['admin', 'editor']) ? $_POST['role'] : 'editor';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurunuz.';
    } elseif (!validate_email($email)) {
        $error = 'Geçerli bir e-posta adresi giriniz.';
    } elseif (strlen($password) < 8) {
        $error = 'Şifre en az 8 karakter olmalıdır.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Kullanıcı adı 3-50 karakter arasında olmalıdır.';
    } else {
        // Kullanıcı adı kontrol et - prepared statement
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $check = $stmt->get_result();
        $stmt->close();

        if ($check->num_rows > 0) {
            $error = 'Bu kullanıcı adı zaten kullanılıyor.';
        } else {
            // E-posta kontrol et
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $email_check = $stmt->get_result();
            $stmt->close();

            if ($email_check->num_rows > 0) {
                $error = 'Bu e-posta adresi zaten kullanılıyor.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

                if ($stmt->execute()) {
                    $message = 'Kullanıcı başarıyla eklendi!';
                } else {
                    $error = 'Ekleme sırasında hata oluştu.';
                }
                $stmt->close();
            }
        }
    }
}

// Kullanıcı sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    if ($delete_id === (int)$_SESSION['admin_id']) {
        $error = 'Kendi hesabınızı silemezsiniz!';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $message = 'Kullanıcı silindi!';
        } else {
            $error = 'Silme sırasında hata oluştu.';
        }
        $stmt->close();
    }
}

// Kullanıcıları al
$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");

set_security_headers();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Kullanıcı Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2>Yeni Kullanıcı Ekle</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="add">

                    <div class="form-group">
                        <label for="username">Kullanıcı Adı:</label>
                        <input type="text" id="username" name="username" required minlength="3" maxlength="50">
                    </div>

                    <div class="form-group">
                        <label for="email">E-posta:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Şifre:</label>
                        <input type="password" id="password" name="password" required minlength="8">
                        <small style="color: #999;">En az 8 karakter olmalıdır.</small>
                    </div>

                    <div class="form-group">
                        <label for="role">Rol:</label>
                        <select id="role" name="role">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Kullanıcı Ekle</button>
                </form>
            </section>

            <section class="list-section">
                <h2>Mevcut Kullanıcılar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Kullanıcı Adı</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Katılma Tarihi</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?><?php echo ((int)$row['id'] === (int)$_SESSION['admin_id']) ? ' <span style="color: #667eea; font-size: 12px;">(SİZ)</span>' : ''; ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span style="background: <?php echo $row['role'] === 'admin' ? '#667eea' : '#764ba2'; ?>; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px;">
                                        <?php echo $row['role'] === 'admin' ? 'Admin' : 'Editor'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d.m.Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if ((int)$row['id'] !== (int)$_SESSION['admin_id']): ?>
                                        <a href="?delete_id=<?php echo (int)$row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?')">Sil</a>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
