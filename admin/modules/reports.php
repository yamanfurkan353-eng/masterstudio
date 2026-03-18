<?php
session_start();
require_once '../../core/config.php';
require_once '../../core/functions.php';
require_once '../includes/check-admin.php';

$current_page = 'reports';
$sidebar_base = '../';

// Tarih aralığı
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Genel istatistikler
$stats = $conn->query("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM reservations")->fetch_assoc();

// Tarih aralığı istatistikleri
$stmt = $conn->prepare("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed
    FROM reservations WHERE created_at BETWEEN ? AND ?");
$date_to_end = $date_to . ' 23:59:59';
$stmt->bind_param("ss", $date_from, $date_to_end);
$stmt->execute();
$period_stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Oda tipi bazında istatistikler
$room_stats = $conn->query("SELECT
    room_type,
    COUNT(*) as total_reservations,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM reservations GROUP BY room_type ORDER BY total_reservations DESC");

// Gelir tahmini (onaylı rezervasyonlar)
$revenue = $conn->query("SELECT
    r.room_type,
    COUNT(*) as bookings,
    DATEDIFF(r.check_out_date, r.check_in_date) as nights,
    rt.price_per_night,
    SUM(DATEDIFF(r.check_out_date, r.check_in_date) * rt.price_per_night) as revenue
    FROM reservations r
    LEFT JOIN room_types rt ON r.room_type = rt.name_tr
    WHERE r.status = 'confirmed'
    GROUP BY r.room_type, rt.price_per_night
    ORDER BY revenue DESC");

$total_revenue = 0;
$revenue_rows = [];
if ($revenue) {
    while ($row = $revenue->fetch_assoc()) {
        $revenue_rows[] = $row;
        $total_revenue += $row['revenue'] ?? 0;
    }
}

// Aylık trend (son 6 ay)
$monthly = $conn->query("SELECT
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed
    FROM reservations
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC");

$monthly_data = [];
if ($monthly) {
    while ($row = $monthly->fetch_assoc()) {
        $monthly_data[] = $row;
    }
}

// Oda doluluk
$total_rooms = $conn->query("SELECT COUNT(*) as c FROM rooms WHERE is_available = TRUE")->fetch_assoc()['c'];
$occupied_today = $conn->query("SELECT COUNT(DISTINCT r.room_type) as c FROM reservations r WHERE r.status = 'confirmed' AND r.check_in_date <= CURDATE() AND r.check_out_date > CURDATE()")->fetch_assoc()['c'];

// Popüler odalar
$popular = $conn->query("SELECT room_type, COUNT(*) as cnt FROM reservations WHERE status != 'cancelled' GROUP BY room_type ORDER BY cnt DESC LIMIT 5");

set_security_headers();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlar - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
    <style>
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .report-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; }
        .report-card h4 { color: #666; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; }
        .report-card .value { font-size: 32px; font-weight: 700; color: #667eea; }
        .report-card .sub { font-size: 12px; color: #999; margin-top: 5px; }
        .chart-bar { display: flex; align-items: center; gap: 10px; margin: 8px 0; }
        .chart-bar .bar { height: 24px; border-radius: 4px; background: linear-gradient(135deg, #667eea, #764ba2); transition: width 0.5s; min-width: 4px; }
        .chart-bar .label { min-width: 100px; font-size: 13px; }
        .chart-bar .amount { font-size: 13px; font-weight: 600; min-width: 60px; text-align: right; }
        .section-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .section-card h3 { margin-bottom: 15px; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 8px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <header class="top-bar">
                <h1>Raporlar ve Analitik</h1>
            </header>

            <div style="padding: 20px;">
                <!-- Genel İstatistikler -->
                <div class="report-grid">
                    <div class="report-card">
                        <h4>Toplam Rezervasyon</h4>
                        <div class="value"><?php echo (int)$stats['total']; ?></div>
                    </div>
                    <div class="report-card">
                        <h4>Onaylı</h4>
                        <div class="value" style="color: #28a745;"><?php echo (int)$stats['confirmed']; ?></div>
                        <div class="sub"><?php echo $stats['total'] > 0 ? round(($stats['confirmed'] / $stats['total']) * 100) : 0; ?>% onay oranı</div>
                    </div>
                    <div class="report-card">
                        <h4>Beklemede</h4>
                        <div class="value" style="color: #ffc107;"><?php echo (int)$stats['pending']; ?></div>
                    </div>
                    <div class="report-card">
                        <h4>İptal</h4>
                        <div class="value" style="color: #dc3545;"><?php echo (int)$stats['cancelled']; ?></div>
                        <div class="sub"><?php echo $stats['total'] > 0 ? round(($stats['cancelled'] / $stats['total']) * 100) : 0; ?>% iptal oranı</div>
                    </div>
                    <div class="report-card">
                        <h4>Tahmini Gelir</h4>
                        <div class="value" style="font-size: 24px;"><?php echo number_format($total_revenue, 2); ?> TL</div>
                        <div class="sub">Onaylı rezervasyonlar</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Oda Tipi Bazında -->
                    <div class="section-card">
                        <h3>Oda Tipi Bazında Rezervasyonlar</h3>
                        <?php
                        $max_count = 1;
                        $room_stat_rows = [];
                        if ($room_stats) {
                            while ($row = $room_stats->fetch_assoc()) {
                                $room_stat_rows[] = $row;
                                if ($row['total_reservations'] > $max_count) $max_count = $row['total_reservations'];
                            }
                        }
                        ?>
                        <?php foreach ($room_stat_rows as $row): ?>
                            <div class="chart-bar">
                                <div class="label"><?php echo htmlspecialchars($row['room_type']); ?></div>
                                <div class="bar" style="width: <?php echo ($row['total_reservations'] / $max_count) * 100; ?>%;"></div>
                                <div class="amount"><?php echo (int)$row['total_reservations']; ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($room_stat_rows)): ?>
                            <p style="color: #999; text-align: center;">Henüz veri yok.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Gelir Dağılımı -->
                    <div class="section-card">
                        <h3>Gelir Dağılımı (Onaylı)</h3>
                        <?php
                        $max_rev = 1;
                        foreach ($revenue_rows as $row) {
                            if (($row['revenue'] ?? 0) > $max_rev) $max_rev = $row['revenue'];
                        }
                        ?>
                        <?php foreach ($revenue_rows as $row): ?>
                            <div class="chart-bar">
                                <div class="label"><?php echo htmlspecialchars($row['room_type']); ?></div>
                                <div class="bar" style="width: <?php echo (($row['revenue'] ?? 0) / $max_rev) * 100; ?>%; background: linear-gradient(135deg, #28a745, #20c997);"></div>
                                <div class="amount"><?php echo number_format($row['revenue'] ?? 0, 0); ?> TL</div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($revenue_rows)): ?>
                            <p style="color: #999; text-align: center;">Henüz onaylı rezervasyon yok.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Aylık Trend -->
                <div class="section-card">
                    <h3>Aylık Rezervasyon Trendi (Son 6 Ay)</h3>
                    <?php if (!empty($monthly_data)): ?>
                        <?php
                        $max_monthly = 1;
                        foreach ($monthly_data as $m) { if ($m['total'] > $max_monthly) $max_monthly = $m['total']; }
                        $months_tr = ['01'=>'Oca','02'=>'Şub','03'=>'Mar','04'=>'Nis','05'=>'May','06'=>'Haz','07'=>'Tem','08'=>'Ağu','09'=>'Eyl','10'=>'Eki','11'=>'Kas','12'=>'Ara'];
                        ?>
                        <div style="display: flex; align-items: flex-end; gap: 15px; height: 200px; padding: 20px 0;">
                            <?php foreach ($monthly_data as $m):
                                $parts = explode('-', $m['month']);
                                $month_label = ($months_tr[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
                                $height = ($m['total'] / $max_monthly) * 160;
                                $confirmed_height = $m['total'] > 0 ? ($m['confirmed'] / $m['total']) * $height : 0;
                            ?>
                                <div style="flex: 1; text-align: center;">
                                    <div style="position: relative; height: 160px; display: flex; flex-direction: column; justify-content: flex-end;">
                                        <div style="background: rgba(102,126,234,0.2); border-radius: 4px 4px 0 0; height: <?php echo $height; ?>px; position: relative;">
                                            <div style="background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 4px 4px 0 0; height: <?php echo $confirmed_height; ?>px; position: absolute; bottom: 0; width: 100%;"></div>
                                        </div>
                                    </div>
                                    <div style="font-size: 11px; margin-top: 5px; color: #666;"><?php echo $month_label; ?></div>
                                    <div style="font-size: 12px; font-weight: 600;"><?php echo (int)$m['total']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="display: flex; gap: 20px; justify-content: center; font-size: 12px; color: #666; margin-top: 10px;">
                            <span><span style="display: inline-block; width: 12px; height: 12px; background: rgba(102,126,234,0.2); border-radius: 2px; vertical-align: middle;"></span> Toplam</span>
                            <span><span style="display: inline-block; width: 12px; height: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px; vertical-align: middle;"></span> Onaylı</span>
                        </div>
                    <?php else: ?>
                        <p style="color: #999; text-align: center; padding: 40px;">Henüz yeterli veri yok.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
