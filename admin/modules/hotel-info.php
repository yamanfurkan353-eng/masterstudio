<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';

// Mevcut otel bilgilerini al
$hotel = $conn->query("SELECT * FROM hotel_info LIMIT 1")->fetch_assoc();

// Otel bilgilerini güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_tr = $_POST['name_tr'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $description_tr = $_POST['description_tr'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $address_tr = $_POST['address_tr'] ?? '';
    $address_en = $_POST['address_en'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $check_in_time = $_POST['check_in_time'] ?? '14:00:00';
    $check_out_time = $_POST['check_out_time'] ?? '11:00:00';
    $star_rating = intval($_POST['star_rating'] ?? 5);

    if (!empty($name_tr)) {
        if ($hotel) {
            // Güncelle
            $stmt = $conn->prepare("UPDATE hotel_info SET name_tr=?, name_en=?, description_tr=?, description_en=?, address_tr=?, address_en=?, phone=?, email=?, check_in_time=?, check_out_time=?, star_rating=? WHERE id=?");
            $id = $hotel['id'];
            $stmt->bind_param("ssssssssssii", $name_tr, $name_en, $description_tr, $description_en, $address_tr, $address_en, $phone, $email, $check_in_time, $check_out_time, $star_rating, $id);
        } else {
            // Ekle
            $stmt = $conn->prepare("INSERT INTO hotel_info (name_tr, name_en, description_tr, description_en, address_tr, address_en, phone, email, check_in_time, check_out_time, star_rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssi", $name_tr, $name_en, $description_tr, $description_en, $address_tr, $address_en, $phone, $email, $check_in_time, $check_out_time, $star_rating);
        }

        if ($stmt->execute()) {
            $message = 'Otel bilgileri başarıyla güncellendi!';
            // Yeni verileri al
            $hotel = $conn->query("SELECT * FROM hotel_info LIMIT 1")->fetch_assoc();
        } else {
            $error = 'Güncelleme sırasında hata oluştu.';
        }
    } else {
        $error = 'Lütfen otel adını giriniz.';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otel Bilgileri - Admin Panel</title>
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
                    <li><a href="hotel-info.php" class="active">Otel Bilgileri</a></li>
                    <li><a href="pages.php">Sayfalar</a></li>
                    <li><a href="settings.php">Ayarlar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Otel Bilgileri Yönetimi</h1>
            </header>

            <section class="form-section">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <div class="form-group">
                        <label for="name_tr">Otel Adı (TR):</label>
                        <input type="text" id="name_tr" name="name_tr" value="<?php echo htmlspecialchars($hotel['name_tr'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="name_en">Otel Adı (EN):</label>
                        <input type="text" id="name_en" name="name_en" value="<?php echo htmlspecialchars($hotel['name_en'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description_tr">Açıklama (TR):</label>
                        <textarea id="description_tr" name="description_tr"><?php echo htmlspecialchars($hotel['description_tr'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description_en">Açıklama (EN):</label>
                        <textarea id="description_en" name="description_en"><?php echo htmlspecialchars($hotel['description_en'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="address_tr">Adres (TR):</label>
                        <input type="text" id="address_tr" name="address_tr" value="<?php echo htmlspecialchars($hotel['address_tr'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="address_en">Adres (EN):</label>
                        <input type="text" id="address_en" name="address_en" value="<?php echo htmlspecialchars($hotel['address_en'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($hotel['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">E-posta:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($hotel['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="check_in_time">Giriş Saati:</label>
                        <input type="time" id="check_in_time" name="check_in_time" value="<?php echo $hotel['check_in_time'] ?? '14:00:00'; ?>">
                    </div>

                    <div class="form-group">
                        <label for="check_out_time">Çıkış Saati:</label>
                        <input type="time" id="check_out_time" name="check_out_time" value="<?php echo $hotel['check_out_time'] ?? '11:00:00'; ?>">
                    </div>

                    <div class="form-group">
                        <label for="star_rating">Yıldız Derecelendirmesi:</label>
                        <select id="star_rating" name="star_rating">
                            <option value="1" <?php echo (($hotel['star_rating'] ?? 5) == 1) ? 'selected' : ''; ?>>1 Yıldız</option>
                            <option value="2" <?php echo (($hotel['star_rating'] ?? 5) == 2) ? 'selected' : ''; ?>>2 Yıldız</option>
                            <option value="3" <?php echo (($hotel['star_rating'] ?? 5) == 3) ? 'selected' : ''; ?>>3 Yıldız</option>
                            <option value="4" <?php echo (($hotel['star_rating'] ?? 5) == 4) ? 'selected' : ''; ?>>4 Yıldız</option>
                            <option value="5" <?php echo (($hotel['star_rating'] ?? 5) == 5) ? 'selected' : ''; ?>>5 Yıldız</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
