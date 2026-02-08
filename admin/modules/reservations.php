<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';

// Durum güncelle
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        $message = 'Rezervasyon durumu güncellendi!';
    } else {
        $error = 'Güncelleme sırasında hata oluştu.';
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
}

// Rezervasyonları al
$reservations = $conn->query("SELECT * FROM reservations ORDER BY created_at DESC");
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
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>MasterStudio</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="reservations.php" class="active">Rezervasyonlar</a></li>
                    <li><a href="room-types.php">Oda Tipleri</a></li>
                    <li><a href="rooms.php">Odalar</a></li>
                    <li><a href="hotel-info.php">Otel Bilgileri</a></li>
                    <li><a href="pages.php">Sayfalar</a></li>
                    <li><a href="settings.php">Ayarlar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Rezervasyonlar</h1>
            </header>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
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
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['guest_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['guest_phone']); ?></td>
                                <td><?php echo $row['check_in_date']; ?></td>
                                <td><?php echo $row['check_out_date']; ?></td>
                                <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="pending" <?php echo ($row['status'] === 'pending') ? 'selected' : ''; ?>>Beklemede</option>
                                            <option value="confirmed" <?php echo ($row['status'] === 'confirmed') ? 'selected' : ''; ?>>Onaylanan</option>
                                            <option value="cancelled" <?php echo ($row['status'] === 'cancelled') ? 'selected' : ''; ?>>İptal</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?')">Sil</a>
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
