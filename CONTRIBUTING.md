# Katkıda Bulunma Rehberi

Öncelikle, MasterStudio Hotel projesine katkı yapmayı düşündüğünüz için teşekkür ederiz! 🎉

Bu dokuman, projeye nasıl katkıda bulunabileceğinizi açıklar.

## 📋 İçerik
1. [Başlamadan Önce](#başlamadan-önce)
2. [Kod Katkısı](#kod-katkısı)
3. [Bug Raporlama](#bug-raporlama)
4. [Özellik İsteme](#özellik-isteme)
5. [Kod Standartları](#kod-standartları)
6. [Komit Mesajları](#komit-mesajları)
7. [Pull Request Süreci](#pull-request-süreci)

---

## 🚀 Başlamadan Önce

### Araştırma Yapın
- Sorun zaten rapor edilmiş mi kontrol edin
- Pull request'leri kontrol edin
- Dokümantasyonu okuyun

### Geliştirme Ortamı Kurulumu

```bash
# Repoyu fork edin (GitHub'da Fork butonu)

# Forkunuzu klonlayın
git clone https://github.com/YOUR_USERNAME/masterstudio.git
cd masterstudio

# Orijinal repoyu remote olarak ekleyin
git remote add upstream https://github.com/yamanfurkan353-eng/masterstudio.git

# Bağımlılıkları yükleyin (Docker ile)
docker-compose up -d

# Ya da manuel olarak kurulum yapın
# KURULUM_TALIMAT.md dosyasına bakın
```

---

## 💻 Kod Katkısı

### Branch Oluşturma

```bash
# Ana branch'i güncelleyin
git fetch upstream
git rebase upstream/main

# Yeni feature branch oluşturun
git checkout -b feature/your-feature-name

# Veya bug fix için
git checkout -b bugfix/your-bug-name

# Veya documentation için
git checkout -b docs/your-documentation-name
```

### Yapılandırma

**Branch Namenaming Kuralları:**
- Feature: `feature/short-description` (örn: `feature/add-email-notifications`)
- Bug Fix: `bugfix/short-description` (örn: `bugfix/login-redirect-issue`)
- Documentation: `docs/short-description` (örn: `docs/api-documentation`)
- Security: `security/short-description` (örn: `security/sql-injection-fix`)

### Geliştirme

1. Kodu yazın
2. Testler ekleyin
3. Lokal olarak test edin

```bash
# Docker ile test etme
docker-compose exec php php -l modules/your-file.php

# Veri tabanı testleri
docker-compose exec mysql mysql -u root -p"root_password" < test.sql
```

4. Tüm testleri geçildikten sonra commit yapın

---

## 🐛 Bug Raporlama

### Yeni Issue Oluşturma

GitHub'da [Issues](https://github.com/yamanfurkan353-eng/masterstudio/issues) sekmesine gidin.

**Başlık:** Kısa ve açıklayıcı olun
```
Oturum açma sayfası 403 hatasını gösteriyor
```

**Açıklama şablonu:**

```markdown
## Sorun Açıklaması
[Sorunun ne olduğunu açıklayın]

## Adımlar
1. [İlk adım]
2. [İkinci adım]
3. [Hata oluştuğu adım]

## Beklenen Davranış
[Ne olması gerekiyordu]

## Gerçek Davranış
[Aslında ne oldu]

## Ekran Görüntüsü
[Varsa ekleyin]

## Sistem Bilgileri
- İşletim Sistemi: [Windows 10, Ubuntu 20.04, vb.]
- PHP Versiyonu: [8.2, 8.3, vb.]
- MySQL Versiyonu: [8.0, 8.1, vb.]
- Docker: [Evet/Hayır]

## Ek Notlar
[Diğer açıklamalar varsa]
```

### Kaliteli Bug Raporu

✅ **İyi Rapor:**
- Başlık açık ve kısa
- Adımlar tekrarlanabilir
- Çevre bilgileri tamam
- Ekran görüntüsü var

❌ **Kötü Rapor:**
- "Çalışmıyor" gibi başlık
- Adımlar belirsiz
- Sistem bilgileri yok

---

## 💡 Özellik İsteme

### Issue Şablonu

```markdown
## Özellik Açıklaması
[Ne istiyorsunuz?]

## Neden Gerekli?
[Problem nedir? Neden bu özellik işe yarayacak?]

## Önerilen Çözüm
[Nasıl yapılmalı?]

## Alternatif Çözümler
[Başka yollar?]

## Ek Kontekst
[Diğer detaylar]
```

### İyi Özellik İsteği

✅ **Örnek:**
```markdown
## Rezervasyon Bildirimleri
Misafirler rezervasyon yapıldığında email almalı.

## Neden Gerekli
Misafireler rezervasyonlarının onaylandığını öğrenmezler.

## Önerilen Çözüm
- Yeni rezervasyon veya iptal edilince email gönder
- Email şablonları yöneticinin düzenleyebileceği şekilde
```

---

## 📐 Kod Standartları

### PHP Kod Stil Rehberi

```php
<?php
// PHP dosyaları <?php ile başlar

// 1. Sabitler BÜYÜK_HARFLE
define('DB_HOST', 'localhost');

// 2. Değişkenler camelCase
$userName = 'John Doe';

// 3. Fonksiyonlar snake_case (PHP convention)
function get_user_by_id($id) {
    // Kod...
}

// 4. Sınıflar PascalCase
class UserRepository {
    public function getUser($id) {
        // Kod
    }
}

// 5. Her zaman güvenlik kontrol edin
$safe_input = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');

// 6. Prepared statements kullanın
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// 7. Koşullu kontroller
if ($user_id > 0) {
    // Kod
} else {
    // Kod
}

// 8. Hata yönetimi
try {
    // Kod
} catch (Exception $e) {
    error_log($e->getMessage());
}
?>
```

### HTML/CSS Standartları

```html
<!-- 1. Semantic HTML kullanın -->
<header>...</header>
<main>...</main>
<footer>...</footer>

<!-- 2. Data attributes kullanın -->
<form method="POST" data-action="login">
    <input type="text" name="username" required>
</form>

<!-- 3. Accessibility kontrol edin -->
<img src="image.jpg" alt="Açıklayıcı metin">
<label for="email">Email:</label>
<input id="email" type="email">

<!-- 4. CSS classes semantic olmalı -->
<div class="user-profile"> <!-- ✓ İyi -->
<div class="container-1"> <!-- ✗ Kötü -->

<!-- 5. Responsive design kontrol et -->
@media (max-width: 768px) {
    /* Mobile styles */
}
```

### JavaScript Standartları

```javascript
// 1. const yı öncelikle kullanın, sonra let
const API_ENDPOINT = 'https://api.example.com';
let userCount = 0;

// 2. Arrow functions tercihen
const handleClick = () => {
    console.log('Clicked');
};

// 3. Template literals
const message = `Hello, ${userName}`;

// 4. Async/await
const fetchData = async () => {
    try {
        const response = await fetch(API_ENDPOINT);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error(error);
    }
};

// 5. Classes ES6 with
class User {
    constructor(name, email) {
        this.name = name;
        this.email = email;
    }
    
    getName() {
        return this.name;
    }
}

// 6. Comments yeterli olmalı
// Basit işlemler için comment gerekli değil
// Kompleks logik için açıkla
```

### Güvenlik Kontrolleri

```php
// XSS Koruması
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// SQL Injection Koruması
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

// CSRF Koruması
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Password Hashing
$hashed = password_hash($password, PASSWORD_BCRYPT);
password_verify($input_password, $hashed);

// Input Validasyon
filter_var($email, FILTER_VALIDATE_EMAIL);
preg_match('/^[0-9]+$/', $phone_number);
```

---

## 📝 Komit Mesajları

### Format

```
[TYPE] Başlık (maksimum 50 karakter)

İsteğe bağlı: Daha detaylı açıklama
```

### Türler (TYPE)

- `feat:` - Yeni özellik
- `fix:` - Hata düzeltme
- `docs:` - Dokumentasyon
- `style:` - Formatting (kod değişikliği yok)
- `refactor:` - Kod yeniden yazma
- `perf:` - Performans iyileştirme
- `test:` - Test ekleme/güncelleme
- `chore:` - Yapılandırma değişiklikleri

### Örnekler

```bash
git commit -m "feat: Email bildirimleri sistemini ekle"

git commit -m "fix: Login page 403 hatasını düzelt"

git commit -m "docs: API dokumentasyonunu güncelle"

git commit -m "refactor: Database connection kodunu iyileştir

- Connection pool implementasyonı
- Error handling iyileştirildi
- Performance %15 arttırıldı"
```

---

## 🔄 Pull Request Süreci

### Pull Request Oluşturma

1. Kendi fork'unuzda push edin:
```bash
git push origin feature/your-feature
```

2. GitHub'a gidin
3. "Compare & Pull Request" butonuna tıkla

### PR Şablonu

```markdown
## Açıklama
Bu PR şu değişiklikleri yapıyor:
- Değişiklik 1
- Değişiklik 2
- Değişiklik 3

## Türü
- [ ] Yeni özellik
- [ ] Hata düzeltme
- [ ] Dokumentasyon
- [ ] Breaking change

## Testing
- [ ] Manuel test yapıldı
- [ ] Lokal ortamda çalışıyor
- [ ] Hata yoktur

## Kontrol Listesi
- [ ] Kod standartlarını takip etti
- [ ] Tüm testler geçti
- [ ] Dokumentasyon güncellendi
- [ ] Komit mesajları açık

## İlgili Issues
Closes #123
```

### PR İnceleme Süreci

1. **Otomatik kontroller** - Testler çalışır
2. **İnceleme** - En az 1 maintainer inceler
3. **Düzenlemeler** - Verilen feedback'i uygulamanız isteyebilir
4. **Merge** - Onaylandıktan sonra merge edilir

### Communication

- Sorularınız varsa polite olun
- Feedback'i yapıcı algılayın
- Tartışmalar produktif olmalı

---

## 🎯 Katkı Türleri

### 1. Code Katkısı
- Yeni özellikler
- Hata düzeltmeler
- Performans iyileştirmeleri
- Refactoring

### 2. Documentation Katkısı
- README güncellemeleri
- API dokümantasyonu
- Türkçe/İngilizce çeviriler
- Örnekler ekleme

### 3. Community Katkısı
- Bug raporlama
- Özellik önerme
- Başkalarına yardım etme
- Toplulukla paylaşma

---

## 🏆 Katkı Editleri

Katkı yaptıktan sonra, aşağıdaki yerlemlerde listelenebilirsiniz:

1. **GitHub Contributors** - Otomatik
2. **README.md** - Sizin isteğinize
3. **CHANGELOG.md** - Önemli katkılar

---

## ❓ Sorularınız Varsa

- GitHub Discussions'da sor
- Email adres: [iletişim için bir mail ekleniniz]
- Discord: [Varsa sunucuyu ekleyin]

---

## 📚 Kaynak Linkler

- [GitHub Flow](https://guides.github.com/introduction/flow/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [Semantic Versioning](https://semver.org/)
- [PHP-FIG PSR Standards](https://www.php-fig.org/)

---

**Katkılarınız için teşekkür ederiz! 💝**

Son güncelleme: Şubat 2026
