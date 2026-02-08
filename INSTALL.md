# Kurulum Rehberi - MasterStudio Hotel

## 🚀 Hızlı Başlangıç

### 1. Docker ile Kurulum (Önerilen)

Docker ve Docker Compose yüklü olmalıdır. Eğer yoksa:
- **Linux/Mac:** `https://docs.docker.com/get-docker/`
- **Windows:** Docker Desktop indirin

#### Adımlar:

```bash
# Projeyi klonla
git clone https://github.com/yamanfurkan353-eng/masterstudio.git
cd masterstudio

# .env dosyasını düzenle (isteğe bağlı)
nano .env

# Docker konteynerlerini başlat
docker-compose up -d

# Veritabanını oluştur (ilk kurulum)
docker-compose exec mysql mysql -u root -p masterstudio_hotel < sql/database.sql
```

#### Erişim Adresleri:
- **Site:** http://localhost
- **Admin Paneli:** http://localhost/admin/auth/login.php
- **phpMyAdmin:** http://localhost:8080

#### Varsayılan Login Bilgileri:
- **Kullanıcı Adı:** admin
- **Şifre:** admin123

---

### 2. Manuel Kurulum (VDS/VPS)

#### Gerekli Yazılımlar:
- PHP 8.2+
- MySQL 8.0+
- Apache (mod_rewrite etkinleştirilmiş)
- Composer (isteğe bağlı)

#### Adımlar:

1. **Projeyi İndir:**
   ```bash
   git clone https://github.com/yamanfurkan353-eng/masterstudio.git
   cd masterstudio
   ```

2. **Veritabanını Oluştur:**
   ```bash
   mysql -u root -p < sql/database.sql
   ```

3. **Yapılandırma Dosyasını Düzenle:**
   ```bash
   nano core/config.php
   ```
   
   Veritabanı bilgilerinizi güncelleyin:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'hotel_user');
   define('DB_PASS', 'hotel_password');
   define('DB_NAME', 'masterstudio_hotel');
   ```

4. **Web Sunucusunu Yapılandır:**

   **Apache için:**
   ```bash
   # apache.conf dosyasını Apache sites dizinine kopyala
   sudo cp apache.conf /etc/apache2/sites-available/masterstudio.conf
   
   # Siteyi etkinleştir
   sudo a2ensite masterstudio
   
   # Apache'yi yeniden başlat
   sudo systemctl restart apache2
   ```

5. **İzinleri Ayarla:**
   ```bash
   sudo chown -R www-data:www-data /path/to/masterstudio
   sudo chmod -R 755 /path/to/masterstudio
   ```

6. **SSL Sertifikası Ekle (Let's Encrypt):**
   ```bash
   sudo apt update
   sudo apt install certbot python3-certbot-apache
   sudo certbot --apache -d yourdomain.com
   ```

---

## 📋 Yönetim Paneli Kullanımı

### Admin Paneli Erişimi
1. Admin paneline gidin: `http://yoursite.com/admin/auth/login.php`
2. Varsayılan kimlik bilgileriyle giriş yapın
3. **Güvenlik:** İlk giriş sonrası şifrenizi değiştirin!

### Temel İşlevler

#### 1. Otel Bilgileri
- Otel adı, açıklaması, adresi
- Telefon ve e-posta
- Giriş/Çıkış saatleri
- Yıldız derecelendirmesi

#### 2. Oda Tipleri Yönetimi
- Yeni oda tipi ekleme
- Fiyatlandırma
- Kolaylıklar (amenities)
- Çoklu dil desteği

#### 3. Odalar Yönetimi
- Oda numarası atama
- Oda tipi seçimi
- Kat bilgisi
- Müsaitlik durumu

#### 4. Rezervasyon Yönetimi
- Gelen rezervasyonları görüntüleme
- Durumunu değiştirme (Beklemede, Onaylanan, İptal)
- Konuk bilgilerine erişme

#### 5. Sayfalar (CMS)
- Dinamik sayfa oluşturma
- Türkçe/İngilizce desteği
- SEO-dostu URL'ler
- Taslak/Yayın durumu

#### 6. Genel Ayarlar
- Site adı ve açıklama
- Footer metni
- Sosyal medya linkleri
- İletişim bilgileri

---

## 🌐 Multi-Language & Theme Support

### Dil Desteği
- Türkçe (TR)
- İngilizce (EN)

Dil seçimini header'daki dropdown menüden yapabilirsiniz.

### Tema Seçimi
- **Açık Tema** (Light Mode)
- **Karanlık Tema** (Dark Mode)

Tema değiştir butonundan tema seçimini yapabilirsiniz.

---

## 🔒 Güvenlik İpuçları

1. **Şifte Değiştir:** İlk girişte admin şifresini değiştirin
2. **HTTPS Kullan:** Üretim ortamında SSL sertifikası kurun
3. **Güncellemeleri Yapın:** PHP ve MySQL'i güncel tutun
4. **Backuplar:** Düzenli veritabanı yedeklemesi yapın
5. **.env Dosyası:** `.env` dosyasını sunucuda güvenli bir yerde saklayın

### Backup Alma:
```bash
# Docker ile
docker-compose exec mysql mysqldump -u root -p masterstudio_hotel > backup.sql

# Manuel
mysqldump -u root -p masterstudio_hotel > backup.sql
```

---

## 🐛 Sorun Giderme

### Veritabanı Bağlantısı Hatası
- MySQL hizmetinin çalışıp çalışmadığını kontrol edin
- Kimlik bilgilerini kontrol edin
- `core/config.php`'de yapılandırmayı doğrulayın

### Dosya İzin Hatası
```bash
sudo chmod -R 755 /path/to/masterstudio
sudo chown -R www-data:www-data /path/to/masterstudio
```

### Docker Hataları
```bash
# Konteyner loglarını kontrol et
docker-compose logs php
docker-compose logs mysql

# Tüm konteynerları yeniden başlat
docker-compose restart
```

---

## 📞 Destek ve Katkı

- **GitHub:** https://github.com/yamanfurkan353-eng/masterstudio
- **Sorunları Raporla:** GitHub Issues kullanarak
- **Katkıda Bulun:** Pull Requests göndererek

---

## 📄 Lisans

Bu proje açık kaynak olup MIT Lisansı altında dağıtılmaktadır.
