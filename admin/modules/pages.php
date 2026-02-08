<?php
session_start();
require_once '../../core/config.php';
require_once '../includes/check-admin.php';

$message = '';
$error = '';
$edit_page = null;
$page_id = intval($_GET['id'] ?? 0);

// Düzenleme modunda sayfayı al
if ($page_id > 0) {
    $edit_page = $conn->query("SELECT * FROM pages WHERE id = $page_id")->fetch_assoc();
    if (!$edit_page) {
        $error = 'Sayfa bulunamadı.';
    }
}

// Yeni sayfa ekle veya güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $slug = strtolower(str_replace(' ', '-', $_POST['slug'] ?? ''));
    $title_tr = $_POST['title_tr'] ?? '';
    $title_en = $_POST['title_en'] ?? '';
    $content_tr = $_POST['content_tr'] ?? '';
    $content_en = $_POST['content_en'] ?? '';
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if (!empty($slug) && !empty($title_tr)) {
        if ($action === 'edit' && $page_id > 0) {
            // Güncelle
            $stmt = $conn->prepare("UPDATE pages SET slug=?, title_tr=?, title_en=?, content_tr=?, content_en=?, is_published=? WHERE id=?");
            $stmt->bind_param("sssssi", $slug, $title_tr, $title_en, $content_tr, $content_en, $is_published, $page_id);
            
            if ($stmt->execute()) {
                $message = 'Sayfa başarıyla güncellendi!';
                $edit_page = $conn->query("SELECT * FROM pages WHERE id = $page_id")->fetch_assoc();
            } else {
                $error = 'Güncelleme sırasında hata oluştu.';
            }
        } else {
            // Ekle
            $stmt = $conn->prepare("INSERT INTO pages (slug, title_tr, title_en, content_tr, content_en, is_published) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $slug, $title_tr, $title_en, $content_tr, $content_en, $is_published);

            if ($stmt->execute()) {
                $message = 'Sayfa başarıyla eklendi!';
                $_POST = array();
                $edit_page = null;
            } else {
                $error = 'Ekleme sırasında hata oluştu.';
            }
        }
    } else {
        $error = 'Lütfen gerekli alanları doldurunuz.';
    }
}

// Sayfa sil
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = 'Sayfa silindi!';
    } else {
        $error = 'Silme sırasında hata oluştu.';
    }
}

// Sayfaları al
$pages = $conn->query("SELECT * FROM pages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfalar - Admin Panel</title>
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
                    <li><a href="hotel-info.php">Otel Bilgileri</a></li>
                    <li><a href="pages.php" class="active">Sayfalar</a></li>
                    <li><a href="settings.php">Ayarlar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="top-bar">
                <h1>Sayfalar Yönetimi</h1>
            </header>

            <section class="form-section">
                <h2><?php echo $edit_page ? 'Sayfayı Düzenle' : 'Yeni Sayfa Oluştur'; ?></h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="form">
                    <input type="hidden" name="action" value="<?php echo $edit_page ? 'edit' : 'add'; ?>">
                    
                    <div class="form-group">
                        <label for="slug">Sayfa URL (Slug): *</label>
                        <input type="text" id="slug" name="slug" placeholder="hakkimizda" value="<?php echo htmlspecialchars($edit_page['slug'] ?? ''); ?>" required>
                        <small style="color: #999;">Otomatik oluşturulur. Alanları kendiniz doldurcaksınız eğer gerekli ise.</small>
                    </div>

                    <div class="form-group">
                        <label for="title_tr">Başlık (TR): *</label>
                        <input type="text" id="title_tr" name="title_tr" value="<?php echo htmlspecialchars($edit_page['title_tr'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="title_en">Başlık (EN):</label>
                        <input type="text" id="title_en" name="title_en" value="<?php echo htmlspecialchars($edit_page['title_en'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="content_tr">İçerik (TR): *</label>
                        <textarea id="content_tr" name="content_tr" rows="10" required><?php echo htmlspecialchars($edit_page['content_tr'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="content_en">İçerik (EN):</label>
                        <textarea id="content_en" name="content_en" rows="10"><?php echo htmlspecialchars($edit_page['content_en'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_published" value="1" <?php echo ($edit_page && $edit_page['is_published']) ? 'checked' : ''; ?>>
                            Yayında Yap
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_page ? 'Güncelle' : 'Sayfa Oluştur'; ?></button>
                        <?php if ($edit_page): ?>
                            <a href="pages.php" class="btn btn-secondary" style="background: #999; color: white; border: none;">İptal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="list-section">
                <h2>Mevcut Sayfalar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Başlık (TR)</th>
                            <th>Slug</th>
                            <th>Durum</th>
                            <th>Oluşturulma</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pages->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title_tr']); ?></td>
                                <td><code style="background: var(--light-bg); padding: 5px 10px; border-radius: 3px;"><?php echo htmlspecialchars($row['slug']); ?></code></td>
                                <td>
                                    <span style="background: <?php echo $row['is_published'] ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $row['is_published'] ? '#155724' : '#856404'; ?>; padding: 5px 10px; border-radius: 3px;">
                                        <?php echo $row['is_published'] ? '✓ Yayında' : '⊘ Taslak'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d.m.Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="?id=<?php echo $row['id']; ?>" class="btn btn-small" style="background: #667eea; color: white; border: none;">Düzenle</a>
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
