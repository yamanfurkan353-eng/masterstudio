<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';
$current_page = 'reservations';
$sidebar_base = '../';
$view_reservation = null;

// Sayfalama
$page_num = max(1, intval($_GET['page'] ?? 1));
$per_page = 15;
$offset = ($page_num - 1) * $per_page;

// Filtreler
$filter_status = $_GET['status'] ?? '';
$filter_search = trim($_GET['search'] ?? '');
$filter_date_from = $_GET['date_from'] ?? '';
$filter_date_to = $_GET['date_to'] ?? '';

// Detay görüntüleme
if (isset($_GET['view_id'])) {
    $view_id = intval($_GET['view_id']);
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $view_id);
    $stmt->execute();
    $view_reservation = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Durum güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token();
    $id = intval($_POST['id']);
    $status = $_POST['status'] ?? '';
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

// Not ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    verify_csrf_token();
    $id = intval($_POST['id']);
    $notes = trim($_POST['notes'] ?? '');
    $stmt = $conn->prepare("UPDATE reservations SET notes = ? WHERE id = ?");
    $stmt->bind_param("si", $notes, $id);
    if ($stmt->execute()) {
        $message = 'Not güncellendi!';
        // Detay sayfasını yenile
        $stmt2 = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $view_reservation = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();
    }
    $stmt->close();
}

// Rezervasyon sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) { $message = 'Rezervasyon silindi!'; }
    else { $error = 'Silme sırasında hata oluştu.'; }
    $stmt->close();
}

