# 🏨 MasterStudio Hotel - Open Source Otel Yönetim Sistemi

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.md)
[![Version](https://img.shields.io/badge/version-1.0.0-blue)](CHANGELOG.md)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-purple)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-blue)](https://mysql.com)

Açık kaynak, profesyonel otel web sitesi ve yönetim paneli. Modern tasarım, tam özellikli admin paneli, çoklu dil ve tema desteği, ve Docker desteği ile donatılmış.

> **Türkçe/English bilingual project** | Tam uygulamayla başlamaya hazır

---

## 📋 İçerik

- [🚀 Hızlı Başlangıç](#hızlı-başlangıç)
- [✨ Özellikler](#özellikler)
- [�️ Teknolojiler](#teknolojiler)
- [👤 Varsayılan Login](#varsayılan-login)
- [📚 Dokümantasyon](#dokümantasyon)
- [🤝 Katkıda Bulun](#katkıda-bulun)
- [📄 Lisans](#lisans)
- [💬 Destek & İletişim](#destek--iletişim)

---

## 🚀 Hızlı Başlangıç

### Docker ile (Önerilen)
```bash
git clone https://github.com/yamanfurkan353-eng/masterstudio.git
cd masterstudio
cp .env.example .env  # Yapılandırma dosyasını oluştur
docker-compose up -d
```
- **Site:** http://localhost
- **Admin:** http://localhost/admin/auth/login.php
- **phpMyAdmin:** http://localhost:8080

### Manuel Kurulum
- **Local Development (PC/Mac/Linux):** [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md) dosyasına bakın
- **Server Deployment:** [KURULUM_TALIMAT.md](KURULUM_TALIMAT.md) dosyasına bakın

## 🎯 Özellikler

### Frontend (Ön Yüz)
✅ **Responsive & Modern Tasarım** - Tüm cihazlarla uyumlu  
✅ **Açık/Karanlık Tema** - Kullanıcı tercihi ile kaydedilir  
✅ **Çoklu Dil** - Türkçe ve İngilizce  
✅ **SEO Uyumlu** - Meta tagleri ve yapılandırılmış veriler  
✅ **Hızlı Yükleme** - Optimize edilmiş CSS/JS  

**Sayfalar:**
- 🏠 Anasayfa (Hero section + Oda tipleri showcase)
- 🛏️ Odalarımız (Detaylı oda tipi listesi)
- 📋 Rezervasyon (Dinamik form)
- ℹ️ Hakkımızda (Otel bilgileri)
- 📧 İletişim (İletişim formu + Bilgiler)
- 📄 Dinamik Sayfalar (Admin panelinden oluşturulabilir)

### Admin Paneli (Yönetim)
✅ **Güvenli Giriş** - Bcrypt şifre hashleme  
✅ **Dashboard** - Rezervasyon istatistikleri  
✅ **Rezervasyon Yönetimi** - Onay/İptal işlemleri  
✅ **Oda & Oda Tipi Yönetimi** - CRUD işlemleri  
✅ **Otel Bilgileri** - Otel adı, telefon, saat vb.  
✅ **Sayfa Yönetimi (CMS)** - Dinamik sayfa oluştur/düzenle  
✅ **Kullanıcı Yönetimi** - Yeni admin/editör ekleme  
✅ **Profil Ayarları** - Şifre değiştir, e-posta güncelle  
✅ **Ayarlar** - Footer, sosyal medya linkleri  

## 📂 Klasör Yapısı

```
masterstudio/
├── index.php                 # Anasayfa
├── rooms.php                 # Odalar sayfası
├── booking.php              # Rezervasyon sayfası
├── about.php                # Hakkımızda
├── contact.php              # İletişim
├── page.php                 # Dinamik sayfa loader
├── admin/
│   ├── index.php            # Dashboard
│   ├── profile.php          # Profil/Şifre ayarları
│   ├── auth/
│   │   └── login.php        # Admin giriş
│   ├── modules/
│   │   ├── reservations.php # Rezervasyon yönetimi
│   │   ├── rooms.php        # Oda yönetimi
│   │   ├── room-types.php   # Oda tipi yönetimi
│   │   ├── hotel-info.php   # Otel bilgileri
│   │   ├── pages.php        # Sayfa yönetimi (CMS)
│   │   ├── users.php        # Kullanıcı yönetimi
│   │   └── settings.php     # Genel ayarlar
│   └── includes/
│       └── check-admin.php  # Yetkilendirme kontrolü
├── assets/
│   ├── css/
│   │   ├── style.css        # Ana stil
│   │   ├── dark.css         # Karanlık tema
│   │   ├── admin-style.css  # Admin paneli stili
│   │   └── admin-login.css  # Giriş sayfası stili
│   ├── js/
│   │   ├── main.js          # Genel fonksiyonlar
│   │   ├── theme.js         # Tema seçimi
│   │   └── lang.js          # Dil seçimi
│   ├── img/                 # Görseller
│   └── vendor/              # Üçüncü taraf kütüphaneler
├── core/
│   ├── config.php           # Veritabanı yapılandırması
│   └── functions.php        # Yardımcı fonksiyonlar
├── includes/
│   ├── header.php           # Dinamik başlık
│   └── footer.php           # Dinamik alt bilgi
├── sql/
│   └── database.sql         # Veritabanı şeması
├── docker-compose.yml       # Docker yapılandırması
├── Dockerfile               # PHP imajı
├── apache.conf             # Apache yapılandırması
├── INSTALL.md              # Kurulum rehberi
└── README.md               # Bu dosya
```

## 🔐 Varsayılan Login

```
Kullanıcı Adı: admin
Şifre: admin123
```

⚠️ **ÖNEMLİ:** İlk kurulumdan sonra şifrenizi değiştirin!

## 🛠️ Geliştirme

### Teknolojiler
- **Backend:** PHP 8.2+
- **Database:** MySQL 8.0+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Server:** Apache
- **Containerization:** Docker & Docker Compose

### Gerekli Paketler
```bash
# Docker kurulumu
curl -fsSL https://get.docker.com | sh

# Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

## 📚 Kullanım Örnekleri

### Yeni Oda Tipi Ekleme
1. Admin Paneline giriş yapın
2. Oda Tipleri → Yeni Oda Tipi Ekle
3. Bilgileri doldurup kaydedin

### Dinamik Sayfa Oluşturma
1. Admin Paneline giriş yapın
2. Sayfalar → Yeni Sayfa Oluştur
3. Türkçe ve İngilizce içerik ekleyin
4. "Yayında Yap" seçeneğini işaretleyin
5. Sayfa http://yoursite.com/page.php?page=slug-name adresinde görünür

### Rezervasyon Yönetimi
1. Dashboard'dan son rezervasyonları görün
2. Rezervasyonlar sayfasında detaydı görüntüleyin
3. Durumu (Beklemede/Onaylanan/İptal) değiştirin

## 🔒 Güvenlik Önerileri

1. **Şifre Değiştir** - İlk giriş sonrası hemen şifre değiştirin
2. **HTTPS Kullan** - Üretim ortamında SSL sertifikası gereklidir
3. **Güncellemeleri Yapın** - PHP ve MySQL'i güncel tutun
4. **Backup Alın** - Düzenli veritabanı yedeklemesi yapın
5. **.env Dosyası** - Sunucuda güvenli bir yerde saklayın

### Backup Alma
```bash
# Docker ile
docker-compose exec mysql mysqldump -u root -p masterstudio_hotel > backup.sql

# Manuel
mysqldump -u root -p masterstudio_hotel > backup.sql
```

## 🐛 Sorun Giderme

### Veritabanı Hatası
- MySQL hizmetinin çalışıp çalışmadığını kontrol edin
- Kimlik bilgilerini `core/config.php`'de doğrulayın
- Veritabanının oluşturulup oluşturulmadığını kontrol edin

### Dosya İzin Hatası
```bash
sudo chmod -R 755 /path/to/masterstudio
sudo chown -R www-data:www-data /path/to/masterstudio
```

### Docker İçinde Sorun
```bash
# Logları kontrol et
docker-compose logs php
docker-compose logs mysql

# Konteynerları yeniden başlat
docker-compose restart
```

## 🤝 Katkı Yapma

1. Depoyu fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişiklikleri commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'e push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📄 Lisans

MIT Lisansı altında dağıtılmaktadır.

## 📧 İletişim

- **GitHub:** https://github.com/yamanfurkan353-eng/masterstudio
- **Issues:** GitHub Issues kullanarak sorun rapor edin
- **Discussions:** Fikirler ve öneriler için tartışmalar başlatın

## 🎓 Öğrenme Kaynakları

- [PHP Dokümantasyonu](https://www.php.net/docs.php)
- [MySQL Rehberi](https://dev.mysql.com/doc/)
- [Docker Tutorial](https://docs.docker.com/get-started/)
- [HTML/CSS/JS Best Practices](https://developer.mozilla.org/)

---

## 📚 Dokümantasyon

### 🚀 Başlamak İçin
- [**INSTALL.md**](INSTALL.md) - Hızlı kurulum (5-10 dakika)
- [**LOCAL_DEVELOPMENT.md**](LOCAL_DEVELOPMENT.md) - PC/Mac/Linux'ta kurulum (kendi bilgisayarına)

### 📖 Detaylı Rehberler
- [**KURULUM_TALIMAT.md**](KURULUM_TALIMAT.md) - Sunuda kurulum (Windows, Linux, macOS, VDS/VPS, Docker)
- [**CONFIG.md**](CONFIG.md) - Yapılandırma ve özelleştirme
- [**DEPLOYMENT.md**](DEPLOYMENT.md) - Produksiyona çıkma kontrol listesi

### 👥 Topluluk
- [**CONTRIBUTING.md**](CONTRIBUTING.md) - Katkı yapma rehberi
- [**CODE_OF_CONDUCT.md**](CODE_OF_CONDUCT.md) - Davranış kuralları
- [**AÇIK_KAYNAK_REHBERI.md**](AÇIK_KAYNAK_REHBERI.md) - Açık kaynak yapısı

### 🔒 Güvenlik & Sürüm
- [**SECURITY.md**](SECURITY.md) - Güvenlik politikası ve best practices
- [**CHANGELOG.md**](CHANGELOG.md) - Sürüm geçmişi
- [**LICENSE.md**](LICENSE.md) - MIT Lisansı

---

## 🎯 Desteklenen Özellikler Özeti

| Özellik | Durum | Açıklama |
|---------|-------|---------|
| Responsive Design | ✅ | Tüm cihazlarla uyumlu |
| Admin Panel | ✅ | 9 modül ile tam yönetim |
| Veritabanı | ✅ | MySQL 8.0+ otomatik setup |
| Docker | ✅ | One-command deployment |
| Çoklu Dil | ✅ | TR/EN (FI, DE genişlemesi hazır) |
| Açık/Koyu Tema | ✅ | CSS variables tabanlı |
| SEO | ✅ | Meta tags ve yapılandırma |
| Güvenlik | ✅ | Bcrypt, SQL injection protection |
| HTTPS/SSL | ✅ | Let's Encrypt entegrasyonu |
| Backup Araçları | ✅ | Veritabanı yedekleme scriptleri |

---

## 🌟 Neden MasterStudio?

- ✨ **Kurumu Hazır** - Direkt deployment'a başlayın
- 🔒 **Güvenli** - Profesyonel güvenlik uygulamaları
- 🚀 **Hızlı** - Optimize edilmiş kod ve sorguları
- 📱 **Responsive** - Mobil-first tasarım
- 🎨 **Modern** - Güncel UI/UX pratiyleri
- 🌍 **Açık Kaynak** - MIT lisansı, herkes katkıda bulabilir
- 📖 **İyi Dokümante** - Türkçe/İngilizce detaylı rehberler
- 🐳 **Docker Ready** - Kontainerize edilmiş hazır setup

---

## 🚀 1 Dakika ile Başla

```bash
# 1. Projeyi klonla
git clone https://github.com/yamanfurkan353-eng/masterstudio.git
cd masterstudio

# 2. Docker ile başlat (Docker kurulu olması gerekir)
docker-compose up -d

# 3. Adreslere git
# - Ön yüz:      http://localhost
# - Admin:       http://localhost/admin/auth/login.php  
# - phpMyAdmin:  http://localhost:8080
```

**Bitdi!** 🎉

---

## 📱 Ekran Görüntüleri

### Ön Yüz
- Homepage ile hero section ve oda showcase
- Responsive odalar listesi
- Tam rezervasyon sistemi
- İletişim ve hakkımızda sayfaları
- Açık/Koyu tema geçişi

### Admin Paneli
- Dashboard ile istatistikler
- Rezervasyon yönetimi
- Oda ve oda tipi CRUD
- CMS sayfa yönetimi
- Kullanıcı yönetimi
- Profil ve ayarlar

---

## 🤝 Katkıda Bulun

Katkılar bize çok önemli! İşte nasıl yapabilirsiniz:

1. **Kodu Improve Et** - Bug düzelt, özellik ekle
2. **Dokümantasyon** - Rehberleri geliştir, çeviri yap
3. **Issue Rapor Et** - Problemi bildir, öneride bulun
4. **Paylaş** - Arkadaşlarla, sosyal medyada paylaş

Detaylı rehber için [CONTRIBUTING.md](CONTRIBUTING.md) dosyasına bakın.

---

## 📝 Versiyonlar

| Version | Tarih | Durum | EOL |
|---------|-------|-------|-----|
| **1.0.0** | Feb 2026 | ✅ Aktif | Feb 2027 |
| 0.9.5 | Jan 2026 | Eski | Aug 2026 |

[Tüm sürümler için CHANGELOG'a bakın](CHANGELOG.md)

---

## 🔒 Güvenlik

**Önemli:** Güvenlik açıkları kamuya açık Issue'lerde raporlamayın.

[SECURITY.md](SECURITY.md) dosyasındaki talimatları takip edin.

Rapor için: security@masterstudio.local

---

## ⚖️ Açık Kaynak Lisansı

Bu proje [MIT Lisansı](LICENSE.md) altında dağıtılır.

```
Kısa söylemek gerekirse:
✅ Ticari kullanabilir
✅ Değiştirebilir
✅ Yazılımı dağıtabilir
⚠️  Orijinal lisans kopyasını saklayın
❌ Garanti vermez
```

Detaylı lisans bilgisi için [LICENSE.md](LICENSE.md)'ye bakın.

---

## 💬 Destek & İletişim

### Sorularınız Varsa

- 📖 [Dokümantasyon](README.md) ve [Rehbeleri](KURULUM_TALIMAT.md) okuyun
- 💬 [GitHub Discussions](https://github.com/yamanfurkan353-eng/masterstudio/discussions) başlatın
- 🐛 [GitHub Issues](https://github.com/yamanfurkan353-eng/masterstudio/issues) açın
- 📧 Email: [İletişim için eposta eklenebilir]

### Sosyal Medya

- 🌟 GitHub'da ⭐ vermeyi unutmayın!
- 👥 Tartışmalara katılın
- 📢 Proje'yi paylaş

---

## 👥 Katkıda Bulunanlar

Bu projede katkıda bulunan herkese teşekkür ederiz!

---

## 📊 İstatistikler

- **Kod Satırları:** 3000+ (PHP, JS, CSS)
- **Admin Modülleri:** 9
- **Frontend Sayfaları:** 6
- **Veritabanı Tabloları:** 7
- **Dokümantasyon Sayfaları:** 7

---

## 🎓 Kullanılan Teknolojiler

**Backend:**
- PHP 8.2+ (Object-Oriented Programming)
- MySQL 8.0+ (Relational Database)
- Apache 2.4 (Web Server)

**Frontend:**
- HTML5 (Semantic Markup)
- CSS3 (Flexbox, CSS Grid, Variables)
- Vanilla JavaScript (No jQuery dependency)

**DevOps:**
- Docker (Containerization)
- Docker Compose (Multi-container orchestration)

---

## 🚀 Gelecek Yükseltmeler

### v1.1.0 (Q2 2026)
- Email bildirimleri
- SMS entegrasyonu
- Payment gateway (Stripe, PayPal)
- Kullanıcı yorumları ve derecelendirme

### v1.2.0 (Q3 2026)
- REST API
- Mobile app (PWA)
- Advanced reporting'in
- Promocional kodlar

### v2.0.0 (Q4 2026)
- Microservices mimarisi
- GraphQL API
- Real-time notifications
- Multi-property support

---

## 📚 Kaynaklar

- [PHP Resmi Dokümantasyonu](https://www.php.net/docs.php)
- [MySQL Öğretici](https://dev.mysql.com/doc/)
- [Docker Başlarken](https://docs.docker.com/get-started/)
- [Web Geliştirme Best Practices](https://developer.mozilla.org/)
- [OWASP Security](https://owasp.org/)

---

**Son Güncelleme:** Şubat 2026  
**Sürüm:** 1.0.0  
**Durum:** Aktif Geliştirme

Projeyi beğendiyseniz ⭐ vermeyi unutmayın!
