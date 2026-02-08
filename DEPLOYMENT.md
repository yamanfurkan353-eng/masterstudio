# 🚀 Deployment Kontrol Listesi

Bu dokuman, MasterStudio Hotel'ı canlı ortama (production) deployment etmeden kontrol edilmesi gereken önemli noktaları içerir.

---

## 1️⃣ Ön Deployment Kontrolleri

### Sistem Gereksinimleri
- [ ] **İşletim Sistemi:** Desteklenen bir sistem (Ubuntu 20.04+, CentOS 8+, vb.)
- [ ] **Server İnterneti:** En az 1Mbps (3Mbps+ önerilir)
- [ ] **Disk Alanı:** En az 1GB boş alan (20GB+ önerilir)
- [ ] **RAM:** En az 512MB-1GB
- [ ] **CPU:** En az 1GHz işlemci

### Yazılım Gereksinimleri
- [ ] **PHP:** 8.2 veya üzeri kurulu ve çalışıyor
- [ ] **MySQL:** 8.0 veya üzeri kurulu ve çalışıyor
- [ ] **Apache:** 2.4 veya üzeri kurulu
- [ ] **mod_rewrite:** Aktifleştirilmiş (`sudo a2enmod rewrite`)
- [ ] **curl/wget:** Kurulmuş sağlıyor
- [ ] **Git:** (İsteğe bağlı, proje indirmek için)

### Dosya İzinleri
- [ ] Proje klasörüne web sunucusu kullanıcısı (www-data) yazma izni
  ```bash
  sudo chown -R www-data:www-data /var/www/html/masterstudio
  sudo chmod -R 755 /var/www/html/masterstudio
  sudo chmod -R 775 /var/www/html/masterstudio/uploads
  ```
- [ ] Log klasörü var ve yazılabilir
  ```bash
  sudo mkdir -p /var/log/masterstudio
  sudo chown www-data:www-data /var/log/masterstudio
  sudo chmod 755 /var/log/masterstudio
  ```
- [ ] Temp klasörü var ve yazılabilir

---

## 2️⃣ Kod ve Yapılandırma Denetimi

### core/config.php
- [ ] **Veritabanı Bilgileri** Doğru ayarlanmış (DB_HOST, DB_USER, DB_PASS, DB_NAME)
- [ ] **Debug Modu** Kapalı (geliştirme sırasında açıksa kapatın)
- [ ] **Hata Bildirimi** Dosyaya yazılacak şekilde ayarlanmış
- [ ] **Gizli Anahtarlar** Güvenli ve random (JWT, API keys, vb.)

### PHP Yapılandırması (php.ini)
- [ ] `display_errors = Off` (Hataları ekranda gösterme)
- [ ] `log_errors = On` (Hataları dosyaya yazma)
- [ ] `error_reporting = E_ALL` (Tüm hataları yakala)
- [ ] `session.cookie_httponly = 1` (Session cookie güvenliği)
- [ ] `session.cookie_secure = 1` (HTTPS için - SSL etkinse)
- [ ] `upload_max_filesize = 10M` (Uygun değer)
- [ ] `disable_functions` Tehlikeli fonksiyonları içeriyor mu?

### Dinamik Dosyalar
- [ ] `.env` dosyası **GİT'İ AYRIT SILİNMİŞ** (`.gitignore` ile)
- [ ] `config-local.php` varsa, `.gitignore`'de
- [ ] Tüm log dosyaları `.gitignore`'de
- [ ] `uploads/` ve `backups/` dizinleri `.gitignore`'de

### Güvenlik Kontrolleri
- [ ] Şifreler en az 12 karakter, karma (büyük, küçük, sayı, sembol)
- [ ] Default admin şifresi değiştirilmiş
- [ ] API keys ve tokens random ve 32+ karakter
- [ ] Hiçbir şifre kaynak kodda hard-coded değil
- [ ] CORS ayarları uygun (`Access-Control-Allow-Origin` kısıtlı)
- [ ] XSS koruması aktif (`htmlspecialchars()` kullanılıyor)
- [ ] SQL injection koruması aktif (prepared statements)
- [ ] CSRF token'ı form'da var

---

## 3️⃣ Veritabanı Denetimi

### Veritabanı Ayarları
- [ ] Veritabanı UTF-8 charset kullanıyor (`utf8mb4_unicode_ci`)
- [ ] Tüm tablolar oluşturulmuş (`sql/database.sql` çalıştırıldı)
- [ ] Foreign key kısıtlamaları etkin
- [ ] User tablosu test verisi kaldırılmış (şifreleri kontrol et)
- [ ] Hiçbir sensitive veri test/sample değerlere sahip değil

