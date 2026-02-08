# Güvenlik Politikası

## 🔒 Desteklenen Sürümler

| Sürüm | Destekli | End of Life |
|-------|----------|-------------|
| 1.0.x | ✅ Evet  | Feb 2027    |
| 0.9.x | ✅ Evet  |Feb 2026     |
| < 0.9 | ❌ Hayır | —           |

---

## 🚨 Güvenlik Açığı Raporlama

Eğer bir güvenlik açığı bulduysanız, **LÜTFEN bunu kamuya açık GitHub Issues'de bildirmeyin.** Bunun yerine aşağıdaki adımları takip edin:

### 1. Sorumlu Açıklama (Responsible Disclosure)

Açığı bulursanız:

```
📧 E-posta: security@masterstudio.local
Konu: [SECURITY] Açığın Kısa Açıklaması
```

### 2. Email İçeriği

```markdown
## Açığın Tanımı
[Açığın ne olduğunu açıklayın]

## Etki Düzeyi
- [ ] Kritik (Remote Code Execution)
- [ ] Yüksek (Authentication Bypass)
- [ ] Orta (Data Exposure)
- [ ] Düşük (Information Disclosure)

## Kanıt-of-Concept (PoC)
[Açığı göstermek için kod veya adımlar]

## İlgili Dosyalar
[Hangi dosyaların etkilendiğini belirtin]

## Durumunuz
- [ ] Açığı kimin bulduğu
- [ ] Bulunduğu tarih
- [ ] İletişim bilgileri
```

### 3. Beklenen Yanıt Süresi

- **Kritik:** 48 saat içinde yanıt
- **Yüksek:** 72 saat içinde yanıt
- **Orta:** 1 hafta içinde yanıt
- **Düşük:** 2 hafta içinde yanıt

### 4. İşlem

1. Açığı doğrulayız
2. Düzeltme geliştirilir
3. Test yapılır
4. Patch yayınlanır
5. Güvenlik Danışmanı yayınlandı
6. Bildirenin adı (isteğe bağlı) kreditlendirilir

---

## 🔐 Güvenlik Uygulamaları

### Backend (PHP)

#### 1. SQL Injection Koruması
```php
// ✗ KÖTÜ
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✓ İYİ
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
```

#### 2. XSS Koruması
```php
// ✗ KÖTÜ
echo "<h1>" . $_POST['title'] . "</h1>";

// ✓ İYİ
echo "<h1>" . htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') . "</h1>";
```

#### 3. Şifre Güvenliği
```php
// ✗ KÖTÜ
$hashed = md5($password);

// ✓ İYİ
$hashed = password_hash($password, PASSWORD_BCRYPT);
password_verify($input, $hashed);
```

#### 4. CSRF Koruması
```php
// Session tokens oluştur
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Formda ekle
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Doğrula
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}
```

#### 5. Error Handling
```php
// ✗ KÖTÜ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✓ İYİ
if ($conn->connect_error) {
    error_log("Database connection failed");
    die("Hata oluştu, lütfen daha sonra tekrar deneyin");
}
```

### Frontend (JavaScript)

#### 1. Input Validation
```javascript
// Formdan gelen veriyi validate et
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Kullan
if (!validateEmail(userEmail)) {
    alert('Geçerli email adı girin');
}
```

#### 2. DOM Sanitization
```javascript
// ✗ KÖTÜ
document.getElementById('content').innerHTML = userInput;

// ✓ İYİ
document.getElementById('content').textContent = userInput;

// Veya HTML gerekirse
const div = document.createElement('div');
div.textContent = userInput;
document.getElementById('content').appendChild(div);
```

#### 3. Secure Content
```javascript
// HTTPS kullan
const url = 'https://example.com/api/data';

// Sensitive data localStorage'e koyma
// ✗ localStorage.setItem('password', password);

// ✓ SessionStorage veya memory kullan
sessionStorage.setItem('token', token);
```

### Yapılandırma

#### 1. Apache Güvenliği
```apache
# Dosya erişimini kısıtla
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Direktif listelemeyi devre dışı bırak
<Directory /var/www/html/masterstudio>
    Options -Indexes
</Directory>

# .htaccess koruması
<Files ".htaccess">
    Order allow,deny
    Deny from all
</Files>
```

