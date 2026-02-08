<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';

// Yeni oda ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $room_number = $_POST['room_number'] ?? '';
    $room_type_id = intval($_POST['room_type_id'] ?? 0);
    $floor = intval($_POST['floor'] ?? 0);

    if (!empty($room_number) && $room_type_id > 0) {
        $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type_id, floor) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $room_number, $room_type_id, $floor);

        if ($stmt->execute()) {
            $message = 'Oda başarıyla eklendi!';
        } else {
            $error = 'Ekleme sırasında hata oluştu.';
        }
    } else {
        $error = 'Lütfen tüm gerekli alanları doldurunuz.';
    }
}

// Oda sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = 'Oda silindi!';
    } else {
        $error = 'Silme sırasında hata oluştu.';
    }
}

// Odaları al
$rooms = $conn->query("SELECT r.*, rt.name_tr FROM rooms r LEFT JOIN room_types rt ON r.room_type_id = rt.id ORDER BY r.room_number ASC");
$room_types_list = $conn->query("SELECT id, name_tr FROM room_types WHERE is_active = TRUE");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odalar - Admin Panel</title>
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
                    <li><a href="rooms.php" class="active">Odalar</a></li>
                    <li><a href="hotel-info.php">Otel Bilgileri</a></li>
                    <li><a href="pages.php">Sayfalar</a></li>
                    <li><a href="settings.php">Ayarlar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Odalar Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2>Yeni Oda Ekle</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="room_number">Oda Numarası:</label>
                        <input type="text" id="room_number" name="room_number" placeholder="101" required>
                    </div>

                    <div class="form-group">
                        <label for="room_type_id">Oda Tipi:</label>
                        <select id="room_type_id" name="room_type_id" required>
                            <option value="">Seçiniz...</option>
                            <?php 
                            $room_types_list->data_seek(0);
                            while ($rt = $room_types_list->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $rt['id']; ?>"><?php echo htmlspecialchars($rt['name_tr']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="floor">Kat:</label>
                        <input type="number" id="floor" name="floor" placeholder="1">
                    </div>

                    <button type="submit" class="btn btn-primary">Oda Ekle</button>
                </form>
            </section>

            <section class="list-section">
                <h2>Mevcut Odalar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Oda No</th>
                            <th>Oda Tipi</th>
                            <th>Kat</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $rooms->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['name_tr']); ?></td>
                                <td><?php echo $row['floor']; ?></td>
                                <td><?php echo $row['is_available'] ? 'Müsait' : 'Dolu'; ?></td>
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
