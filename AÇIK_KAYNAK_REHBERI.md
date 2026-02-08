# 🎯 Açık Kaynak Rehberi - MasterStudio Hotel

Bu dokuman, MasterStudio Hotel projesinin açık kaynak yapısını ve nasıl işlediğini açıklar.

---

## 📚 Proje Yapısı

### Dokümantasyon Dosyaları

| Dosya | Amaç | Kimler İçin |
|-------|------|----------|
| [README.md](README.md) | Proje Ana Sayfası | Herkes |
| [INSTALL.md](INSTALL.md) | Hızlı Kurulum | Yeni Kullanıcılar |
| [KURULUM_TALIMAT.md](KURULUM_TALIMAT.md) | Detaylı Türkçe Rehberi | Turkish Users |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Katkı Yapma Rehberi | Geliştiriciler |
| [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) | Davranış Kuralları | Tüm Katılımcılar |
| [SECURITY.md](SECURITY.md) | Güvenlik Politikası | Security-Conscious Users |
| [CONFIG.md](CONFIG.md) | Yapılandırma Rehberi | System Administrators |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Deployment Kontrol Listesi | DevOps Engineers |
| [CHANGELOG.md](CHANGELOG.md) | Sürüm Geçmişi | Tüm Kullanıcılar |
| [LICENSE.md](LICENSE.md) | MIT Lisansı | Legal |

### Klasör Yapısı

```
masterstudio/
├── .github/
│   ├── ISSUE_TEMPLATE/    # GitHub Issue şablonları
│   │   ├── bug_report.md
│   │   └── feature_request.md
│   └── workflows/         # GitHub Actions CI/CD
│       └── php-lint.yml
├── scripts/               # Yardımcı scriptler
│   ├── backup.sh         # Veritabanı yedekleme
│   └── restore.sh        # Yedekten geri yükleme
├── admin/                # Admin paneli
├── assets/               # CSS, JS, Görseller
├── core/                 # Yapılandırma ve Fonksiyonlar
├── includes/             # Header, Footer
├── sql/                  # Veritabanı şeması
└── docker-compose.yml    # Docker yapılandırması
```

---

## 👥 Katılım Seviyeleri

### 1. Kullanıcı Seviyesi 👤
- Projeyi indirme ve kurlama
- Sorun raporlama
- Özellik isteme
- Dokümantasyon okuma

**Başlangıç:** [README.md](README.md) → [INSTALL.md](INSTALL.md)

### 2. Geliştiriciler Seviyesi 👨‍💻

**Küçük Katkılar:**
- Bug düzeltme
- Dokumentasyon geliştirme
- Kod stili iyileştirmeler
- Çeviriler

**Başlangıç:** [CONTRIBUTING.md](CONTRIBUTING.md)

**Adımlar:**
```bash
# 1. Projeyi fork et
# 2. Klonla
git clone https://github.com/YOUR_USERNAME/masterstudio.git
cd masterstudio

# 3. Feature branch oluştur
git checkout -b feature/my-feature

# 4. Kod yazıp commit et
# 5. GitHub'da Pull Request aç
```

### 3. Maintainer Seviyesi 🔧

- PR'ları gözden geçir
- Issue'ları yönet
- Release'leri yayınla
- Security açıklarını işle

---

## 🔄 Katkı Süreci

### 1. Issue Aç
```bash
# .github/ISSUE_TEMPLATE/ dosyaları otomatik sunum olur
# Tür seç: Bug Report veya Feature Request
```

### 2. Fork & Branch
```bash
# Fork et (GitHub Web'de)
git clone https://github.com/YOUR_USERNAME/masterstudio.git

# Feature branch oluştur
git checkout -b feature/your-feature
# veya
git checkout -b bugfix/your-bugfix
```

### 3. Kod Yazma
```bash
# PHP Syntax kontrol et
php -l your-file.php

# Git'te değişiklikleri görüntüle
git diff

# İyi commit mesajları yazı
git commit -m "feat: Add email notifications"
```

