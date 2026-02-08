# 🏠 Local Development Kurulum Rehberi

Bu rehber, **kendi bilgisayarınızda** (Windows, Mac, Linux) MasterStudio Hotel'i kurmak için adımları içerir.

## 📋 İçerik
1. [Gereksinimler](#gereksinimler)
2. [Windows'ta Kurulum](#windowsta-kurulum)
3. [Mac'te Kurulum](#macte-kurulum)
4. [Linux'ta Kurulum](#linuxta-kurulum)
5. [Docker ile Kurulum](#docker-ile-kurulum-tüm-platformlar)
6. [İlk Kurulum Sonrası](#ilk-kurulum-sonrası)
7. [Server'a Deployment](#servera-deployment)

---

## ⚙️ Gereksinimler

### Option 1: Docker (ÖNERİLEN - Tüm Sistemler)
- **Docker Desktop** kurulu
- **Docker Compose** kurulu
- 2GB RAM, 2GB disk

### Option 2: Manuel Kurulum
- **PHP** 8.2 veya üzeri
- **MySQL** 8.0 veya üzeri
- **Apache** 2.4 veya üzeri
- **Git** (klonlamak için)

---

## 🐳 Docker ile Kurulum (TÜM PLATFORMLAR - ÖNERİLEN)

### 1. Projeyi İndir

```bash
git clone https://github.com/yamanfurkan353-eng/masterstudio.git
cd masterstudio
```

### 2. .env Dosyasını Oluştur

```bash
cp .env.example .env
```

**Windows PowerShell:**
```powershell
Copy-Item .env.example .env
```

### 3. .env Dosyasını Düzenle (Local Development)

```bash
# Metin editörü ile aç
nano .env
# veya
code .env  # VS Code ile aç
```

**Aşağıdaki değerleri kullan:**

```env
# Local Development İçin Dummy Değerler
DB_HOST=mysql
DB_USER=hotel_user
DB_PASS=hotel_password
DB_NAME=masterstudio_hotel
MYSQL_ROOT_PASSWORD=root_password
PHP_ENV=development
SITE_URL=http://localhost
```

### 4. Docker Container'larını Başlat

```bash
docker-compose up -d
```

**Çıktı:**
```bash
Creating masterstudio_mysql_1    ... done
Creating masterstudio_php_1      ... done
Creating masterstudio_phpmyadmin_1 ... done
```

### 5. Veritabanını Başlat

```bash
docker-compose exec mysql mysql -u hotel_user -p"hotel_password" masterstudio_hotel < sql/database.sql
```

Şifre istenirse: `hotel_password` yazın

### 6. Erişim

- **Web Sitesi:** http://localhost
- **Admin Panel:** http://localhost/admin/auth/login.php
- **phpMyAdmin:** http://localhost:8080
  - Kullanıcı: `hotel_user`
  - Şifre: `hotel_password`

---

## 💻 Windows'ta Manuel Kurulum

### 1. XAMPP İndirme ve Yükleme

1. https://www.apachefriends.org/ adresine git
2. Windows için XAMPP indir (PHP 8.2+)
3. Çalıştır ve yükle
4. Yükleme sırasında Apache ve MySQL seçeneğini işaretle

### 2. Projeyi Kopyala

```
C:\xampp\htdocs\masterstudio
```

Klasörü oluştur ve projeyi buraya kopyala

### 3. .env Dosyası Oluştur

Projeyi klasöründe `.env.example`'ı `.env` olarak kopyala:

```bash
copy .env.example .env
```

### 4. .env Dosyasını Düzenle

`Notepad++` veya `VS Code` ile aç ve düzenle:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=masterstudio_hotel
MYSQL_ROOT_PASSWORD=
PHP_ENV=development
SITE_URL=http://localhost/masterstudio
```

### 5. XAMPP Başlat

1. XAMPP Control Panel aç
2. Apache ve MySQL'in yanındaki "Start" butonlarına tıkla

### 6. Veritabanı Oluştur

1. http://localhost/phpmyadmin adresine git
2. "Veritabanı" sekmesinde yeni DB oluştur: `masterstudio_hotel`
3. `sql/database.sql` dosyasını import et

### 7. Erişim

- **Web Sitesi:** http://localhost/masterstudio
- **Admin:** http://localhost/masterstudio/admin/auth/login.php

---

## 🍎 Mac'te Manuel Kurulum

### 1. Homebrew ile Kurulum

```bash
# Homebrew kurulu değilse
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# PHP yükle
brew install php@8.2

# MySQL yükle
brew install mysql

# Apache (macOS'ta built-in, sadece etkin hale getir)
sudo apachectl start
```

### 2. Projeyi Klonla

```bash
cd ~/Documents
git clone https://github.com/yamanfurkan353-eng/masterstudio.git
cd masterstudio
```

### 3. PHP Sunucusunu Başlat (Hızlı Test)

```bash
php -S localhost:8000
```

Veya Apache'de:

```bash
# Apache Document Root'a symlink oluştur
sudo ln -s $(pwd) /Library/WebServer/Documents/masterstudio
```

### 4. MySQL Başlat

```bash
mysql.server start
```

### 5. .env Oluştur

```bash
cp .env.example .env
nano .env
```

**Mac için ayarlar:**
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=masterstudio_hotel
PHP_ENV=development
SITE_URL=http://localhost:8000
```

### 6. Veritabanı Başlat

```bash
mysql -u root < sql/database.sql
```

### 7. Erişim

- http://localhost:8000
- http://localhost:8000/admin/auth/login.php

---

## 🐧 Linux'ta Manuel Kurulum

### Ubuntu/Debian

```bash
# Paket güncellemesi
sudo apt update && sudo apt upgrade -y

# PHP ve uzantıları
sudo apt install -y php8.2 php8.2-mysql php8.2-gd

# MySQL
sudo apt install -y mysql-server

# Git
sudo apt install -y git

# Apache
sudo apt install -y apache2 libapache2-mod-php8.2
sudo a2enmod rewrite
sudo systemctl start apache2
```

### CentOS/RHEL

```bash
# PHP Repository
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-$(rpm -E '%{rhel}').rpm

# PHP
sudo dnf install -y php php-mysql php-gd

# MySQL
sudo dnf install -y mysql-server

# Apache
sudo dnf install -y httpd
sudo systemctl start httpd
```

### 5. Projeyi Klonla

```bash
cd ~/Documents
git clone https://github.com/yamanfurkan353-eng/masterstudio.git
cd masterstudio
cp .env.example .env
```

### 6. .env Düzenle

```bash
nano .env
```

### 7. Veritabanı

```bash
sudo mysql -u root < sql/database.sql
```

### 8. Erişim

- http://localhost/masterstudio (ve `sudo ln -s ~/Documents/masterstudio /var/www/html/masterstudio`)

---

## 📱 İlk Kurulum Sonrası

### Admin Paneline Gir

1. http://localhost/admin/auth/login.php (veya kurumunuzun URL'si)
2. Giriş:
   - **Kullanıcı:** admin
   - **Şifre:** admin123 (SQL dosyasında varsayılan)

### Şifreni Değiştir

⚠️ **ÖNEMLİ:** İlk giriş sonrası hemen şifreni değiştir!

1. Admin Paneli → Profil
2. "Şifre Değiştir" bölümüne git
3. Yeni güvenli bir şifre belirle (12+ karakter)

### Otel Bilgilerini Güncelle

1. Admin Paneli → Otel Bilgileri
2. Otel adı, telefon, adres bilgilerini gir

### Oda Tipleri Ekle

1. Admin Paneli → Oda Tipleri
2. "Yeni Oda Tipi" butonuna tıkla
3. Standart, Deluxe, Süit vb. ekle

### Odalar Oluştur

1. Admin Paneli → Odalar
2. Oda numaralarını ata

---

## 📊 Development vs Production Farkı

| Ayar | Local Development | Server Production |
|------|-------------------|------------------|
| **DB_HOST** | localhost | sunucu IP'si |
| **DB_USER** | root veya test_user | güvenlü_user (root değil!) |
| **DB_PASS** | boş veya test | KULLANIÇI GÜÇLÜ (12+ karakter) |
| **PHP_ENV** | development | production |
| **SITE_URL** | http://localhost | https://yourdomain.com |
| **Error Display** | Açık (ekranda göster) | Kapalı (log'a yazı) |

---

## 🚀 Server'a Deployment

Production'a taşımaya hazır olduğunda:

### 1. Yeni .env Oluştur (Server)

```bash
cp .env.example .env.production
```

**Server için değerleri ayarla:**
```env
DB_HOST=localhost
DB_USER=hotel_user
DB_PASS=GÜÇLÜ_ŞİFRE_12_KARAKTER_YA_DA_DAHA_FAZLA
MYSQL_ROOT_PASSWORD=GÜÇLÜ_ROOT_ŞİFRESİ
PHP_ENV=production
SITE_URL=https://yourdomain.com
```

### 2. Production .env Kustırmasını Al

```bash
# Server'da SSH bazlı çalış
scp .env.production user@server:/var/www/html/masterstudio/.env
```

### 3. Veritabanı Backup Al

```bash
mysqldump -u hotel_user -p masterstudio_hotel > backup_$(date +%Y%m%d).sql
```

### 4. HTTPS Sertifikası Kur

```bash
sudo certbot --apache -d yourdomain.com
```

### Detaylı Production Talimatları

Bkz: [DEPLOYMENT.md](DEPLOYMENT.md) ve [KURULUM_TALIMAT.md](KURULUM_TALIMAT.md)

---

## 🐛 Sorun Giderme

### "Cannot connect to database"

```bash
# Docker'da MySQL çekişi var mı kontrol et
docker-compose logs mysql

# MySQL'i restart et
docker-compose restart mysql

# Manual kurulumda MySQL çalışıyor mu kontrol et
sudo systemctl status mysql
# veya
mysql.server status
```

### "localhost/.env not found"

```bash
# .env dosyasını oluştur
cp .env.example .env
cat .env  # Dosyanın içeriğini göster
```

### Port 3306 kullanımda

```bash
# Port değiştir
# .env dosyasında: MYSQL_PORT=3307
# docker-compose.yml'de ports'u güncelle

# veya mevcut servisi kapat
docker-compose down
```

### Apache/PHP Yükseltme

```bash
# PHP versiyonunu kontrol et
php -v

# Eksik extension kontrol et
php -m | grep mysql
```

---

## 📚 Sonraki Adımlar

1. [README.md](README.md) - Projeyi tanılı
2. [CONTRIBUTING.md](CONTRIBUTING.md) - Katkıda bulun
3. [CONFIG.md](CONFIG.md) - İleri ayarlar
4. [KURULUM_TALIMAT.md](KURULUM_TALIMAT.md) - Server Deployment

---

**Son Güncelleme:** Şubat 2026

Sorunuz varsa [GitHub Issues](https://github.com/yamanfurkan353-eng/masterstudio/issues) açın! 🎉