### Veritabanı Güvenliği
- [ ] Veritabanı root şifresi güçlü ve değiştirilmiş
- [ ] `hotel_user` şifresi güçlü
- [ ] Root user sadece localhost'tan erişebiliyor
- [ ] Normal user sadece gerekli veritabanına erişebiliyor
- [ ] Yedek kullanıcısı (read-only) oluşturulmuş
- [ ] Veritabanı günlükleri aktif

---

## 4️⃣ Storage ve Yedekleme

### Yedekleme Yapısı
- [ ] `/backups` klasörü var ve yazılabilir
- [ ] İlk yedek alınmış ve test edilmiş
- [ ] Yedekleme scripti cron job'a eklenmişs (günlük/haftalık)
  ```bash
  # Günlük saat 02:00'de yedekleme
  0 2 * * * /var/www/html/masterstudio/scripts/backup.sh >> /var/log/masterstudio_backup.log 2>&1
  ```
- [ ] Yedekler güvenli bir yere (cloud, harici SSD, vb.) kopyalanıyor
- [ ] Yedek sağlığı düzenli kontrol ediliyor

### Upload Klasörleri
- [ ] `/uploads` klasörü var ve web tarafından yazılabilir
- [ ] Web tarafından erişilmeyen dosyaları `.htaccess` ile koru
- [ ] Upload türleri sınırlanmış (güvenlik için)
- [ ] Upload boyutu sınırlanmış

---

## 5️⃣ Web Sunucusu Denetimi

### Apache Yapılandırması
- [ ] Virtual host doğru yapılandırılmış
- [ ] DocumentRoot doğru ayarlanmış
- [ ] `.htaccess` işe yarayacak şekilde etkinleştirilmiş
  ```bash
  sudo a2enmod rewrite
  ```
- [ ] Gizli dosyalar korunuyor (`.htaccess`, `.env`, vb.)
  ```apache
  <FilesMatch "^\.">
      Deny from all
  </FilesMatch>
  ```
- [ ] Directory listing kapalı
  ```apache
  Options -Indexes
  ```
- [ ] Sağlık başlıkları ayarlanmış
  ```apache
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-XSS-Protection "1; mode=block"
  ```

### SSL/TLS Sertifikasi
- [ ] HTTPS sertifikası yüklü ve geçerli
- [ ] HTTP → HTTPS yönlendirmesi etkin
- [ ] SSL sertifikası en az 1 yıl geçerli
- [ ] Sertifika otomatik yenilenmesi kurulu (Let's Encrypt için)
- [ ] HSTS başlığı etkin
  ```apache
  Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
  ```

---

## 6️⃣ DNS ve Alan Adı

### DNS Records
- [ ] A Record doğru IP'yi işaret ediyor
- [ ] CNAME kayıtları (varsa) doğru
- [ ] MX Records (email için) doğru
- [ ] SPF Record ayarlanmış (email güvenliği)
- [ ] DKIM ve DMARC ayarlanmış (isteğe bağlı ama önerilen)

### Sertifika Doğrulama
- [ ] Domain adı sertifikada doğru
- [ ] Wildcard sertifika (varsa) gerekirse

---

## 7️⃣ Email Yapılandırması

### SMTP Ayarları
- [ ] SMTP sunucusu ayarlanmış
- [ ] SMTP kimlik bilgileri doğru
- [ ] Email adresi doğrulanan alan adında
- [ ] SPF ve DKIM geçerli
- [ ] Kullanıcılar test email'i alabiliyorlar

```bash
# Test et
echo "Merhaba Test" | mail -s "Test Email" user@example.com
```

---

## 8️⃣ Monitoring ve Logging

### Log Dosyaları
- [ ] Error log'lar aktif ve yazılıyor
  ```bash
  ls -la /var/log/apache2/masterstudio_*
  tail -f /var/log/apache2/masterstudio_error.log
  ```
- [ ] PHP error log'ları yazılıyor
- [ ] Veritabanı log'ları yazılıyor (isteğe bağlı)
- [ ] Log dosyaları düzenli döndürülüyor (logrotate)

### Monitoring Araçları
- [ ] Serbest disk alanı monitoru
- [ ] Database boyut monitoru
- [ ] HTTP status codes monitoru
- [ ] Server uptime monitoru

### Uyarı Sistemleri
- [ ] Disk alanı azalırsa uyarı
- [ ] Server down olursa uyarı
- [ ] HTTPS sertifikası sona ermeden uyarı
- [ ] Database boyutu artarsa uyarı

---

## 9️⃣ Performans Denetimi

### Hız Testi
- [ ] Homepage yükleme süresi < 3 saniye
- [ ] Admin paneli yükleme < 2 saniye
- [ ] Veritabanı sorguları < 100ms
- [ ] CSS/JS dosyaları minified

### Optimization
- [ ] Görüntüler optimize edilmiş (WebP, compression)
- [ ] CSS/JS sayfaları cache kurulu (Expires headers)
- [ ] Database indeksleri ayarlanmış
- [ ] Gereksiz database sorguları kaldırılmış
- [ ] CDN kullanılıyor (isteğe bağlı)

```bash
# Performans testi (curl ile)
curl -w "
  Time to connect: %{time_connect}s
  Time to start: %{time_starttransfer}s
  Total time: %{time_total}s\n" \
  -o /dev/null -s https://example.com
```

---

## 🔟 Son Kontroller

### Fonksiyonel Testler
- [ ] An sayfası (/, /index.php) açılıyor
- [ ] Odalar sayfası açılıyor
- [ ] Rezervasyon formu doldurulabiliyor
- [ ] Admin giriş sayfası açılıyor
- [ ] Admin paneli açılıyor
- [ ] Cihazlar yönetilebiliyor
- [ ] Tema/dil değişimi çalışıyor
- [ ] Contact formu çalışıyor
- [ ] Email bildirimleri çalışıyor (varsa)

### Güvenlik Test'li
- [ ] SQL injection test edilmiş (güvenlior)
- [ ] XSS test edilmiş (güvenli)
- [ ] CSRF test edilmiş (korumalı)
- [ ] Unauthorized erişim engellenmiş
- [ ] Rate limiting aktif (varsa)

### Cross-Browser Testi
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile tarayıcılar (iOS Safari, Chrome Mobile)

### Responsive Tasarım
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)
- [ ] 4k screenler (3840x2160)

