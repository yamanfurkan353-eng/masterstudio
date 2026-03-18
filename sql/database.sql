-- Veritabanı Adı: masterstudio_hotel
-- Karakter seti: utf8mb4

CREATE DATABASE IF NOT EXISTS `masterstudio_hotel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `masterstudio_hotel`;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `role` ENUM('admin', 'editor') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Oda tipleri tablosu
CREATE TABLE IF NOT EXISTS `room_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name_tr` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100),
    `description_tr` TEXT,
    `description_en` TEXT,
    `max_guests` INT(11) NOT NULL,
    `price_per_night` DECIMAL(10, 2) NOT NULL,
    `amenities_tr` TEXT,
    `amenities_en` TEXT,
    `image_url` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_room_types_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Odalar tablosu
CREATE TABLE IF NOT EXISTS `rooms` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `room_number` VARCHAR(10) NOT NULL UNIQUE,
    `room_type_id` INT(11) NOT NULL,
    `floor` INT(11) DEFAULT 0,
    `is_available` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_rooms_type_id` (`room_type_id`),
    INDEX `idx_rooms_available` (`is_available`),
    FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rezervasyonlar tablosu
CREATE TABLE IF NOT EXISTS `reservations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `guest_name` VARCHAR(100) NOT NULL,
    `guest_email` VARCHAR(100) NOT NULL,
    `guest_phone` VARCHAR(20),
    `room_type` VARCHAR(50) NOT NULL,
    `room_id` INT(11) DEFAULT NULL,
    `check_in_date` DATE NOT NULL,
    `check_out_date` DATE NOT NULL,
    `num_guests` INT(11) NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_reservations_email` (`guest_email`),
    INDEX `idx_reservations_status` (`status`),
    INDEX `idx_reservations_dates` (`check_in_date`, `check_out_date`),
    INDEX `idx_reservations_room_type` (`room_type`),
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sayfalar tablosu
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `title_tr` VARCHAR(255) NOT NULL,
    `title_en` VARCHAR(255) DEFAULT '',
    `content_tr` TEXT,
    `content_en` TEXT,
    `is_published` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pages_slug` (`slug`),
    INDEX `idx_pages_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ayarlar tablosu
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    PRIMARY KEY (`id`),
    INDEX `idx_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Otel bilgileri tablosu
CREATE TABLE IF NOT EXISTS `hotel_info` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name_tr` VARCHAR(255) NOT NULL,
    `name_en` VARCHAR(255),
    `description_tr` TEXT,
    `description_en` TEXT,
    `address_tr` VARCHAR(255),
    `address_en` VARCHAR(255),
    `phone` VARCHAR(20),
    `email` VARCHAR(100),
    `check_in_time` TIME DEFAULT '14:00:00',
    `check_out_time` TIME DEFAULT '11:00:00',
    `star_rating` INT(11) DEFAULT 5,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Denetim günlüğü tablosu (yeni)
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `table_name` VARCHAR(50) NOT NULL,
    `record_id` INT(11),
    `details` TEXT,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_date` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek Admin Kullanıcısı (Şifre: admin123)
-- NOT: Gerçek projelerde güvenli bir şifre kullanılmalıdır!
INSERT INTO `users` (`username`, `password`, `email`, `role`) VALUES
('admin', '$2y$10$tJ.qG.j.D5n.G7v.0y0b.u.Q7j.Q7j.Q7j.Q7j.Q7j.Q7j.Q7j.Q7j.Q7j', 'admin@example.com', 'admin')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Örnek Otel Bilgileri
INSERT INTO `hotel_info` (`name_tr`, `name_en`, `description_tr`, `description_en`, `address_tr`, `phone`, `email`, `star_rating`) VALUES
('MasterStudio Otel', 'MasterStudio Hotel', 'Lüks ve konforlu konaklama deneyimi', 'Luxury and comfortable accommodation experience', 'İstanbul, Türkiye', '+90 212 XXXXXXX', 'info@masterstudio.com', 5)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Örnek Oda Tipleri
INSERT INTO `room_types` (`name_tr`, `name_en`, `description_tr`, `description_en`, `max_guests`, `price_per_night`, `amenities_tr`, `amenities_en`) VALUES
('Standart Oda', 'Standard Room', 'Rahat ve ferah standart oda', 'Comfortable and spacious standard room', 2, 150.00, 'WiFi, Klima, Banyo', 'WiFi, AC, Bathroom'),
('Deluxe Oda', 'Deluxe Room', 'Lüks amenities ile donanmış oda', 'Room equipped with luxury amenities', 2, 250.00, 'WiFi, Klima, Banyo, Balkon', 'WiFi, AC, Bathroom, Balcony'),
('Süit', 'Suite', 'Oturma alanı ve yatak odasıyla birlikte', 'Suite with living area and bedroom', 4, 450.00, 'WiFi, Klima, Banyo, Jakuzi, Oturma Alanı', 'WiFi, AC, Bathroom, Jacuzzi, Living Area')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Örnek Odalar
INSERT INTO `rooms` (`room_number`, `room_type_id`, `floor`) VALUES
('101', 1, 1), ('102', 1, 1), ('103', 1, 1),
('201', 2, 2), ('202', 2, 2),
('301', 3, 3)
ON DUPLICATE KEY UPDATE `id` = `id`;
