<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';
$current_page = 'room-types';
$sidebar_base = '../';
$edit_type = null;

// Düzenleme modunda oda tipini al
$edit_id = intval($_GET['edit_id'] ?? 0);
if ($edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM room_types WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_type = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Yeni oda tipi ekle veya güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verify_csrf_token();

    $action = $_POST['action'];
    $name_tr = trim($_POST['name_tr'] ?? '');
    $name_en = trim($_POST['name_en'] ?? '');
    $description_tr = trim($_POST['description_tr'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $max_guests = intval($_POST['max_guests'] ?? 0);
    $price_per_night = floatval($_POST['price_per_night'] ?? 0);
    $amenities_tr = trim($_POST['amenities_tr'] ?? '');
    $amenities_en = trim($_POST['amenities_en'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name_tr)) {
        $error = 'Oda tipi adı (TR) gereklidir.';
    } elseif ($max_guests < 1 || $max_guests > 20) {
        $error = 'Misafir sayısı 1-20 arasında olmalıdır.';
    } elseif ($price_per_night <= 0) {
        $error = 'Geçerli bir fiyat giriniz.';
    } else {
        if ($action === 'edit' && $edit_id > 0) {
            $stmt = $conn->prepare("UPDATE room_types SET name_tr=?, name_en=?, description_tr=?, description_en=?, max_guests=?, price_per_night=?, amenities_tr=?, amenities_en=?, is_active=? WHERE id=?");
            $stmt->bind_param("ssssiissii", $name_tr, $name_en, $description_tr, $description_en, $max_guests, $price_per_night, $amenities_tr, $amenities_en, $is_active, $edit_id);
            if ($stmt->execute()) {
                $message = 'Oda tipi başarıyla güncellendi!';
                // Güncel veriyi al
                $stmt->close();
                $stmt2 = $conn->prepare("SELECT * FROM room_types WHERE id = ?");
                $stmt2->bind_param("i", $edit_id);
                $stmt2->execute();
                $edit_type = $stmt2->get_result()->fetch_assoc();
                $stmt2->close();
            } else {
                $error = 'Güncelleme sırasında hata oluştu.';
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO room_types (name_tr, name_en, description_tr, description_en, max_guests, price_per_night, amenities_tr, amenities_en, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssiissi", $name_tr, $name_en, $description_tr, $description_en, $max_guests, $price_per_night, $amenities_tr, $amenities_en, $is_active);
            if ($stmt->execute()) {
                $message = 'Oda tipi başarıyla eklendi!';
            } else {
                $error = 'Ekleme sırasında hata oluştu.';
            }
            $stmt->close();
        }
    }
}

// Oda tipi sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM rooms WHERE room_type_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $room_count = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();

    if ($room_count > 0) {
        $error = 'Bu oda tipine bağlı ' . $room_count . ' oda var. Önce odaları siliniz.';
    } else {
        $stmt = $conn->prepare("DELETE FROM room_types WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) { $message = 'Oda tipi başarıyla silindi!'; }
        else { $error = 'Silme sırasında hata oluştu.'; }
        $stmt->close();
    }
}

// Oda tiplerini al (oda sayısı ile birlikte)
$room_types = $conn->query("SELECT rt.*, COUNT(r.id) as room_count FROM room_types rt LEFT JOIN rooms r ON rt.id = r.room_type_id GROUP BY rt.id ORDER BY rt.id DESC");

set_security_headers();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oda Tipleri Yönetimi - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Oda Tipleri Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2><?php echo $edit_type ? 'Oda Tipini Düzenle' : 'Yeni Oda Tipi Ekle'; ?></h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="<?php echo $edit_type ? 'edit' : 'add'; ?>">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="name_tr">Oda Tipi Adı (TR): *</label>
                            <input type="text" id="name_tr" name="name_tr" required maxlength="100" value="<?php echo htmlspecialchars($edit_type['name_tr'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="name_en">Oda Tipi Adı (EN):</label>
                            <input type="text" id="name_en" name="name_en" maxlength="100" value="<?php echo htmlspecialchars($edit_type['name_en'] ?? ''); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="description_tr">Açıklama (TR):</label>
                            <textarea id="description_tr" name="description_tr" rows="3"><?php echo htmlspecialchars($edit_type['description_tr'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description_en">Açıklama (EN):</label>
                            <textarea id="description_en" name="description_en" rows="3"><?php echo htmlspecialchars($edit_type['description_en'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="max_guests">Max Misafir: *</label>
                            <input type="number" id="max_guests" name="max_guests" required min="1" max="20" value="<?php echo (int)($edit_type['max_guests'] ?? 2); ?>">
                        </div>
                        <div class="form-group">
                            <label for="price_per_night">Gece Fiyatı (TL): *</label>
                            <input type="number" id="price_per_night" name="price_per_night" step="0.01" required min="0.01" value="<?php echo htmlspecialchars($edit_type['price_per_night'] ?? ''); ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; padding-top: 25px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" <?php echo (!$edit_type || $edit_type['is_active']) ? 'checked' : ''; ?>>
                                Aktif
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="amenities_tr">Kolaylıklar (TR):</label>
                            <textarea id="amenities_tr" name="amenities_tr" rows="2" placeholder="WiFi, Klima, vb."><?php echo htmlspecialchars($edit_type['amenities_tr'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="amenities_en">Kolaylıklar (EN):</label>
                            <textarea id="amenities_en" name="amenities_en" rows="2" placeholder="WiFi, AC, etc."><?php echo htmlspecialchars($edit_type['amenities_en'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_type ? 'Güncelle' : 'Oda Tipi Ekle'; ?></button>
                        <?php if ($edit_type): ?>
                            <a href="room-types.php" class="btn" style="background: #999; color: white;">İptal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="list-section">
                <h2>Mevcut Oda Tipleri</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Adı (TR)</th>
                            <th>Adı (EN)</th>
                            <th>Max Misafir</th>
                            <th>Fiyat/Gece</th>
                            <th>Oda Sayısı</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($room_types && $room_types->num_rows > 0): ?>
                            <?php while ($row = $room_types->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['name_tr']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['name_en'] ?? ''); ?></td>
                                    <td><?php echo (int)$row['max_guests']; ?></td>
                                    <td><?php echo number_format($row['price_per_night'], 2); ?> TL</td>
                                    <td><?php echo (int)$row['room_count']; ?></td>
                                    <td>
                                        <span style="color: <?php echo $row['is_active'] ? '#155724' : '#721c24'; ?>; font-weight: 600;">
                                            <?php echo $row['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?edit_id=<?php echo (int)$row['id']; ?>" class="btn btn-small" style="background: #667eea; color: white; border: none;">Düzenle</a>
                                        <a href="?delete_id=<?php echo (int)$row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Bu oda tipini silmek istediğinizden emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center; padding: 20px;">Henüz oda tipi bulunmuyor.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
