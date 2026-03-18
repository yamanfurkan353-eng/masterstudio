<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';
require_once 'includes/check-admin.php';

$current_page = 'dashboard';
$sidebar_base = '';

// Dashboard istatistikleri - tek optimize sorgu
$stats_query = $conn->query("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM reservations");
$stats = $stats_query->fetch_assoc();

// Son rezervasyonları al
$reservations = $conn->query("SELECT * FROM reservations ORDER BY created_at DESC LIMIT 5");

// Oda istatistikleri
$room_stats = $conn->query("SELECT COUNT(*) as total_rooms, SUM(CASE WHEN is_available THEN 1 ELSE 0 END) as available FROM rooms");
$room_info = $room_stats ? $room_stats->fetch_assoc() : ['total_rooms' => 0, 'available' => 0];

set_security_headers();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MasterStudio</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Dashboard</h1>
                <div class="user-menu">
                    <span><?php echo htmlspecialchars($admin_username); ?></span>
                    <a href="profile.php" style="margin-left: 20px; color: #667eea; cursor: pointer;">Profil</a>
                </div>
            </header>

            <section class="dashboard">
                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>Toplam Rezervasyon</h3>
                        <p class="stat-number"><?php echo (int)$stats['total']; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Beklemede</h3>
                        <p class="stat-number" style="color: #856404;"><?php echo (int)$stats['pending']; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Onaylanan</h3>
                        <p class="stat-number" style="color: #155724;"><?php echo (int)$stats['confirmed']; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Toplam Oda</h3>
                        <p class="stat-number"><?php echo (int)$room_info['total_rooms']; ?></p>
                        <small style="color: #666;"><?php echo (int)$room_info['available']; ?> müsait</small>
                    </div>
                </div>

                <section class="recent-reservations">
                    <h2>Son Rezervasyonlar</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Misafir Adı</th>
                                <th>E-posta</th>
                                <th>Giriş Tarihi</th>
                                <th>Oda Tipi</th>
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
                                        <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                        <td>
                                            <span style="padding: 3px 8px; border-radius: 3px; font-size: 12px;
                                                background: <?php echo $row['status'] === 'confirmed' ? '#d4edda' : ($row['status'] === 'cancelled' ? '#f8d7da' : '#fff3cd'); ?>;
                                                color: <?php echo $row['status'] === 'confirmed' ? '#155724' : ($row['status'] === 'cancelled' ? '#721c24' : '#856404'); ?>;">
                                                <?php
                                                    $status_labels = ['pending' => 'Beklemede', 'confirmed' => 'Onaylı', 'cancelled' => 'İptal'];
                                                    echo $status_labels[$row['status']] ?? $row['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="modules/reservations.php" class="btn btn-small">Görüntüle</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align: center; padding: 20px;">Henüz rezervasyon bulunmuyor.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