---

## 💁 Deployment Öncesi Checklist

### 24 Saat Öncesi
- [ ] Tüm yapılandırmaları gözden geçir
- [ ] Yedekleme scriptini test et
- [ ] SSL sertifikasını kontrol et
- [ ] E-mail yapılandırmasını test et

### 1 Saat Öncesi
- [ ] Son yedek alındı
- [ ] Tüm sistemler çalışıyor
- [ ] Monitoring araçları aktif
- [ ] Support kanalları hazır
- [ ] Maintenance modunun ne zaman aktif edileceğini planla (varsa)

### Deployment Sırasında
- [ ] Maintenance modu aktif et
- [ ] Veritabanı yedekle
- [ ] Dosyaları deploy et
- [ ] Veritabanını migrate et (varsa)
- [ ] Cache sil
- [ ] Son testleri çalıştır
- [ ] Maintenance modunu devre dışı bırak

### Deployment Sonrası (2-4 Saat)
- [ ] Sistem sağlıklarını kontrol et
- [ ] Log dosyalarını gözden geçir
- [ ] Basit fonksiyonelik testi
- [ ] Kullanıcılardan feedback al
- [ ] Monitoring aktif olduğunu doğrula

### 24 Saat Sonrası
- [ ] Sistem performansını değerlendir
- [ ] Herhangi bir hata veya uyarı var mı kontrol et
- [ ] Yedekleme düzgün çalışıyor mu kontrol et
- [ ] Kullanıcı feedback'i kontrol et

---

## 🆘 Rollback Planı

Bir sorun oluşursa geri dönüş planı:

### Hızlı Geri Dönüş (< 5 dakika)
1. Veritabanını önceki sürüme geri yükle
   ```bash
   ./scripts/restore.sh backups/masterstudio_hotel_YYYYMMDD_HHMMSS.sql
   ```
2. Dosyaları önceki versiyona geri kopyala
   ```bash
   rsync -av /var/backups/masterstudio_backup/ /var/www/html/masterstudio/
   ```
3. Cache sil
   ```bash
   rm -rf /tmp/php* /var/cache/apache2
   ```
4. Apache yeniden başlat
   ```bash
   sudo systemctl restart apache2
   ```

### Seçim İzleme
- Her değişikliğin tarihi ve saati kayıt et
- Git commit'leri imzala ve işaretle
- Database backups'ı etiketle

---

## 📞 Acil İletişim

Deployment sırasında sorun oluşursa:

- **Technical Lead:** [İsim/Email]
- **DBA:** [İsim/Email]
- **Support:** [İsim/Email]
- **Hosting Provider Support:** [Telefon/Email]

---

## ✅ Onay İmzaları

Deployment'a gitmeden önce bu listeyi tamamladığını onaylayanın imzası:

**Yapan Kişi:** _________________________ **Tarih:** _____________

**Kontrol Eden:** ________________________ **Tarih:** _____________

---

Son güncelleme: Şubat 2026