// Sorgu oluştur
$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_status) && in_array($filter_status, ['pending', 'confirmed', 'cancelled'])) {
    $where_clauses[] = "status = ?";
    $params[] = $filter_status;
    $types .= 's';
}
if (!empty($filter_search)) {
    $where_clauses[] = "(guest_name LIKE ? OR guest_email LIKE ? OR guest_phone LIKE ?)";
    $search_param = "%$filter_search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}
if (!empty($filter_date_from)) {
    $where_clauses[] = "check_in_date >= ?";
    $params[] = $filter_date_from;
    $types .= 's';
}
if (!empty($filter_date_to)) {
    $where_clauses[] = "check_out_date <= ?";
    $params[] = $filter_date_to;
    $types .= 's';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Toplam sayı
$count_sql = "SELECT COUNT(*) as total FROM reservations $where_sql";
if (!empty($params)) {
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_records = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
} else {
    $total_records = $conn->query($count_sql)->fetch_assoc()['total'];
}
$total_pages = max(1, ceil($total_records / $per_page));

// Veri çek
$data_sql = "SELECT * FROM reservations $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
if (!empty($params)) {
    $stmt = $conn->prepare($data_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $reservations = $stmt->get_result();
    $stmt->close();
} else {
    $reservations = $conn->query($data_sql);
}

set_security_headers();

$status_labels = ['pending' => 'Beklemede', 'confirmed' => 'Onaylı', 'cancelled' => 'İptal'];
$status_colors = ['pending' => '#856404', 'confirmed' => '#155724', 'cancelled' => '#721c24'];
$status_bgs = ['pending' => '#fff3cd', 'confirmed' => '#d4edda', 'cancelled' => '#f8d7da'];
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
                <h1>Rezervasyonlar <?php echo $view_reservation ? '- Detay' : ''; ?></h1>
                <div class="user-menu">
                    <span>Toplam: <?php echo $total_records; ?> kayıt</span>
                </div>
            </header>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success" style="margin: 20px;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="margin: 20px;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($view_reservation): ?>
            <!-- DETAY GÖRÜNÜMÜ -->
            <div class="form-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Rezervasyon #<?php echo (int)$view_reservation['id']; ?></h2>
                    <a href="reservations.php" class="btn btn-secondary" style="background: #999; color: white; border: none;">Listeye Dön</a>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <h3 style="margin-bottom: 15px; color: #667eea;">Misafir Bilgileri</h3>
                        <table style="width: 100%;">
                            <tr><td style="padding: 8px; font-weight: 600;">Ad Soyad:</td><td style="padding: 8px;"><?php echo htmlspecialchars($view_reservation['guest_name']); ?></td></tr>
                            <tr><td style="padding: 8px; font-weight: 600;">E-posta:</td><td style="padding: 8px;"><a href="mailto:<?php echo htmlspecialchars($view_reservation['guest_email']); ?>"><?php echo htmlspecialchars($view_reservation['guest_email']); ?></a></td></tr>
                            <tr><td style="padding: 8px; font-weight: 600;">Telefon:</td><td style="padding: 8px;"><?php echo htmlspecialchars($view_reservation['guest_phone'] ?? '-'); ?></td></tr>
                            <tr><td style="padding: 8px; font-weight: 600;">Misafir Sayısı:</td><td style="padding: 8px;"><?php echo (int)$view_reservation['num_guests']; ?></td></tr>
                        </table>
                    </div>
                    <div>
                        <h3 style="margin-bottom: 15px; color: #667eea;">Rezervasyon Bilgileri</h3>
                        <table style="width: 100%;">
                            <tr><td style="padding: 8px; font-weight: 600;">Oda Tipi:</td><td style="padding: 8px;"><?php echo htmlspecialchars($view_reservation['room_type']); ?></td></tr>
                            <tr><td style="padding: 8px; font-weight: 600;">Giriş:</td><td style="padding: 8px;"><?php echo htmlspecialchars($view_reservation['check_in_date']); ?></td></tr>
                            <tr><td style="padding: 8px; font-weight: 600;">Çıkış:</td><td style="padding: 8px;"><?php echo htmlspecialchars($view_reservation['check_out_date']); ?></td></tr>
                            <?php
                            $in = new DateTime($view_reservation['check_in_date']);
                            $out = new DateTime($view_reservation['check_out_date']);
                            $nights = $in->diff($out)->days;
                            ?>
                            <tr><td style="padding: 8px; font-weight: 600;">Gece Sayısı:</td><td style="padding: 8px;"><?php echo $nights; ?> gece</td></tr>
                            <tr><td style="padding: 8px; font-weight: 600;">Oluşturulma:</td><td style="padding: 8px;"><?php echo date('d.m.Y H:i', strtotime($view_reservation['created_at'])); ?></td></tr>
                        </table>
                    </div>
                </div>

                <!-- Durum Güncelleme -->
                <div style="margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="margin-bottom: 10px;">Durum</h3>
                    <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$view_reservation['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <?php foreach ($status_labels as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $view_reservation['status'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Güncelle</button>
                    </form>
                </div>

                <!-- Notlar -->
                <div style="margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="margin-bottom: 10px;">Notlar</h3>
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$view_reservation['id']; ?>">
                        <input type="hidden" name="add_note" value="1">
                        <textarea name="notes" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; margin-bottom: 10px;"><?php echo htmlspecialchars($view_reservation['notes'] ?? ''); ?></textarea>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Notu Kaydet</button>
                    </form>
                </div>
            </div>

            <?php else: ?>
            <!-- FİLTRE BÖLÜMÜ -->
            <div class="form-section">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                    <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                        <label>Arama</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($filter_search); ?>" placeholder="İsim, e-posta veya telefon...">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Durum</label>
                        <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Tümü</option>
                            <?php foreach ($status_labels as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $filter_status === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Tarih (Başlangıç)</label>
                        <input type="date" name="date_from" value="<?php echo htmlspecialchars($filter_date_from); ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Tarih (Bitiş)</label>
                        <input type="date" name="date_to" value="<?php echo htmlspecialchars($filter_date_to); ?>">
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filtrele</button>
                        <a href="reservations.php" class="btn" style="padding: 10px 20px; background: #e0e0e0;">Temizle</a>
                    </div>
                </form>
            </div>

            <!-- LİSTE -->
            <section class="list-section">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Misafir</th>
                            <th>E-posta</th>
                            <th>Giriş</th>
                            <th>Çıkış</th>
                            <th>Oda Tipi</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reservations && $reservations->num_rows > 0): ?>
                            <?php while ($row = $reservations->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo (int)$row['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['guest_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['guest_email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                    <td>
                                        <span style="padding: 4px 10px; border-radius: 3px; font-size: 12px; background: <?php echo $status_bgs[$row['status']] ?? '#eee'; ?>; color: <?php echo $status_colors[$row['status']] ?? '#333'; ?>;">
                                            <?php echo $status_labels[$row['status']] ?? $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?view_id=<?php echo (int)$row['id']; ?>" class="btn btn-small" style="background: #667eea; color: white; border: none;">Detay</a>
                                        <a href="?delete_id=<?php echo (int)$row['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Silmek istediğinizden emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center; padding: 30px; color: #999;">Kayıt bulunamadı.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- SAYFALAMA -->
                <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 5px; margin-top: 20px; flex-wrap: wrap;">
                    <?php
                    $query_params = $_GET;
                    unset($query_params['page']);
                    $base_url = 'reservations.php?' . http_build_query($query_params);
                    ?>
                    <?php if ($page_num > 1): ?>
                        <a href="<?php echo $base_url . '&page=' . ($page_num - 1); ?>" class="btn btn-small" style="background: #e0e0e0;">Önceki</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                        <a href="<?php echo $base_url . '&page=' . $i; ?>" class="btn btn-small" style="background: <?php echo $i === $page_num ? '#667eea' : '#e0e0e0'; ?>; color: <?php echo $i === $page_num ? 'white' : '#333'; ?>;"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page_num < $total_pages): ?>
                        <a href="<?php echo $base_url . '&page=' . ($page_num + 1); ?>" class="btn btn-small" style="background: #e0e0e0;">Sonraki</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