### 4. Push & PR
```bash
git push origin feature/your-feature
# GitHub'da PR aç
```

### 5. Review & Merge
- En az 1 maintainer gözden geçirme
- CI testleri geçmeli
- Format standartlarına uymalı
- Merge edilir

---

## 📋 Kod Kalite Standartları

### PHP Kodlama

✅ **İyi:**
```php
// Prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

// Input validasyon
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new InvalidArgumentException("Invalid email");
}

// Output encoding
echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

// Error handling
try {
    // kod
} catch (Exception $e) {
    error_log($e->getMessage());
}
```

❌ **Kötü:**
```php
// SQL injection riski
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// No validation
echo $_POST['username'];

// No error handling
$conn->query($sql);
```

### JavaScript
- `const` ve `let` kullanı
- Arrow functions tercih et
- Template literals kullan
- Async/await modern approach

### CSS
- CSS variables kullan
- Mobile-first approach
- Semantic class names
- BEM metodolojisi (isteğe bağlı)

---

## 🧪 Testing Kontrol Listesi

Bir PR gönderilmeden önce:

```bash
# 1. PHP syntax testi
find . -name "*.php" -exec php -l {} \;

# 2. Lokal ortamda test et
docker-compose up -d
# Siteyi aç ve testa sokulunuz

# 3. Veritabanı consistency kontrolü
# phpMyAdmin ile tabloları kontrol et

# 4. Code review
# Kendi kodunu gözden geçir: anlaşılır mı, optimize mi?

# 5. Dokümantasyon
# Değişiklikleri README'ye ekle (gerekirse)
```

---

## 🚀 Release Süreci

### Sürüm Numaralandırması
- **MAJOR:** Breaking changes (1.0.0 → 2.0.0)
- **MINOR:** Yeni özellikler, uyumlu (1.0.0 → 1.1.0)
- **PATCH:** Bug düzeltmeler (1.0.0 → 1.0.1)

### Release Kontrol Listesi

1. **Hazırlık**
   - [ ] Tüm PR'lar merge edilmiş
   - [ ] CHANGELOG.md güncellenmiş
   - [ ] Sürüm numarası doğrulandı
   - [ ] SQL migration'lar test edildi

2. **Git Operations**
   ```bash
   # Release branch oluştur
   git checkout -b release/v1.1.0

   # Sürüm numarasını güncelle
   # Commit et
   git commit -am "chore: Bump version to 1.1.0"

   # Main branch'e merge et
   git checkout main
   git merge --no-ff release/v1.1.0

   # Tag oluştur
   git tag -a v1.1.0 -m "Release version 1.1.0"

   # Push et
   git push origin main --tags
   ```

3. **GitHub Release Oluştur**
   - Release notes yazı
   - CHANGELOG'dan kopyala
   - Binaries ekle (varsa)

4. **Post-Release**
   - [ ] Dokümantasyon sitesi günncellenmiş
   - [ ] Social media'da paylaş
   - [ ] Issue'lerde announcement yap

---

## 📊 Proje İstatistikleri

```
Kod Satırları: 3000+
  - PHP: 2000+
  - JavaScript: 300+
  - CSS: 500+

Admin Modülleri: 9
Veritabanı Tabloları: 7
Frontend Sayfaları: 6

Dokümantasyon Sayfaları: 10+
```

---

## 🎓 Yeni Geliştiriciler İçin Yolbaşı

### Adım 1: Proje Hakkında Bilgi Edinin
1. [README.md](README.md) - Projenin ne olduğunu anla
2. [KURULUM_TALIMAT.md](KURULUM_TALIMAT.md) - Sistemi kur
3. Admin panelini keşfet
4. Kodu gözden geçir

### Adım 2: Geliştirme Ortamı Kur
```bash
# Docker ile
docker-compose up -d

# Veya manuel
# KURULUM_TALIMAT.md'yi takip et
```

