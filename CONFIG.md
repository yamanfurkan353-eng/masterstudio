# 🔧 Yapılandırma Rehberi

Bu dokuman, MasterStudio Hotel'ı kendi ortamınız için yapılandırmak hakkında ayrıntılı bilgi sağlar.

## 📋 İçerik
1. [Veritabanı Yapılandırması](#veritabanı-yapılandırması)
2. [PHP Yapılandırması](#php-yapılandırması)
3. [Apache Yapılandırması](#apache-yapılandırması)
4. [Docker Yapılandırması](#docker-yapılandırması)
5. [Ortam Değişkenleri](#ortam-değişkenleri)
6. [SSL/HTTPS](#sslaşağıhttps)
7. [Email Yapılandırması](#email-yapılandırması)
8. [Logging ve Monitoring](#logging-ve-monitoring)

---

## 💾 Veritabanı Yapılandırması

### MySQL Başlangıç

#### Veritabanı ve Kullanıcı Oluşturma

```sql
-- Veritabanı oluştur
CREATE DATABASE masterstudio_hotel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Admin kullanıcı oluştur
CREATE USER 'hotel_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Tüm yetkileri ver
GRANT ALL PRIVILEGES ON masterstudio_hotel.* TO 'hotel_user'@'localhost';

-- Backup kullanıcısı (read-only)
CREATE USER 'hotel_backup'@'localhost' IDENTIFIED BY 'backup_password';
GRANT SELECT ON masterstudio_hotel.* TO 'hotel_backup'@'localhost';

-- Değişiklikleri uygula
FLUSH PRIVILEGES;
```

#### Veritabanı Şemasını İçe Aktarma

```bash
# Komut satırı ile
mysql -u hotel_user -p masterstudio_hotel < sql/database.sql

# phpMyAdmin ile
# 1. phpMyAdmin'e giriş yapın
# 2. masterstudio_hotel veritabanını seçin
# 3. "Import" sekmesine gidin
# 4. sql/database.sql dosyasını seçin
# 5. "Go" butonuna tıklayın
```

### Con Tablolar ve İlişkiler

```
masterstudio_hotel/
├── users (admin/editor hesapları)
├── hotel_info (otel bilgileri)
├── room_types (oda tipleri)
├── rooms (oda kayıtları)
├── reservations (rezervasyonlar)
├── pages (CMS sayfaları)
└── settings (sistem ayarları)
```

---

## 🐘 PHP Yapılandırması

### core/config.php

Bu dosya tüm veritabanı bağlantı bilgilerini içerir.

```php
<?php
// Veritabanı Bilgileri
define('DB_HOST', 'localhost');         // Veritabanı sunucusu
define('DB_USER', 'hotel_user');        // Veritabanı kullanıcısı
define('DB_PASS', 'strong_password');   // Veritabanı şifresi
define('DB_NAME', 'masterstudio_hotel'); // Veritabanı adı

// Site Ayarları
define('SITE_NAME', 'MasterStudio Hotel');
define('SITE_URL', 'http://localhost');

// Güvenlik
define('SESSION_TIMEOUT', 3600); // 1 saat

// Tema ve Dil
define('DEFAULT_THEME', 'light'); // light/dark
define('DEFAULT_LANG', 'tr');     // tr/en
?>
```

### PHP.ini Ayarları

Üretim ortamı için önerilen ayarlar:

```ini
; /etc/php/8.2/apache2/php.ini

; Güvenlik
display_errors = Off
log_errors = On
error_reporting = E_ALL
error_log = /var/log/php/error.log

; Session
session.cookie_httponly = 1
session.cookie_secure = 1         ; HTTPS için
session.cookie_samesite = Strict
session.gc_maxlifetime = 3600

; File Upload
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20

; Memory ve eksekücyon
memory_limit = 256M
max_execution_time = 30
max_input_time = 60

; Tehlikeli fonksiyonları devre dışı bırak
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source
```

---

## 🪶 Apache Yapılandırması

### Virtual Host Konfigürasyonu

```apache
# /etc/apache2/sites-available/masterstudio.conf

<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com
    ServerAdmin admin@example.com
    
    # Dokument kökü
    DocumentRoot /var/www/html/masterstudio
    
    # Error ve Access logları
    ErrorLog ${APACHE_LOG_DIR}/masterstudio_error.log
    CustomLog ${APACHE_LOG_DIR}/masterstudio_access.log combined
    
    <Directory /var/www/html/masterstudio>
        # Temel İzinler
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        # URL Rewriting
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            
            # Index dosyası
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php [L,QSA]
        </IfModule>
        
        # Güvenlik başlıkları
        <IfModule mod_headers.c>
            Header set X-Content-Type-Options "nosniff"
            Header set X-Frame-Options "SAMEORIGIN"
            Header set X-XSS-Protection "1; mode=block"
        </IfModule>
    </Directory>
    
    # Gizli dosyaları engelle
    <FilesMatch "^\.">
        Order allow,deny
        Deny from all
    </FilesMatch>
    
    # Index listemesini devre dışı bırak
    <Directory /var/www/html/masterstudio/uploads>
        Options -Indexes
    </Directory>
</VirtualHost>

# HTTPS yönlendirmesi (HTTP'den HTTPS'ye)
<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com
    RewriteEngine On
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>
```

### Modülleri Etkinleştirme

```bash
# Required modülleri aktifleştir
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod ssl
sudo a2enmod php8.2

# Sites'ı aktifleştir
sudo a2ensite masterstudio.conf

# Eski sitesini devre dışı bırak (isteğe bağlı)
sudo a2dissite 000-default.conf

# Konfigürasyonu kontrol et
sudo apache2ctl configtest
# Açık çıkmalı: Syntax OK

# Apache'yi yeniden başlat
sudo systemctl restart apache2
```

---

## 🐳 Docker Yapılandırması

### docker-compose.yml Özelleştirmesi

```yaml
version: '3.8'

services:
  # MySQL Veritabanı
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: masterstudio_hotel
      MYSQL_USER: hotel_user
      MYSQL_PASSWORD: hotel_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./sql:/docker-entrypoint-initdb.d
    networks:
      - masterstudio_network

  # PHP-Apache
  php:
    build: .
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www/html
      - ./apache.conf:/etc/apache2/sites-available/000-default.conf
    depends_on:
      - mysql
    networks:
      - masterstudio_network
    environment:
      MYSQL_HOST: mysql
      MYSQL_USER: hotel_user
      MYSQL_PASSWORD: hotel_password

  # phpMyAdmin
  phpmyadmin:
    image: phpmyadmin
    environment:
      PMA_HOST: mysql
      PMA_USER: hotel_user
      PMA_PASSWORD: hotel_password
    ports:
      - "8080:80"
    depends_on:
      - mysql
    networks:
      - masterstudio_network

volumes:
  mysql_data:

networks:
  masterstudio_network:
    driver: bridge
```

### Docker Override (Geliştirme)

```yaml
# docker-compose.override.yml (git'te takip edilmez)

version: '3.8'

services:
  php:
    environment:
      PHP_DISPLAY_ERRORS: 1
      PHP_LOG_ERRORS: 0
    ports:
      - "8000:80"
```

---

## 🔐 Ortam Değişkenleri

### .env Dosyası

```bash
# .env örneği

# Veritabanı
DB_HOST=localhost
DB_USER=hotel_user
DB_PASS=strong_password
DB_NAME=masterstudio_hotel

# Site Bilgileri
SITE_NAME="MasterStudio Hotel"
SITE_URL="https://example.com"
ADMIN_EMAIL="admin@example.com"

# SMTP (Email için)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
SMTP_FROM="noreply@example.com"

# Güvenlik
SECRET_KEY="your-secret-key-here"
API_KEY="your-api-key-here"

# Mode
APP_ENV=production
APP_DEBUG=false

# Tarih ve Zaman
TIMEZONE="Europe/Istanbul"
```

### Ortam Değişkenlerini Yükleme (PHP)

```php
<?php
// core/config.php

// .env dosyasını yükle
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Kullanma
$site_name = getenv('SITE_NAME');
$db_host = getenv('DB_HOST');
?>
```

---

## 🔒 SSL/HTTPS

### Let's Encrypt ile

```bash
# Certbot yükle
sudo apt install certbot python3-certbot-apache

# Sertifikat al
sudo certbot --apache -d example.com -d www.example.com

# Otomatik yenileme
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# Sertifikaları kontrol et
sudo certbot certificates

# Manuel yenileme
sudo certbot renew --dry-run
```

### Self-Signed Sertifikat (Geliştirme)

```bash
# Sertifikat oluştur (10 yıl geçerli)
sudo openssl req -x509 -nodes -days 3650 \
  -newkey rsa:2048 \
  -keyout /etc/ssl/private/masterstudio.key \
  -out /etc/ssl/certs/masterstudio.crt

# Apache'de kullan
# /etc/apache2/sites-available/masterstudio-ssl.conf

<VirtualHost *:443>
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/masterstudio.crt
    SSLCertificateKeyFile /etc/ssl/private/masterstudio.key
    # ... rest of config
</VirtualHost>
```

---

## 📧 Email Yapılandırması

### SMTP Wrapper (PHP)

```php
<?php
// core/mail.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // SMTP Ayarları
        $this->mailer->isSMTP();
        $this->mailer->Host = getenv('SMTP_HOST');
        $this->mailer->Port = getenv('SMTP_PORT');
        $this->mailer->Username = getenv('SMTP_USER');
        $this->mailer->Password = getenv('SMTP_PASS');
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPSecure = 'tls';
    }
    
    public function sendReservationConfirmation($email, $guest_name) {
        try {
            $this->mailer->setFrom(getenv('SMTP_FROM'), 'MasterStudio Hotel');
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Rezervasyon Onayı';
            
            // HTML içerik
            $this->mailer->isHTML(true);
            $this->mailer->Body = "<h1>Resepsiyon Onaylandı</h1>";
            $this->mailer->Body .= "<p>Merhaba $guest_name,<br>";
            $this->mailer->Body .= "Rezervasyonunuz başarıyla kaydedilmiştir.</p>";
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail hatası: " . $e->getMessage());
            return false;
        }
    }
}
?>
```

---

## 📊 Logging ve Monitoring

### PHP Error Logging

```php
<?php
// core/logger.php

class Logger {
    private static $log_file = '/var/log/masterstudio/app.log';
    
    public static function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $context_str = !empty($context) ? json_encode($context) : '';
        $log_message = "[$timestamp] [$level] $message $context_str\n";
        
        error_log($log_message, 3, self::$log_file);
    }
    
    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }
    
    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }
}

// Kullanım
Logger::info('User login', ['user_id' => 123]);
Logger::error('Database connection failed', ['host' => 'localhost']);
?>
```

### Apache Loglarını İzle

```bash
# Real-time error log
tail -f /var/log/apache2/masterstudio_error.log

# Access loglarını analiz et
tail -f /var/log/apache2/masterstudio_access.log

# 404 hatalarını bul
grep " 404 " /var/log/apache2/masterstudio_access.log

# En çok erişilen sayfalar
awk '{print $7}' /var/log/apache2/masterstudio_access.log | sort | uniq -c | sort -rn
```

---

## 🔍 Sistem Kontrol Listesi

Deployment yapmadan önce kontrol edin:

- [ ] Veritabanı oluşturuldu ve şema yüklendi
- [ ] config.php veritabanı bilgileri güncelleştirildi
- [ ] .env dosyası oluşturuldu (git'e dahil değil)
- [ ] Dosya izinleri doğru (644 dosyalar, 755 dizinler)
- [ ] Apache modülleri etkinleştirildi (rewrite, headers, ssl)
- [ ] SSL/TLS sertifikası kurulu
- [ ] Email yapılandırması test edildi
- [ ] Veritabanı backup scripti kurulu
- [ ] Log dosya dizini oluşturuldu
- [ ] uptaste ve PHP güncel
- [ ] Firewall kuralları uyarlanmış
- [ ] Error log'lar kaydedilecek şekilde konfigüre edilmiş

---

## 📞 Sorun Giderme

### Veritabanı Erişim Hatası

```bash
# MySQL'e bağlan
mysql -u hotel_user -p masterstudio_hotel

# Kullanıcı yetkilerini kontrol et
SHOW GRANTS FOR 'hotel_user'@'localhost';

# Veritabanı var mı kontrol et
SHOW DATABASES;
```

### Apache Modül Hatası

```bash
# Modülleri kontrol et
apache2ctl -M | grep rewrite

# Modülü etkinleştir
sudo a2enmod rewrite

# Syntax'ı doğrula
apache2ctl configtest
```

### PHP Uzantısı Eksik

```bash
# Kurulu uzantıları kontrol et
php -m | grep mysql

# Eksik ise yükle
sudo apt install php8.2-mysql
```

---

Son güncelleme: Şubat 2026
