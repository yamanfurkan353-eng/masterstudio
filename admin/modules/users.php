<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

// Sadece admin istifadəçilər erişebilsin
if ($_SESSION['admin_role'] !== 'admin') {
    die('Bu sayfaya erişim yetkiniz yoktur.');
}

$message = '';
$error = '';

// Kullanıcı ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'editor';

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Kullanıcı adı kontrol et
        $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($check->num_rows > 0) {
            $error = 'Bu kullanıcı adı zaten kullanılıyor.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $message = 'Kullanıcı başarıyla eklendi!';
            } else {
                $error = 'Ekleme sırasında hata oluştu.';
            }
        }
    } else {
        $error = 'Lütfen tüm alanları doldurunuz.';
    }
}

// Kullanıcı sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Kendi hesabını silemesin
    if ($delete_id === $_SESSION['admin_id']) {
        $error = 'Kendi hesabınızı silemezsiniz!';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $message = 'Kullanıcı silindi!';
        } else {
            $error = 'Silme sırasında hata oluştu.';
        }
    }
}

// Kullanıcıları al
$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
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
                    <li><a href="users.php" class="active">Kullanıcılar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Kullanıcı Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2>Yeni Kullanıcı Ekle</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="username">Kullanıcı Adı:</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-posta:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Şifre:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Rol:</label>
                        <select id="role" name="role">
                            <option value="editor">Editör</option>
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
                                <td><?php echo htmlspecialchars($row['username']); ?><?php echo ($row['id'] === $_SESSION['admin_id']) ? ' <span style="color: #667eea; font-size: 12px;">(SİZ)</span>' : ''; ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span style="background: <?php echo $row['role'] === 'admin' ? '#667eea' : '#764ba2'; ?>; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px;">
                                        <?php echo $row['role'] === 'admin' ? 'Admin' : 'Editör'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d.m.Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if ($row['id'] !== $_SESSION['admin_id']): ?>
                                        <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?')">Sil</a>
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
