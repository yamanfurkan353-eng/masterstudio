<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';
$current_page = 'reservations';
$sidebar_base = '../';

// Durum güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token();

    $id = intval($_POST['id']);
    $status = $_POST['status'] ?? '';

    // Status whitelist kontrolü
    if (!in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $error = 'Geçersiz durum değeri.';
    } else {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            $message = 'Rezervasyon durumu güncellendi!';
        } else {
            $error = 'Güncelleme sırasında hata oluştu.';
        }
        $stmt->close();
    }
}

// Rezervasyon sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = 'Rezervasyon silindi!';
    } else {
        $error = 'Silme sırasında hata oluştu.';
    }
    $stmt->close();
}

// Rezervasyonları al
$reservations = $conn->query("SELECT * FROM reservations ORDER BY created_at DESC");

set_security_headers();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyonlar - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Rezervasyonlar</h1>
            </header>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <section class="list-section">
                <table>
                    <thead>
                        <tr>
                            <th>Misafir Adı</th>
                            <th>E-posta</th>
                            <th>Telefon</th>
                            <th>Giriş Tarihi</th>
                            <th>Çıkış Tarihi</th>
                            <th>Oda Tipi</th>
                            <th>Misafir</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reservations && $reservations->num_rows > 0): ?>
                            <?php while ($row = $reservations->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['guest_email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['guest_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                    <td><?php echo (int)$row['num_guests']; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="pending" <?php echo ($row['status'] === 'pending') ? 'selected' : ''; ?>>Beklemede</option>
                                                <option value="confirmed" <?php echo ($row['status'] === 'confirmed') ? 'selected' : ''; ?>>Onaylanan</option>
                                                <option value="cancelled" <?php echo ($row['status'] === 'cancelled') ? 'selected' : ''; ?>>İptal</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="?delete_id=<?php echo (int)$row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9" style="text-align: center; padding: 20px;">Henüz rezervasyon bulunmuyor.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
