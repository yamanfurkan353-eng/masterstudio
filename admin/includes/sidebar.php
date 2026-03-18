<aside class="sidebar">
    <div class="sidebar-header">
        <h2>MasterStudio</h2>
    </div>
    <nav>
        <ul>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>index.php" <?php echo ($current_page ?? '') === 'dashboard' ? 'class="active"' : ''; ?>>Dashboard</a></li>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/reservations.php" <?php echo ($current_page ?? '') === 'reservations' ? 'class="active"' : ''; ?>>Rezervasyonlar</a></li>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/room-types.php" <?php echo ($current_page ?? '') === 'room-types' ? 'class="active"' : ''; ?>>Oda Tipleri</a></li>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/rooms.php" <?php echo ($current_page ?? '') === 'rooms' ? 'class="active"' : ''; ?>>Odalar</a></li>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/hotel-info.php" <?php echo ($current_page ?? '') === 'hotel-info' ? 'class="active"' : ''; ?>>Otel Bilgileri</a></li>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/pages.php" <?php echo ($current_page ?? '') === 'pages' ? 'class="active"' : ''; ?>>Sayfalar</a></li>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/settings.php" <?php echo ($current_page ?? '') === 'settings' ? 'class="active"' : ''; ?>>Ayarlar</a></li>
            <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
            <li><a href="<?php echo $sidebar_base ?? ''; ?>modules/users.php" <?php echo ($current_page ?? '') === 'users' ? 'class="active"' : ''; ?>>Kullanicılar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <p><?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?></p>
        <a href="<?php echo $sidebar_base ?? ''; ?>profile.php" style="color: #667eea; text-decoration: none; font-size: 13px;">Profil</a>
        <a href="<?php echo $sidebar_base ?? ''; ?>logout.php" class="btn btn-danger" style="margin-top: 5px;">Çıkış Yap</a>
    </div>
</aside>
