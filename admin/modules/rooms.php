<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';
$current_page = 'rooms';
$sidebar_base = '../';

// Yeni oda ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    verify_csrf_token();

    $room_number = sanitize_input($_POST['room_number'] ?? '');
    $room_type_id = intval($_POST['room_type_id'] ?? 0);
    $floor = intval($_POST['floor'] ?? 0);

    if (empty($room_number)) {
        $error = 'Oda numarası gereklidir.';
    } elseif ($room_type_id <= 0) {
        $error = 'Lütfen geçerli bir oda tipi seçiniz.';
    } elseif ($floor < 0 || $floor > 100) {
        $error = 'Geçerli bir kat numarası giriniz.';
    } else {
        // Oda numarası benzersizlik kontrolü
        $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_number = ?");
        $stmt->bind_param("s", $room_number);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Bu oda numarası zaten kayıtlı.';
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type_id, floor) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $room_number, $room_type_id, $floor);

            if ($stmt->execute()) {
                $message = 'Oda başarıyla eklendi!';
            } else {
                $error = 'Ekleme sırasında hata oluştu.';
            }
        }
        $stmt->close();
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
    $stmt->close();
}

// Odaları al
$rooms = $conn->query("SELECT r.*, rt.name_tr FROM rooms r LEFT JOIN room_types rt ON r.room_type_id = rt.id ORDER BY r.room_number ASC");
$room_types_list = $conn->query("SELECT id, name_tr FROM room_types WHERE is_active = TRUE");

set_security_headers();
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
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Odalar Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2>Yeni Oda Ekle</h2>

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
                        <label for="room_number">Oda Numarası:</label>
                        <input type="text" id="room_number" name="room_number" placeholder="101" required maxlength="10">
                    </div>

                    <div class="form-group">
                        <label for="room_type_id">Oda Tipi:</label>
                        <select id="room_type_id" name="room_type_id" required>
                            <option value="">Seçiniz...</option>
                            <?php
                            if ($room_types_list) {
                                $room_types_list->data_seek(0);
                                while ($rt = $room_types_list->fetch_assoc()):
                            ?>
                                <option value="<?php echo (int)$rt['id']; ?>"><?php echo htmlspecialchars($rt['name_tr']); ?></option>
                            <?php
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="floor">Kat:</label>
                        <input type="number" id="floor" name="floor" placeholder="1" min="0" max="100">
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
                        <?php if ($rooms && $rooms->num_rows > 0): ?>
                            <?php while ($row = $rooms->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name_tr'] ?? 'Bilinmiyor'); ?></td>
                                    <td><?php echo (int)$row['floor']; ?></td>
                                    <td>
                                        <span style="color: <?php echo $row['is_available'] ? '#155724' : '#721c24'; ?>;">
                                            <?php echo $row['is_available'] ? 'Müsait' : 'Dolu'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?delete_id=<?php echo (int)$row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align: center; padding: 20px;">Henüz oda bulunmuyor.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