### Adım 3: Basit Katkı Yap
- Dokümantasyon typo'su düzelt
- Yorum (comment) ekle
- Basit bug'ı düzelt

### Adım 4: Öğren
- Kod yapısını anla
- Fonksiyon kullanımını öğren
- Test süreci tanı

### Adım 5: Büyük Özellik Geliştir
- Issue'yu claim et
- RFC (Request for Comments) aç (sizin önerinizi tartışmak için)
- Kodu develop
- PR gönder
- Feedback'i al ve iyileştir

---

## 💬 İletişim Kanalları

### GitHub
- **Issues:** Bug raporları ve özellik istekleri
- **Discussions:** Sorular ve fikirler
- **Pull Requests:** Kod incelemesi

### Email
- **General:** [Email eklenecek]
- **Security:** [Email eklenecek]

### Sosyal Medya
- Twitter/X: [@masterstudio]
- LinkedIn: [Company Page]

---

## 📜 Lisans Bilgileri

Bu proje **MIT Lisansı** altında dağıtılır.

### Ne Yapabilirsiniz?
- ✅ Ticari kullanım
- ✅ Değiştirme
- ✅ Dağıtma
- ✅ Özel kullanım

### Dikkat Etmeniz Gerekenler
- ⚠️ Orijinal lisans + telif hakkı belirtmeli
- ⚠️ Aynı lisans altında yayınlamalısı
- ❌ Garanti yoktur
- ❌ Sorumlusu tutulamaz

Detaylı bilgi: [LICENSE.md](LICENSE.md)

---

## 🏆 Katkıda Bulunanlar

Bu projede katkıda bulunan herkese teşekkür ederiz!

Listede görünmek için:
1. PR gönder
2. Merge edildikten sonra README'ye eklenir
3. (isteğe bağlı) Adını GitHub profilinin altına koy

---

## 🎯 Proje Hedefleri

### Kısa Vadeli (1-3 Ay)
- [ ] Community kulusması
- [ ] İlk 100 star'ı almak
- [ ] Bug raporları toplamak
- [ ] Feedback almak

### Orta Vadeli (3-6 Ay)
- [ ] Email sistemini tamamlamak
- [ ] Payment gateway'i eklemek
- [ ] Review ve rating sistemi
- [ ] API geliştirmek

### Uzun Vadeli (6-12 Ay)
- [ ] Multi-language desteği
- [ ] Mobile app
- [ ] Advanced reporting
- [ ] Multi-property support

---

## 🤝 Destek Verme

Projeyi seviyorsanız:

1. ⭐ **GitHub'da Star Verin**
2. 🐛 **Bug Raporla** - Bulduğunuz sorunları rapor edin
3. 💬 **Feedback Ver** - İyileştirme fikirlerinizi paylaşın
4. 🔄 **Paylaş** - Arkadaşlarınızla, sosyal medyada paylaşın
5. 📝 **Katkıda Bulun** - Kod, dokümantasyon, çeviri

---

## ✅ Kontrol Listesi: Baştan Başlayan Geliştirici

- [ ] README'yi okudum
- [ ] CONTRIBUTING.md'yi okudum
- [ ] CODE_OF_CONDUCT.md'yi kabul ettim
- [ ] Projeyi fork ve klonlamış
- [ ] Docker ile kurulum yaptım (veya manuel)
- [ ] Admin paneline giriş yapabiliyorum
- [ ] PHP syntax'ı doğrulayabiliyorum
- [ ] Git temel komutlarını biliyorum
- [ ] Bir issue buldum (veya Issue'yu claim edeceğim)
- [ ] Feature branch oluşturdum

Artık kod yazmaya hazırsınız! 🚀

---

Son güncelleme: Şubat 2026

**Hoş geldiniz MasterStudio Hotel topluluğuna! 🎉**
