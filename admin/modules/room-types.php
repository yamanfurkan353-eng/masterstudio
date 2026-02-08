<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';

// Yeni oda tipi ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name_tr = $_POST['name_tr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $description_tr = $_POST['description_tr'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $max_guests = $_POST['max_guests'] ?? 0;
    $price_per_night = $_POST['price_per_night'] ?? 0;
    $amenities_tr = $_POST['amenities_tr'] ?? '';
    $amenities_en = $_POST['amenities_en'] ?? '';

    if (!empty($name_tr) && !empty($max_guests) && !empty($price_per_night)) {
        $stmt = $conn->prepare("INSERT INTO room_types (name_tr, name_en, description_tr, description_en, max_guests, price_per_night, amenities_tr, amenities_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssidss", $name_tr, $name_en, $description_tr, $description_en, $max_guests, $price_per_night, $amenities_tr, $amenities_en);

        if ($stmt->execute()) {
            $message = 'Oda tipi başarıyla eklendi!';
        } else {
            $error = 'Erişim sırasında hata oluştu.';
        }
    } else {
        $error = 'Lütfen tüm gerekli alanları doldurunuz.';
    }
}

// Oda tipi sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM room_types WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = 'Oda tipi başarıyla silindi!';
    } else {
        $error = 'Silme sırasında hata oluştu.';
    }
}

// Oda tiplerini al
$room_types = $conn->query("SELECT * FROM room_types ORDER BY id DESC");
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
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>MasterStudio</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="reservations.php">Rezervasyonlar</a></li>
                    <li><a href="room-types.php" class="active">Oda Tipleri</a></li>
                    <li><a href="rooms.php">Odalar</a></li>
                    <li><a href="hotel-info.php">Otel Bilgileri</a></li>
                    <li><a href="pages.php">Sayfalar</a></li>
                    <li><a href="settings.php">Ayarlar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Oda Tipleri Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2>Yeni Oda Tipi Ekle</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="name_tr">Oda Tipi Adı (TR):</label>
                        <input type="text" id="name_tr" name="name_tr" required>
                    </div>

                    <div class="form-group">
                        <label for="name_en">Oda Tipi Adı (EN):</label>
                        <input type="text" id="name_en" name="name_en">
                    </div>

                    <div class="form-group">
                        <label for="description_tr">Açıklama (TR):</label>
                        <textarea id="description_tr" name="description_tr"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description_en">Açıklama (EN):</label>
                        <textarea id="description_en" name="description_en"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="max_guests">Maksimum Misafir Sayısı:</label>
                        <input type="number" id="max_guests" name="max_guests" required>
                    </div>

                    <div class="form-group">
                        <label for="price_per_night">Gece Fiyatı (₺):</label>
                        <input type="number" id="price_per_night" name="price_per_night" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="amenities_tr">Kolaylıklar (TR):</label>
                        <textarea id="amenities_tr" name="amenities_tr" placeholder="WiFi, Klima, vb. virgülle ayırarak yazınız"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="amenities_en">Kolaylıklar (EN):</label>
                        <textarea id="amenities_en" name="amenities_en" placeholder="WiFi, AC, etc. separated by comma"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Oda Tipi Ekle</button>
                </form>
            </section>

            <section class="list-section">
                <h2>Mevcut Oda Tipleri</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Adı (TR)</th>
                            <th>Adı (EN)</th>
                            <th>Max. Misafir</th>
                            <th>Fiyat/Gece</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $room_types->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name_tr']); ?></td>
                                <td><?php echo htmlspecialchars($row['name_en']); ?></td>
                                <td><?php echo $row['max_guests']; ?></td>
                                <td><?php echo number_format($row['price_per_night'], 2); ?> ₺</td>
                                <td><?php echo $row['is_active'] ? 'Aktif' : 'Pasif'; ?></td>
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