#### 2. PHP Güvenliği
```ini
; /etc/php/8.2/apache2/php.ini

; Tehlikeli fonksiyonları devre dışı bırak
disable_functions = exec,passthru,shell_exec,system,proc_open

; File uploads kısıtla
upload_max_filesize = 5M
post_max_size = 5M

; Session güvenliği
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict

; Display errors kapalı
display_errors = 0
log_errors = 1
```

#### 3. MySQL Güvenliği
```sql
-- Admin kullanıcı kısıt
GRANT ALL PRIVILEGES ON masterstudio_hotel.* 
TO 'hotel_user'@'localhost' 
IDENTIFIED BY 'strong_password';

-- Remote erişim kısıtla
CREATE USER 'hotel_user'@'localhost' IDENTIFIED BY '...';
-- NOT 'hotel_user'@'%'

-- Backup yapabilecek user (read-only)
CREATE USER 'backup_user'@'localhost' IDENTIFIED BY '...';
GRANT SELECT ON masterstudio_hotel.* TO 'backup_user'@'localhost';
```

---

## 📋 Güvenlik Kontrol Listesi

Deploying yapmadan önce kontrol edin:

- [ ] Database şifreleri güçlüdür (12+ karakter, mixed)
- [ ] Default admin kullanıcı adı/şifresi değiştirilmiş
- [ ] Error hatalar gösterilmiyor (production'da)
- [ ] HTTPS/SSL sertifikası kurulu
- [ ] Firewall kuralları ayarlanmış
- [ ] Regular backups yapılıyor
- [ ] Eski dosyalar/testler silinmiş
- [ ] Debug modları kapalı
- [ ] File permissions doğru (644 files, 755 dirs)
- [ ] Log dosyaları güvenli (sensitif veri yok)
- [ ] Rate limiting kurulu
- [ ] CSRF tokens aktif
- [ ] SQL injections filtrelendi
- [ ] XSS protections aktif

---

## 🛡️ Best Practices

### 1. Güvenli Kodlama

```php
// ✓ Hazırlanmış İfadeler
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// ✓ Input Validasyon
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new InvalidArgumentException('Invalid email');
}

// ✓ Çıktı Kodlaması
echo htmlSpecialChars($data, ENT_QUOTES, 'UTF-8');

// ✓ Güvenli Session İdleri
ini_set('session.name', 'masterstudio_session');
session_regenerate_id(true);
```

### 2. Authentication (Kimlik Doğrulama)

```php
// ✓ Bcrypt ile Şifre Hash
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// ✓ Password Doğrulama
if (password_verify($input_password, $stored_hash)) {
    // Login başarılı
}

// ✓ Rate Limiting
if (failed_login_attempts > 5) {
    sleep(2 ** attempt_number);
}
```

### 3. Authorization (Yem Kontrol)

```php
// ✓ Role-based Access
if ($_SESSION['role'] !== 'admin') {
    header("Location: /login");
    exit;
}

// ✓ Resource Ownership Check
$resource = get_resource($id);
if ($resource['user_id'] !== $_SESSION['user_id']) {
    die('Unauthorized');
}
```

### 4. Logging (Günlükleme)

```php
// ✓ Security Events Kaydı
error_log("Admin login attempt for user: " . htmlSpecialChars($username));
error_log("Failed login from IP: " . $_SERVER['REMOTE_ADDR']);
error_log("Unauthorized access attempt to /admin");

// ✗ Şifre veya Sensitive veriyi loglama
// error_log("Password: " . $password); // ✗ KÖTÜ
```

---

## 🔍 Tedbir Alınmış Güvenlik Sorunları

### v1.0.0
- Bcrypt password hashing uygulandı
- SQL injection prevention added
- XSS protection implemented
- CSRF token protection added

### v0.9.5
- Session security improved
- File upload validation added
- Output encoding standardized

---

## 📞 İletişim

**Security Team:** [Email eklenecek]
**PGP Key:** [Varsa eklenecek]

---

## Kaynaklar

- [OWASP Top 10](https://owasp.org/Top10/)
- [PHP Security](https://www.php.net/manual/en/security.php)
- [CWE/SANS Top 25](https://cwe.mitre.org/top25/)
- [Security.txt](https://securitytxt.org/)

---

**Güvenliğimiz hepimizin sorumluluğu. Teşekkur ederiz! 🙏**

Son güncelleme: Şubat 2026
