<?php
session_start();
require_once '../../core/config.php';
require_once 'includes/check-admin.php';

// Son rezervasyonları al
$reservations = $conn->query("SELECT * FROM reservations ORDER BY created_at DESC LIMIT 5");
$total_reservations = $conn->query("SELECT COUNT(*) as count FROM reservations")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MasterStudio</title>
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
                    <li><a href="index.php" class="active">Dashboard</a></li>
                    <li><a href="modules/reservations.php">Rezervasyonlar</a></li>
                    <li><a href="modules/room-types.php">Oda Tipleri</a></li>
                    <li><a href="modules/rooms.php">Odalar</a></li>
                    <li><a href="modules/hotel-info.php">Otel Bilgileri</a></li>
                    <li><a href="modules/pages.php">Sayfalar</a></li>
                    <li><a href="modules/settings.php">Ayarlar</a></li>
                    <li><a href="modules/users.php">Kullanıcılar</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p><?php echo htmlspecialchars($admin_username); ?></p>
                <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
            </div>
        </aside>

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
                        <p class="stat-number"><?php echo $total_reservations; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Beklemede</h3>
                        <p class="stat-number"><?php echo $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status='pending'")->fetch_assoc()['count']; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Onaylanan</h3>
                        <p class="stat-number"><?php echo $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status='confirmed'")->fetch_assoc()['count']; ?></p>
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
                            <?php while ($row = $reservations->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['guest_email']); ?></td>
                                    <td><?php echo $row['check_in_date']; ?></td>
                                    <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td>
                                        <a href="modules/reservations.php?id=<?php echo $row['id']; ?>" class="btn btn-small">Görüntüle</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
