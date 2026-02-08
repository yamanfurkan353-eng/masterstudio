# 🏨 MasterStudio Hotel - Detaylı Kurulum Talimatı

Bu dokuman, MasterStudio Hotel sistemini kendi sunucunuza veya bilgisayarınıza kurmak için adım adım talimatları içerir.

## 📋 İçerik
1. [Gereksinimler](#gereksinimler)
2. [Docker ile Kurulum (Kolay)](#docker-ile-kurulum-kolay)
3. [Manuel Kurulum (VDS/VPS)](#manuel-kurulum-vdsvps)
4. [Windows'ta Kurulum](#windowsta-kurulum)
5. [Linux/Ubuntu Kurulum](#linuxubuntu-kurulum)
6. [Veritabanı Yönetimi](#veritabanı-yönetimi)
7. [Sorun Giderme](#sorun-giderme)
8. [İlk Kurulum Sonrası](#ilk-kurulum-sonrası)

---

## ⚙️ Gereksinimler

### Minimum Sistem Gereksinimleri
- **İşlemci:** 1 GHz veya daha hızlı
- **RAM:** 512 MB (Docker ile 1GB önerilir)
- **Disk:** 500 MB boş alan
- **İnternet:** Kütüphaneleri indirmek için

### Docker ile Kurulum Gereksinimleri
- Docker Desktop (Windows/Mac) veya Docker Engine (Linux)
- Docker Compose
- 20GB disk alanı

### Manuel Kurulum Gereksinimleri
- **PHP:** 8.2 veya üzeri
- **MySQL:** 8.0 veya üzeri
- **Apache:** mod_rewrite etkinleştirilmiş
- **Git:** Projeyi indirmek için

---

## 🐳 Docker ile Kurulum (Kolay)

En hızlı ve güvenli kurulum yöntemidir. Tüm bağımlılıklar otomatik yüklenir.

### 1. Adım: Docker İndirme ve Yükleme

#### Windows/Mac
1. https://www.docker.com/products/docker-desktop adresine gidin
2. Docker Desktop'ı indirin ve yükleyin
3. Bilgisayarı yeniden başlatın

#### Ubuntu/Debian
```bash
# Docker yükleme
curl -fsSL https://get.docker.com | sudo sh

# Kullanıcıyı docker grubuna ekle
sudo usermod -aG docker $USER

# Logout ve login yapın veya
newgrp docker
```

#### Fedora/RHEL
```bash
sudo dnf install docker-compose-docker
sudo systemctl start docker
sudo usermod -aG docker $USER
```

### 2. Adım: Projeyi İndirme

```bash
# Terminalinizi açın/PowerShell

# Projeyi klonla (veya zip olarak indir)
git clone https://github.com/yamanfurkan353-eng/masterstudio.git

# Klasöre gir
cd masterstudio

# Windows'ta PowerShell kullanıyorsanız
# Aynı komutlar çalışır
```

### 3. Adım: Docker Konteynerlerini Başlat

```bash
# Konteynerları başlat (arka planda)
docker-compose up -d

# Durumunu kontrol et
docker-compose ps

# Logları görmek için
docker-compose logs -f
```

### 4. Adım: Veritabanını Oluştur

```bash
# SQL dosyasını çalıştır (Otomatik olur, ama emin olmak için)
docker-compose exec mysql mysql -u root -p"root_password" masterstudio_hotel < sql/database.sql

# Veya phpMyAdmin'den manuel olarak
# http://localhost:8080 adresine git
# User: hotel_user
# Pass: hotel_password
# SQL dosyasını yapıştır
```

### 5. Adım: Sitey Erişim

- **Ön Yüz:** http://localhost
- **Admin:** http://localhost/admin/auth/login.php
- **phpMyAdmin:** http://localhost:8080

### Durdurma/Yeniden Başlatma

```bash
# Durdur
docker-compose down

# Yeniden başlat
docker-compose restart

# Verileri sil (DİKKAT!)
docker-compose down -v
```

---

## 💻 Manuel Kurulum (VDS/VPS)

Linux sunucusunda direkt kurulum.

### 1. Adım: Gereklileri Yükleme

#### Ubuntu/Debian
```bash
# Sistem güncellemesi
sudo apt update && sudo apt upgrade -y

# PHP ve gerekli uzantıları
sudo apt install -y php8.2 php8.2-mysql php8.2-gd php8.2-curl php8.2-xml

# MySQL Server
sudo apt install -y mysql-server

# Apache
sudo apt install -y apache2 libapache2-mod-php8.2

# Git
sudo apt install -y git

# Mod Rewrite aktifleştirme
sudo a2enmod rewrite

# Apache yeniden başlat
sudo systemctl restart apache2
```

#### CentOS/RHEL
```bash
# PHP 8.2 repository
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-$(rpm -E '%{rhel}').rpm

# PHP ve uzantılar
sudo dnf install -y php82 php82-php-mysql php82-php-gd

# MySQL
sudo dnf install -y mysql-server

# Apache
sudo dnf install -y httpd

# WebServer başlat
sudo systemctl start httpd
```

### 2. Adım: Projeyi İndirme

```bash
# Web sunucusu dizinine git
cd /var/www/html

# Projeyi klonla
sudo git clone https://github.com/yamanfurkan353-eng/masterstudio.git

# İzinleri ayarla
sudo chown -R www-data:www-data /var/www/html/masterstudio
sudo chmod -R 755 /var/www/html/masterstudio
```

### 3. Adım: Veritabanını Oluşturma

```bash
# MySQL'e gir
sudo mysql -u root

# İçinde:
CREATE DATABASE masterstudio_hotel;
CREATE USER 'hotel_user'@'localhost' IDENTIFIED BY 'hotel_password';
GRANT ALL PRIVILEGES ON masterstudio_hotel.* TO 'hotel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# SQL dosyasını çalıştır
mysql -u hotel_user -p masterstudio_hotel < /var/www/html/masterstudio/sql/database.sql
# Şifre sor: hotel_password yazın
```

### 4. Adım: Apache Yapılandırması

```bash
# Yeni VirtualHost oluştur
sudo nano /etc/apache2/sites-available/masterstudio.conf
```

Aşağıdaki içeriği yapıştırın:
```apache
<VirtualHost *:80>
    ServerAdmin admin@example.com
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    
    DocumentRoot /var/www/html/masterstudio

    <Directory /var/www/html/masterstudio>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted

        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            RewriteRule ^index\.php$ - [L]
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule . /index.php [L]
        </IfModule>
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/masterstudio_error.log
    CustomLog ${APACHE_LOG_DIR}/masterstudio_access.log combined
</VirtualHost>
```

Kaydet: `Ctrl+X` → `Y` → `Enter`

```bash
# Siteyi etkinleştir
sudo a2ensite masterstudio.conf

# Eski siteyi devre dışı bırak (isteğe bağlı)
sudo a2dissite 000-default.conf

# Yapılandırmayı kontrol et
sudo apache2ctl configtest

# Apache yeniden başlat
sudo systemctl restart apache2
```

### 5. Adım: SSL Sertifikası (HTTPS)

```bash
# Let's Encrypt ve Certbot yükleme
sudo apt install -y certbot python3-certbot-apache

# Sertifikat al ve apache otomatik yapılandırması
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Otomatik yenileme
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

### 6. Adım: config.php Güncelleme

```bash
# Yapılandırma dosyasını düzenle
sudo nano /var/www/html/masterstudio/core/config.php
```

Aşağıdaki kısımları güncelleyin:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'hotel_user');
define('DB_PASS', 'hotel_password');
define('DB_NAME', 'masterstudio_hotel');
```

---

## 🪟 Windows'ta Kurulum

XAMPP veya WAMP kullanarak Windows'ta kurulum.

### XAMPP ile Kurulum

#### 1. XAMPP İndirme
https://www.apachefriends.org/ adresinden indirin

#### 2. Yükleme
- XAMPP installer'ını çalıştırın
- Apache ve MySQL seçeneklerini işaretleyin

#### 3. XAMPP Başlatma
- XAMPP Control Panel'i açın
- Apache ve MySQL'i "Start" butonuyla başlatın

#### 4. Projeyi Kopyalama
```
C:\xampp\htdocs\masterstudio klasörü oluşturun
Projeyi buraya kopyalayın
```

#### 5. Veritabanı Oluşturma
- http://localhost/phpmyadmin adresine gidin
- Yeni bir veritabanı oluşturun: `masterstudio_hotel`
- SQL dosyasını import edin

#### 6. Erişim
- http://localhost/masterstudio

---

## 🐧 Linux/Ubuntu Kurulum

Komple otomasyonlu kurulum scripti:

```bash
#!/bin/bash

# Kurulum Scripti
set -e

echo "MasterStudio Hotel Kurulumu Başlanıyor..."

# Paket güncellemesi
sudo apt update && sudo apt upgrade -y

# Gerekli paketler
sudo apt install -y \
    curl \
    git \
    php8.2 \
    php8.2-mysql \
    php8.2-gd \
    php8.2-curl \
    php8.2-xml \
    mysql-server \
    apache2 \
    libapache2-mod-php8.2

# Mod rewrite ve other mods
sudo a2enmod rewrite
sudo a2enmod php8.2

# MySQL hizmetini başlat
sudo systemctl start mysql
sudo systemctl enable mysql

# Apache hizmetini başlat
sudo systemctl start apache2
sudo systemctl enable apache2

# Projeyi indir
cd /tmp
git clone https://github.com/yamanfurkan353-eng/masterstudio.git

# Web sunucu dizinine kopyala
sudo cp -r masterstudio /var/www/html/masterstudio

# İzinleri ayarla
sudo chown -R www-data:www-data /var/www/html/masterstudio
sudo chmod -R 755 /var/www/html/masterstudio

echo "Kurulum Tamamlandı!"
echo "Admin: http://localhost/masterstudio/admin/auth/login.php"
echo "Kullanıcı: admin"
echo "Şifre: admin123"
```

Bu scripti kaydedin ve çalıştırın:
```bash
chmod +x kurulum.sh
sudo ./kurulum.sh
```

---

## 💾 Veritabanı Yönetimi

### Backup Alma

```bash
# Docker ile
docker-compose exec mysql mysqldump -u root -p"root_password" masterstudio_hotel > backup_$(date +%Y%m%d_%H%M%S).sql

# Manuel sunucuda
mysqldump -u hotel_user -p masterstudio_hotel > backup.sql
# Şifre girin: hotel_password
```

### Backup Geri Yükleme

```bash
# Docker ile
docker-compose exec -T mysql mysql -u root -p"root_password" masterstudio_hotel < backup.sql

# Manuel sunucuda
mysql -u hotel_user -p masterstudio_hotel < backup.sql
```

### Veritabanını Sıfırlama

```bash
# Docker ile
docker-compose exec mysql mysql -u root -p"root_password" -e "DROP DATABASE masterstudio_hotel;"
docker-compose exec mysql mysql -u root -p"root_password" masterstudio_hotel < /var/www/html/sql/database.sql
```

---

## 🐛 Sorun Giderme

### Hata: "Veritabanı Bağlantısı Başarısız"

**Çözüm:**
```bash
# 1. MySQL çalışıyor mu kontrol et
docker-compose ps mysql

# 2. config.php dosyasını kontrol et
cat core/config.php

# 3. MySQL loglarını kontrol et
docker-compose logs mysql
```

### Hata: "403 Forbidden"

**Çözüm:**
```bash
# İzinleri düzelt
sudo chmod -R 755 /var/www/html/masterstudio
sudo chown -R www-data:www-data /var/www/html/masterstudio

# Apache yeniden başlat
sudo systemctl restart apache2
```

### Hata: "Class 'mysqli' not found"

**Çözüm:**
```bash
# PHP MySQL uzantısını yükle
sudo apt install php8.2-mysql

# PHP yeniden başlat
sudo systemctl restart php8.2-fpm
sudo systemctl restart apache2
```

### Hata: "CORS hatası"

**Çözüm:**
```bash
# Apache'de CORS etkinleştirme
sudo nano /etc/apache2/mods-available/headers.conf

# Aşağıdakileri ekle:
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE"
```

### Docker Portlar Meşgul

```bash
# Meşgul portu bulma
sudo lsof -i :80   # Port 80
sudo lsof -i :3306 # MySQL portu

# Ya da docker-compose.yml'de portları değiştirin
ports:
  - "8080:80"    # Ön yüz
  - "3307:3306"  # MySQL
```

---

## 🎯 İlk Kurulum Sonrası

### 1. Yönetici Şifresini Değiştirme

1. Admin paneline giriş yapın
2. Profil → Şifre Değiştir
3. Yeni güvenli bir şifre belirleyin

### 2. Otel Bilgilerinizi Düzenleme

1. Admin → Otel Bilgileri
2. Otel adı, telefon, adres vb. güncelleyin

### 3. Oda Tipleri Oluşturma

1. Admin → Oda Tipleri → Yeni Oda Tipi
2. Standart, Deluxe, Süit vb. ekleyin

### 4. Odalar Ekleme

1. Admin → Odalar → Yeni Oda
2. Oda numaralarını atayın

### 5. Dinamik Sayfalar Oluşturma

1. Admin → Sayfalar → Yeni Sayfa
2. Hakkımızda, Hizmetler vb. sayfaları ekleyin

### 6. Ayarları Tamamlama

1. Admin → Ayarlar
2. Footer metni ve sosyal medya linklerini ekleyin

---

## 📧 Destek ve Yardım

- 🐛 Sorun raporla: [GitHub Issues](https://github.com/yamanfurkan353-eng/masterstudio/issues)
- 💬 Soru sor: [GitHub Discussions](https://github.com/yamanfurkan353-eng/masterstudio/discussions)
- 📖 Dokümantasyon: README.md ve CONTRIBUTING.md dosyalarını okuyun

---

**Başarılı kurulumlar! 🎉**

Son güncelleme: Şubat 2026
